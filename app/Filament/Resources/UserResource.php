<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Users';
    
    protected static ?string $pluralModelLabel = 'Users';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Information')
                    ->description('Manage user account details')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->placeholder('John Doe')
                            ->columnSpan(1),
                        
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->placeholder('john@example.com')
                            ->prefixIcon('heroicon-o-envelope')
                            ->columnSpan(1)
                            ->helperText('This will be used for login and notifications'),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->revealable() // Built-in password visibility toggle
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->rule(Password::default())
                                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->placeholder('Enter password')
                                    ->helperText(fn (string $context): string => 
                                        $context === 'create' 
                                            ? 'Minimum 8 characters' 
                                            : 'Leave empty to keep current password'
                                    ),
                                
                                TextInput::make('password_confirmation')
                                    ->label('Confirm Password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->dehydrated(false)
                                    ->placeholder('Confirm password'),
                            ]),
                    ]),
                
                Section::make('Account Status')
                    ->description('Manage user account verification and status')
                    ->icon('heroicon-o-shield-check')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verified')
                            ->native(false)
                            ->displayFormat('M d, Y H:i')
                            ->placeholder('Not verified')
                            ->helperText('Set date/time to mark email as verified')
                            ->columnSpan(1),
                        
                        Select::make('role')
                            ->label('User Role')
                            ->options([
                                'admin' => 'Administrator',
                                'customer' => 'Customer',
                                'manager' => 'Manager',
                                'editor' => 'Editor',
                            ])
                            ->default('customer')
                            ->required()
                            ->native(false)
                            ->columnSpan(1)
                            ->helperText('Determines user permissions'),
                        
                        Toggle::make('is_active')
                            ->label('Active Account')
                            ->default(true)
                            ->helperText('Inactive users cannot log in')
                            ->columnSpan(1),
                        
                        Toggle::make('marketing_emails')
                            ->label('Marketing Emails')
                            ->default(true)
                            ->helperText('User wants to receive marketing emails')
                            ->columnSpan(1),
                    ]),
                
                Section::make('Profile Information')
                    ->description('Additional user profile details')
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Profile Avatar')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '16:9',
                                '4:3',
                                null,
                            ])
                            ->directory('avatars')
                            ->visibility('public')
                            ->maxSize(2048) // 2MB
                            ->columnSpanFull()
                            ->helperText('Upload a square image for best results (max 2MB)'),
                        
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->placeholder('+1 (555) 123-4567')
                            ->maxLength(20)
                            ->columnSpan(1),
                        
                        TextInput::make('company')
                            ->label('Company/Organization')
                            ->placeholder('Acme Inc.')
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        TextInput::make('job_title')
                            ->label('Job Title')
                            ->placeholder('Marketing Manager')
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        Forms\Components\Textarea::make('bio')
                            ->label('Bio')
                            ->placeholder('Tell us a little about yourself...')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->placeholder('Street address')
                            ->rows(2)
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Grid::make(3)
                            ->schema([
                                TextInput::make('city')
                                    ->label('City')
                                    ->placeholder('New York')
                                    ->maxLength(100),
                                
                                TextInput::make('state')
                                    ->label('State/Province')
                                    ->placeholder('NY')
                                    ->maxLength(100),
                                
                                TextInput::make('zip_code')
                                    ->label('ZIP/Postal Code')
                                    ->placeholder('10001')
                                    ->maxLength(20),
                                
                                Select::make('country')
                                    ->label('Country')
                                    ->options([
                                        'US' => 'United States',
                                        'CA' => 'Canada',
                                        'UK' => 'United Kingdom',
                                        'AU' => 'Australia',
                                        'DE' => 'Germany',
                                        'FR' => 'France',
                                        'ES' => 'Spain',
                                        'IT' => 'Italy',
                                        'JP' => 'Japan',
                                        'CN' => 'China',
                                        'IN' => 'India',
                                        'BR' => 'Brazil',
                                        'MX' => 'Mexico',
                                        'Other' => 'Other',
                                    ])
                                    ->searchable()
                                    ->native(false),
                            ]),
                    ]),
                
                Section::make('Preferences')
                    ->description('User preferences and settings')
                    ->icon('heroicon-o-cog')
                    ->columns(2)
                    ->schema([
                        Select::make('language')
                            ->label('Preferred Language')
                            ->options([
                                'en' => 'English',
                                'es' => 'Spanish',
                                'fr' => 'French',
                                'de' => 'German',
                                'zh' => 'Chinese',
                                'ar' => 'Arabic',
                                'hi' => 'Hindi',
                            ])
                            ->default('en')
                            ->native(false)
                            ->columnSpan(1),
                        
                        Select::make('timezone')
                            ->label('Timezone')
                            ->options([
                                'America/New_York' => 'Eastern Time',
                                'America/Chicago' => 'Central Time',
                                'America/Denver' => 'Mountain Time',
                                'America/Los_Angeles' => 'Pacific Time',
                                'America/Anchorage' => 'Alaska Time',
                                'America/Honolulu' => 'Hawaii Time',
                                'Europe/London' => 'London',
                                'Europe/Paris' => 'Paris',
                                'Asia/Tokyo' => 'Tokyo',
                                'Asia/Singapore' => 'Singapore',
                                'Australia/Sydney' => 'Sydney',
                            ])
                            ->searchable()
                            ->native(false)
                            ->columnSpan(1),
                    ]),
                
                Section::make('Metadata')
                    ->description('System information and timestamps')
                    ->icon('heroicon-o-information-circle')
                    ->columns(3)
                    ->schema([
                        DateTimePicker::make('created_at')
                            ->label('Account Created')
                            ->disabled()
                            ->dehydrated(false)
                            ->displayFormat('M d, Y H:i')
                            ->visibleOn('edit'),
                        
                        DateTimePicker::make('updated_at')
                            ->label('Last Updated')
                            ->disabled()
                            ->dehydrated(false)
                            ->displayFormat('M d, Y H:i')
                            ->visibleOn('edit'),
                        
                        DateTimePicker::make('last_login_at')
                            ->label('Last Login')
                            ->disabled()
                            ->dehydrated(false)
                            ->displayFormat('M d, Y H:i')
                            ->visibleOn('edit'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png'))
                    ->size(40),
                
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->email),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500)
                    ->icon('heroicon-o-envelope')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->alignCenter(),
                
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'manager' => 'warning',
                        'editor' => 'info',
                        'customer' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable()
                    ->searchable(),
                
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->alignCenter(),
                
                TextColumn::make('company')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('orders_count')
                    ->label('Orders')
                    ->counts('orders')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->alignCenter(),
                
                TextColumn::make('downloads_count')
                    ->label('Downloads')
                    ->counts('downloads')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Never'),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'admin' => 'Administrator',
                        'manager' => 'Manager',
                        'editor' => 'Editor',
                        'customer' => 'Customer',
                    ])
                    ->label('User Role')
                    ->searchable(),
                
                Filter::make('verified')
                    ->label('Email Verified')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                
                Filter::make('unverified')
                    ->label('Unverified Email')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
                
                Filter::make('active')
                    ->label('Active Accounts')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                
                Filter::make('inactive')
                    ->label('Inactive Accounts')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('is_active', false)),
                
                SelectFilter::make('country')
                    ->options([
                        'US' => 'United States',
                        'CA' => 'Canada',
                        'UK' => 'United Kingdom',
                        'AU' => 'Australia',
                        'DE' => 'Germany',
                        'FR' => 'France',
                    ])
                    ->label('Country')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View'),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete User')
                    ->modalDescription('Are you sure you want to delete this user? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete user'),
                Tables\Actions\Action::make('verify_email')
                    ->label('Verify Email')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update(['email_verified_at' => now()]);
                    })
                    ->visible(fn ($record) => is_null($record->email_verified_at)),
                
                Tables\Actions\Action::make('impersonate')
                    ->label('Login as User')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->action(fn ($record) => auth()->login($record))
                    ->visible(fn () => auth()->user()->isAdmin())
                    ->requiresConfirmation()
                    ->modalHeading('Impersonate User')
                    ->modalDescription('You will be logged in as this user. To return, log out and log back in as admin.')
                    ->modalSubmitActionLabel('Yes, impersonate'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Users')
                        ->modalDescription('Are you sure you want to delete the selected users? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete users'),
                    
                    Tables\Actions\BulkAction::make('verify_emails')
                        ->label('Verify Email')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if (is_null($record->email_verified_at)) {
                                    $record->update(['email_verified_at' => now()]);
                                }
                            }
                        }),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Accounts')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Accounts')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                    
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Selected')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('gray')
                        ->action(fn ($records) => static::exportUsers($records)),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Subscription Overview')
                    ->schema([
                        InfolistGrid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Customer')
                                    ->weight('bold')
                                    ->url(fn ($record) => route('filament.admin.resources.users.view', $record->user))
                                    ->color('primary'),
                                
                                TextEntry::make('name')
                                    ->label('Subscription')
                                    ->weight('bold'),
                                
                                TextEntry::make('price')
                                    ->label('Price')
                                    ->money('USD')
                                    ->weight('bold'),
                                
                                TextEntry::make('plan')
                                    ->label('Billing Plan')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'lifetime' => 'success',
                                        'yearly' => 'info',
                                        'quarterly' => 'warning',
                                        'monthly' => 'gray',
                                        default => 'gray',
                                    }),
                                
                                IconEntry::make('isActive')
                                    ->label('Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->getStateUsing(fn ($record) => $record->isActive()),
                                
                                TextEntry::make('order_id')
                                    ->label('Order ID')
                                    ->copyable()
                                    ->copyMessage('Order ID copied')
                                    ->placeholder('Not specified'),
                            ]),
                    ]),
                
                InfolistSection::make('Customer Information')
                    ->schema([
                        InfolistGrid::make(2)
                            ->schema([
                                TextEntry::make('user.email')
                                    ->label('Email')
                                    ->copyable()
                                    ->copyMessage('Email copied'),
                                TextEntry::make('user.created_at')
                                    ->label('Customer Since')
                                    ->date('M d, Y'),
                            ]),
                    ]),
                
                InfolistSection::make('Subscription Dates')
                    ->schema([
                        InfolistGrid::make(3)
                            ->schema([
                                TextEntry::make('starts_at')
                                    ->label('Started')
                                    ->dateTime('M d, Y - g:i A')
                                    ->badge()
                                    ->color('success'),
                                
                                TextEntry::make('trial_ends_at')
                                    ->label('Trial Ends')
                                    ->dateTime('M d, Y - g:i A')
                                    ->placeholder('No trial')
                                    ->badge()
                                    ->color('warning')
                                    ->visible(fn ($record) => $record->trial_ends_at),
                                
                                TextEntry::make('ends_at')
                                    ->label('Expires')
                                    ->dateTime('M d, Y - g:i A')
                                    ->placeholder('Never')
                                    ->badge()
                                    ->color(fn ($record) => 
                                        $record->ends_at && $record->ends_at->isPast() 
                                            ? 'danger' 
                                            : 'gray'
                                    ),
                                
                                TextEntry::make('next_billing_at')
                                    ->label('Next Billing')
                                    ->dateTime('M d, Y - g:i A')
                                    ->placeholder('N/A')
                                    ->visible(fn ($record) => $record->next_billing_at),
                                
                                TextEntry::make('cancelled_at')
                                    ->label('Cancelled')
                                    ->dateTime('M d, Y - g:i A')
                                    ->placeholder('Not cancelled')
                                    ->badge()
                                    ->color('danger')
                                    ->visible(fn ($record) => $record->cancelled_at),
                            ]),
                    ]),
                
                InfolistSection::make('Membership Package')
                    ->schema([
                        InfolistGrid::make(2)
                            ->schema([
                                TextEntry::make('membershipPackage.name')
                                    ->label('Package')
                                    ->weight('bold')
                                    ->url(fn ($record) => $record->membershipPackage 
                                        ? route('filament.admin.resources.membership-packages.view', $record->membershipPackage)
                                        : null
                                    )
                                    ->color('primary'),
                                
                                TextEntry::make('membershipPackage.description')
                                    ->label('Description')
                                    ->limit(100),
                                
                                TextEntry::make('membershipPackage.download_limit_per_month')
                                    ->label('Package Limit')
                                    ->formatStateUsing(fn ($state) => $state ? "{$state}/month" : 'Unlimited'),
                            ]),
                    ])
                    ->visible(fn ($record) => $record->membershipPackage),
                
                InfolistSection::make('Download Usage')
                    ->schema([
                        InfolistGrid::make(3)
                            ->schema([
                                TextEntry::make('downloads_used')
                                    ->label('Downloads Used')
                                    ->numeric()
                                    ->badge()
                                    ->color(fn ($record) => 
                                        $record->download_limit && $record->downloads_used >= $record->download_limit 
                                            ? 'danger' 
                                            : 'warning'
                                    ),
                                
                                TextEntry::make('download_limit')
                                    ->label('Download Limit')
                                    ->formatStateUsing(fn ($state) => $state ?? 'Unlimited')
                                    ->badge()
                                    ->color('info'),
                                
                                TextEntry::make('remainingDownloads')
                                    ->label('Remaining')
                                    ->getStateUsing(fn ($record) => $record->remainingDownloads() ?? 'Unlimited')
                                    ->badge()
                                    ->color(fn ($record) => 
                                        $record->download_limit && $record->remainingDownloads() <= 5 
                                            ? 'danger' 
                                            : 'success'
                                    ),
                            ]),
                    ]),
                
                InfolistSection::make('Additional Notes')
                    ->schema([
                        TextEntry::make('metadata')
                            ->label('Notes')
                            ->markdown()
                            ->placeholder('No additional notes')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->metadata)),
                
                InfolistSection::make('System Information')
                    ->schema([
                        InfolistGrid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime('M d, Y - g:i A'),
                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('M d, Y - g:i A'),
                                TextEntry::make('deleted_at')
                                    ->label('Deleted')
                                    ->dateTime('M d, Y - g:i A')
                                    ->badge()
                                    ->color('danger')
                                    ->visible(fn ($record) => $record->deleted_at),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relation managers for orders, downloads, etc.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    protected static function exportUsers($records)
    {
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['Name', 'Email', 'Role', 'Status', 'Verified', 'Joined', 'Orders', 'Total Spent']);
        
        foreach ($records as $record) {
            fputcsv($csv, [
                $record->name,
                $record->email,
                ucfirst($record->role ?? 'customer'),
                $record->is_active ? 'Active' : 'Inactive',
                $record->email_verified_at ? 'Yes' : 'No',
                $record->created_at?->format('Y-m-d'),
                $record->orders()->count(),
                '$' . number_format($record->orders()->where('payment_status', 'paid')->sum('total'), 2),
            ]);
        }
        
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'users-export-' . now()->format('Y-m-d') . '.csv');
    }
}