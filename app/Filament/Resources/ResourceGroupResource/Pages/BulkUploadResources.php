<?php

namespace App\Filament\Resources\ResourceGroupResource\Pages;

use App\Filament\Resources\ResourceGroupResource;
use App\Models\Category;
use App\Models\Resource;
use App\Models\ResourceGroup;
use App\Models\ServiceCategory;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class BulkUploadResources extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = ResourceGroupResource::class;

    protected static string $view = 'livewire.library.resource-group-resource.pages.bulk-upload-resources';

    public ResourceGroup $record;
    
    public ?array $data = [];
    
    public $uploadProgress = 0;
    public $processedFiles = [];
    public $totalFiles = 0;
    public $isProcessing = false;

    public function mount(): void
    {
        $this->form->fill([
            'group_id' => $this->record->id,
            'create_subgroups' => true,
            'default_published' => false,
            'default_price' => 0,
            'default_category_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Section::make('Bulk Upload Resources')
                    ->description("Upload multiple files at once to the group: {$this->record->full_path}")
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\Select::make('group_id')
                                    ->label('Target Group')
                                    ->options(function () {
                                        return ResourceGroup::orderBy('name')
                                            ->get()
                                            ->mapWithKeys(fn ($group) => [
                                                $group->id => $group->full_path
                                            ]);
                                    })
                                    ->required()
                                    ->default($this->record->id),

                                Components\Select::make('default_category_id')
                                    ->label('Default Category')
                                    ->options(function () {
                                        return Category::orderBy('name')
                                            ->get()
                                            ->mapWithKeys(fn ($cat) => [
                                                $cat->id => $cat->name
                                            ]);
                                    })
                                    ->required()
                                    ->helperText('Category to assign to all uploaded resources'),
                                    
                                Components\TextInput::make('default_price')
                                    ->label('Default Price (Ksh)')
                                    ->numeric()
                                    ->prefix('Ksh')
                                    ->default(0)
                                    ->helperText('Set 0 for free resources'),
                                    
                                Components\Toggle::make('default_published')
                                    ->label('Publish Immediately')
                                    ->default(false)
                                    ->helperText('Auto-publish uploaded resources'),
                                    
                                Components\Toggle::make('create_subgroups')
                                    ->label('Create Subgroups from Folders')
                                    ->default(true)
                                    ->helperText('Automatically create subgroups based on folder structure'),
                                    
                                Components\Toggle::make('extract_zip')
                                    ->label('Extract ZIP Archives')
                                    ->default(true)
                                    ->helperText('Automatically extract uploaded ZIP files'),
                            ]),
                            
                        Components\FileUpload::make('files')
                            ->label('Select Files or ZIP Archive')
                            ->multiple()
                            ->directory('temp-uploads')
                            ->preserveFilenames()
                            ->maxSize(512000) // 500MB
                            ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/epub+zip', 'image/*'])
                            ->helperText('You can upload multiple files or a ZIP archive containing your resources. Max 500MB total.')
                            ->required()
                            ->columnSpanFull(),
                            
                        Components\ViewField::make('upload_progress')
                            ->view('filament.forms.components.upload-progress')
                            ->visible(fn () => $this->isProcessing),
                    ])
            ])
            ->statePath('data');
    }

    public function processUpload()
    {
        $this->validate();
        
        $this->isProcessing = true;
        $this->uploadProgress = 0;
        $this->processedFiles = [];
        
        $data = $this->form->getState();
        $files = $data['files'] ?? [];
        $this->totalFiles = count($files);
        
        $allResourceFiles = [];
        
        // Process each uploaded file
        foreach ($files as $index => $filePath) {
            $fullPath = Storage::disk('public')->path($filePath);
            
            // Check if it's a ZIP file
            if ($data['extract_zip'] && pathinfo($fullPath, PATHINFO_EXTENSION) === 'zip') {
                $extractedFiles = $this->extractZip($fullPath, $data);
                $allResourceFiles = array_merge($allResourceFiles, $extractedFiles);
            } else {
                // Single file
                $allResourceFiles[] = [
                    'path' => $fullPath,
                    'relative_path' => basename($fullPath),
                    'group_id' => $data['group_id']
                ];
            }
            
            // Update progress
            $this->uploadProgress = round(($index + 1) / $this->totalFiles * 50); // First 50% for extraction
            $this->processedFiles[] = $filePath;
        }
        
        // Now create resources from all files
        $createdCount = $this->createResourcesFromFiles($allResourceFiles, $data);
        
        Notification::make()
            ->title('Bulk Upload Complete')
            ->body("Successfully created {$createdCount} resources.")
            ->success()
            ->send();
        
        $this->isProcessing = false;
        
        return redirect(ResourceGroupResource::getUrl('view', ['record' => $this->record]));
    }

    protected function extractZip($zipPath, $data)
    {
        $extractedFiles = [];
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath) === true) {
            $extractPath = Storage::disk('public')->path('extracted/' . uniqid());
            mkdir($extractPath, 0755, true);
            
            $zip->extractTo($extractPath);
            $zip->close();
            
            // Scan extracted directory
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($extractPath, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $relativePath = substr($file->getPathname(), strlen($extractPath) + 1);
                    
                    // Determine group based on folder structure
                    $groupId = $data['group_id'];
                    
                    if ($data['create_subgroups']) {
                        $pathParts = explode(DIRECTORY_SEPARATOR, dirname($relativePath));
                        if (count($pathParts) > 0 && $pathParts[0] !== '.') {
                            $groupId = $this->getOrCreateSubgroup($pathParts, $data['group_id']);
                        }
                    }
                    
                    $extractedFiles[] = [
                        'path' => $file->getPathname(),
                        'relative_path' => $relativePath,
                        'group_id' => $groupId
                    ];
                }
            }
        }
        
        return $extractedFiles;
    }

    protected function getOrCreateSubgroup(array $pathParts, $parentGroupId)
    {
        $currentParentId = $parentGroupId;
        
        foreach ($pathParts as $part) {
            if (empty($part) || $part === '.') continue;
            
            $group = ResourceGroup::firstOrCreate(
                [
                    'slug' => Str::slug($part),
                    'parent_id' => $currentParentId
                ],
                [
                    'name' => $part,
                    'is_active' => true
                ]
            );
            
            $currentParentId = $group->id;
        }
        
        return $currentParentId;
    }

    protected function createResourcesFromFiles($files, $data)
    {
        $createdCount = 0;
        $totalFiles = count($files);
        
        foreach ($files as $index => $fileInfo) {
            $file = $fileInfo['path'];
            $relativePath = $fileInfo['relative_path'];
            $groupId = $fileInfo['group_id'];
            
            if (!file_exists($file)) continue;
            
            $fileName = pathinfo($relativePath, PATHINFO_BASENAME);
            $title = pathinfo($relativePath, PATHINFO_FILENAME);
            $extension = pathinfo($relativePath, PATHINFO_EXTENSION);
            
            // Check if it's a valid resource file type
            $validExtensions = ['pdf', 'doc', 'docx', 'epub', 'zip', 'jpg', 'jpeg', 'png', 'gif'];
            if (!in_array(strtolower($extension), $validExtensions)) {
                continue; // Skip invalid file types
            }
            
            // Move file to permanent location
            $newPath = 'resources/' . $groupId . '/' . uniqid() . '_' . Str::slug($title) . '.' . $extension;
            $moved = Storage::disk('public')->put($newPath, file_get_contents($file));
            
            if ($moved) {
                // Create resource
                $resource = Resource::create([
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . uniqid(),
                    'description' => "Resource from folder: {$relativePath}",
                    'short_description' => "Uploaded from bulk import",
                    'group_id' => $groupId,
                    'category_id' => $data['default_category_id'],
                    'price' => $data['default_price'] ?? 0,
                    'is_published' => $data['default_published'] ?? false,
                    'delivery_type' => 'upload',
                    'file_path' => $newPath,
                    'file_name' => $fileName,
                    'file_size' => filesize($file),
                ]);
                
                $createdCount++;
                
                // Update progress (50-100%)
                $this->uploadProgress = 50 + round(($index + 1) / $totalFiles * 50);
            }
            
            // Clean up temp file
            if (strpos($file, 'extracted/') !== false) {
                unlink($file);
            }
        }
        
        // Clean up extraction directory
        $extractDirs = Storage::disk('public')->directories('extracted');
        foreach ($extractDirs as $dir) {
            Storage::disk('public')->deleteDirectory($dir);
        }
        
        // Clean up temp uploads
        foreach ($this->processedFiles as $tempFile) {
            Storage::disk('public')->delete($tempFile);
        }
        
        return $createdCount;
    }

    protected function getFormActions(): array
    {
        return [
            Components\Actions\Action::make('processUpload')
                ->label('Process Upload')
                ->action('processUpload')
                ->color('success')
                ->icon('heroicon-o-cloud-arrow-up')
                ->disabled(fn () => $this->isProcessing),
        ];
    }
}