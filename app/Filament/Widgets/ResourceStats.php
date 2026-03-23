<?php

namespace App\Filament\Widgets;

use App\Models\DownloadTracker;
use App\Models\Resource;
use App\Models\Order;
use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Carbon\Carbon;

class ResourceStats extends BaseWidget
{
    use InteractsWithPageFilters;
    
    protected function getStats(): array
    {
        $period = $this->filters['period'] ?? 'today';
        
        $dateFilter = $this->getDateFilter($period);
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        
        return [
            Stat::make('Total Resources', $this->getFilteredCount(Resource::class, $dateFilter, $startDate, $endDate))
                ->description('Active resources')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),
                
            Stat::make('Published', $this->getFilteredCount(Resource::where('is_published', true), $dateFilter, $startDate, $endDate))
                ->description($this->getFilteredCount(Resource::where('is_published', false), $dateFilter, $startDate, $endDate) . ' drafts')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
                
            Stat::make('Total Downloads', $this->getFilteredSum(DownloadTracker::class, 'downloads', $dateFilter, $startDate, $endDate))
                ->description($period === 'all' ? 'All time' : ucfirst(str_replace('_', ' ', $period)))
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('warning'),
                
            Stat::make('Orders', 'Ksh ' . number_format(
                $this->getFilteredSum(Order::where('payment_status', 'paid'), 'total', $dateFilter, $startDate, $endDate,'updated_at'), 
                2
            ))
                ->description('From ' . $this->getFilteredCount(Order::where('payment_status', 'paid'), $dateFilter, $startDate, $endDate,'updated_at') . ' sales')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Subscriptions', 'Ksh ' . number_format(
                $this->getFilteredSum(Subscription::class, 'price', $dateFilter, $startDate, $endDate), 
                2
            ))
                ->description('From ' . $this->getFilteredCount(Subscription::class, $dateFilter, $startDate, $endDate))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }    
    
    /**
     * Get the date filter based on the selected period
     */
    protected function getDateFilter(string $period): ?array
    {
        $now = Carbon::now();
        
        return match($period) {
            'today' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'yesterday' => [
                'start' => $now->copy()->subDay()->startOfDay(),
                'end' => $now->copy()->subDay()->endOfDay(),
            ],
            'this_week' => [
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
            ],
            'last_week' => [
                'start' => $now->copy()->subWeek()->startOfWeek(),
                'end' => $now->copy()->subWeek()->endOfWeek(),
            ],
            'this_month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ],
            'this_year' => [
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfYear(),
            ],
            'custom', 
            'all' => null,
            default => null,
        };
    }
    
    /**
     * Apply date filter to a query builder or model
     */
    protected function applyDateFilter($query, ?array $dateFilter, $startDate = null, $endDate = null,$dateCol='created_at')
    {
        // Handle custom date range
        if ($startDate && $endDate) {
            return $query->whereBetween($dateCol, [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }
        
        // Handle predefined periods
        if ($dateFilter) {
            return $query->whereBetween($dateCol, [$dateFilter['start'], $dateFilter['end']]);
        }
        
        return $query;
    }
    
    /**
     * Get filtered count
     */
    protected function getFilteredCount($model, ?array $dateFilter, $startDate = null, $endDate = null,$dateCol='created_at'): int
    {
        if (is_string($model)) {
            $query = $model::query();
        } else {
            $query = $model;
        }
        
        return $this->applyDateFilter($query, $dateFilter, $startDate, $endDate,$dateCol)->count();
    }
    
    /**
     * Get filtered sum
     */
    protected function getFilteredSum($model, string $column, ?array $dateFilter, $startDate = null, $endDate = null, $dateCol='created_at'): float
    {
        if (is_string($model)) {
            $query = $model::query();
        } else {
            $query = $model;
        }
        
        return $this->applyDateFilter($query, $dateFilter, $startDate, $endDate,$dateCol)->sum($column) ?? 0;
    }
    
    /**
     * Get the filters that can be applied to the widget
     */
    protected function getFilters(): ?array
    {
        return [
            'period' => [
                'label' => 'Period',
                'options' => [
                    'all' => 'All Time',
                    'today' => 'Today',
                    'yesterday' => 'Yesterday',
                    'this_week' => 'This Week',
                    'last_week' => 'Last Week',
                    'this_month' => 'This Month',
                    'this_year' => 'This Year',
                    'custom' => 'Custom Range',
                ],
                'default' => 'all',
            ],
        ];
    }
}