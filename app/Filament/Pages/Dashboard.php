<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;
    
    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('period')
                    ->label('Period')
                    ->options([
                        'all' => 'All Time',
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'this_week' => 'This Week',
                        'last_week' => 'Last Week',
                        'this_month' => 'This Month',
                        'this_year' => 'This Year',
                        'custom' => 'Custom Range',
                    ])
                    ->default('today')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state !== 'custom') {
                            $set('startDate', null);
                            $set('endDate', null);
                        }
                    }),
                    
                DatePicker::make('startDate')
                    ->label('Start Date')
                    ->visible(fn (callable $get) => $get('period') === 'custom')
                    ->required(fn (callable $get) => $get('period') === 'custom'),
                    
                DatePicker::make('endDate')
                    ->label('End Date')
                    ->visible(fn (callable $get) => $get('period') === 'custom')
                    ->required(fn (callable $get) => $get('period') === 'custom')
                    ->afterOrEqual('startDate'),
            ])
            ->columns(3);
    }
}