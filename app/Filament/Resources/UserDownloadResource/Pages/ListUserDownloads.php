<?php

namespace App\Filament\Resources\UserDownloadResource\Pages;

use App\Filament\Resources\UserDownloadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUserDownloads extends ListRecords
{
    protected static string $resource = UserDownloadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export CSV')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(fn () => $this->exportDownloads()),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'today' => Tab::make('Today')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('downloaded_at', today())),
            'yesterday' => Tab::make('Yesterday')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('downloaded_at', today()->subDay())),
            'this_week' => Tab::make('This Week')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('downloaded_at', [now()->startOfWeek(), now()->endOfWeek()])),
        ];
    }

    protected function exportDownloads()
    {
        $downloads = $this->getFilteredTableQuery()->get();
        
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['Date', 'Customer', 'Email', 'Resource', 'Access Type', 'Amount', 'Download Count']);
        
        foreach ($downloads as $download) {
            fputcsv($csv, [
                $download->downloaded_at?->format('Y-m-d H:i:s'),
                $download->user?->name,
                $download->user?->email,
                $download->resource?->title,
                $download->access_type,
                $download->amount_paid ? '$' . number_format($download->amount_paid, 2) : '-',
                $download->download_count,
            ]);
        }
        
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'downloads-' . now()->format('Y-m-d') . '.csv');
    }
}