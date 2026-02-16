<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use App\Models\MembershipPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    
    protected static ?string $navigationGroup = 'Library Resources';
    
    protected static ?string $navigationLabel = 'Subscriptions';
    
    protected static ?string $pluralModelLabel = 'Subscriptions';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Subscription Details')
                    ->description('Customer and package information')
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(),
                                TextInput::make('password')
                                    ->password()
                                    ->required()
                                    ->default('password')
                                    ->revealable(),
                            ])
                            ->columnSpan(1),
                        
                        Select::make('membership_package_id')
                            ->label('Membership Package')
                            ->relationship('membershipPackage', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $package = MembershipPackage::find($state);
                                    if ($package) {
                                        $set('name', $package->name);
                                        $plan = $get('plan') ?? 'monthly';
                                        $priceField = "price_{$plan}";
                                        $set('price', $package->$priceField ?? 0);
                                        $set('download_limit', $package->download_limit_per_month);
                                    }
                                }
                            })
                            ->columnSpan(1),
                        
                        TextInput::make('name')
                            ->label('Subscription Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        Select::make('plan')
                            ->label('Billing Plan')
                            ->options([
                                'monthly' => 'Monthly',
                                'yearly' => 'Yearly',
                                'quarterly' => 'Quarterly',
                                'lifetime' => 'Lifetime',
                            ])
                            ->default('monthly')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $packageId = $get('membership_package_id');
                                if ($packageId) {
                                    $package = MembershipPackage::find($packageId);
                                    if ($package) {
                                        $priceField = "price_{$state}";
                                        $set('price', $package->$priceField ?? 0);
                                    }
                                }
                            })
                            ->columnSpan(1),
                        
                        TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->step(0.01)
                            ->columnSpan(1),
                        
                        TextInput::make('order_id')
                            ->label('Order ID / Invoice #')
                            ->placeholder('ORD-12345')
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->columnSpan(1),
                        
                        Select::make('type')
                            ->label('Subscription Type')
                            ->options([
                                'membership' => 'Membership',
                                'addon' => 'Add-on',
                                'gift' => 'Gift',
                            ])
                            ->default('membership')
                            ->required()
                            ->columnSpan(1),
                    ]),
                
                Section::make('Dates & Billing')
                    ->description('Subscription timeline and billing dates')
                    ->icon('heroicon-o-calendar')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label('Start Date')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->displayFormat('M d, Y H:i')
                            ->columnSpan(1),
                        
                        DateTimePicker::make('trial_ends_at')
                            ->label('Trial Ends')
                            ->native(false)
                            ->displayFormat('M d, Y H:i')
                            ->nullable()
                            ->helperText('Leave empty if no trial')
                            ->columnSpan(1),
                        
                        DateTimePicker::make('ends_at')
                            ->label('End Date')
                            ->native(false)
                            ->displayFormat('M d, Y H:i')
                            ->nullable()
                            ->helperText('When subscription expires')
                            ->columnSpan(1),
                        
                        DateTimePicker::make('next_billing_at')
                            ->label('Next Billing Date')
                            ->native(false)
                            ->displayFormat('M d, Y H:i')
                            ->nullable()
                            ->columnSpan(1),
                        
                        DateTimePicker::make('cancelled_at')
                            ->label('Cancelled At')
                            ->native(false)
                            ->displayFormat('M d, Y H:i')
                            ->nullable()
                            ->disabled()
                            ->columnSpan(1),
                    ]),
                
                Section::make('Download Limits')
                    ->description('Usage tracking and restrictions')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->columns(2)
                    ->schema([
                        TextInput::make('download_limit')
                            ->label('Monthly Download Limit')
                            ->numeric()
                            ->nullable()
                            ->helperText('Leave empty for unlimited')
                            ->columnSpan(1),
                        
                        TextInput::make('downloads_used')
                            ->label('Downloads Used This Month')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(1),
                    ]),
                
                Section::make('Additional Information')
                    ->description('Notes and metadata')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Textarea::make('metadata')
                            ->label('Notes / Metadata')
                            ->rows(3)
                            ->nullable()
                            ->placeholder('Any additional information about this subscription...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(['users.name', 'users.email'])
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->user?->email)
                    ->wrap(),
                
                TextColumn::make('name')
                    ->label('Subscription')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->wrap(),
                
                BadgeColumn::make('plan')
                    ->label('Plan')
                    ->colors([
                        'success' => 'lifetime',
                        'info' => 'yearly',
                        'warning' => 'quarterly',
                        'gray' => 'monthly',
                    ])
                    ->icons([
                        'lifetime' => 'heroicon-o-sparkles',
                        'yearly' => 'heroicon-o-calendar',
                        'quarterly' => 'heroicon-o-calendar-days',
                        'monthly' => 'heroicon-o-clock',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                
                TextColumn::make('price')
                    ->label('Price')
                    ->money('Ksh')
                    ->sortable()
                    ->weight('bold'),
                
                BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn ($record): string => 
                        $record->cancelled_at ? 'cancelled' : 
                        ($record->ends_at && $record->ends_at->isPast() ? 'expired' : 
                        ($record->onTrial() ? 'trial' : 'active'))
                    )
                    ->colors([
                        'success' => 'active',
                        'warning' => 'trial',
                        'danger' => 'cancelled',
                        'gray' => 'expired',
                    ])
                    ->icons([
                        'active' => 'heroicon-o-check-circle',
                        'trial' => 'heroicon-o-clock',
                        'cancelled' => 'heroicon-o-x-circle',
                        'expired' => 'heroicon-o-calendar',
                    ]),
                
                TextColumn::make('downloads_used')
                    ->label('Downloads')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => 
                        $record->download_limit 
                            ? "{$state} / {$record->download_limit}"
                            : "{$state} (unlimited)"
                    )
                    ->badge()
                    ->color(fn ($record) => 
                        $record->download_limit && $record->downloads_used >= $record->download_limit 
                            ? 'danger' 
                            : ($record->downloads_used > 0 ? 'warning' : 'gray')
                    ),
                
                TextColumn::make('ends_at')
                    ->label('Expires')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->color(fn ($record) => 
                        $record->ends_at && $record->ends_at->isPast() 
                            ? 'danger' 
                            : ($record->ends_at ? 'warning' : 'success')
                    )
                    ->formatStateUsing(fn ($state, $record) => 
                        $record->plan === 'lifetime' 
                            ? 'Lifetime' 
                            : ($state ? $state->format('M d, Y') : 'Never')
                    ),
                
                TextColumn::make('order_id')
                    ->label('Order #')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable()
                    ->copyMessage('Order ID copied'),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('plan')
                    ->label('Billing Plan')
                    ->options([
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                        'quarterly' => 'Quarterly',
                        'lifetime' => 'Lifetime',
                    ]),
                
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'trial' => 'Trial',
                        'cancelled' => 'Cancelled',
                        'expired' => 'Expired',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (!$value) return $query;
                        
                        return match($value) {
                            'active' => $query->whereNull('cancelled_at')
                                ->where(function ($q) {
                                    $q->whereNull('ends_at')
                                        ->orWhere('ends_at', '>', now());
                                }),
                            'trial' => $query->whereNotNull('trial_ends_at')
                                ->where('trial_ends_at', '>', now()),
                            'cancelled' => $query->whereNotNull('cancelled_at'),
                            'expired' => $query->whereNotNull('ends_at')
                                ->where('ends_at', '<', now()),
                            default => $query,
                        };
                    }),
                
                SelectFilter::make('user_id')
                    ->label('Customer')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                Filter::make('download_limit_reached')
                    ->label('Download Limit Reached')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->whereColumn('downloads_used', '>=', 'download_limit')
                        ->whereNotNull('download_limit')),
                
                Filter::make('needs_renewal')
                    ->label('Needs Renewal')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->whereNotNull('ends_at')
                        ->where('ends_at', '<', now()->addDays(7))
                        ->where('ends_at', '>', now())
                        ->whereNull('cancelled_at')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('View'),
                        
                    Tables\Actions\EditAction::make()
                        ->label('Edit'),
                        
                    Tables\Actions\Action::make('cancel')
                        ->label('Cancel Subscription')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Cancel Subscription')
                        ->modalDescription('Are you sure you want to cancel this subscription? The user will lose access.')
                        ->modalSubmitActionLabel('Yes, cancel subscription')
                        ->action(function ($record) {
                            $record->update([
                                'cancelled_at' => now(),
                                'ends_at' => $record->ends_at ?? now(),
                            ]);
                        })
                        ->visible(fn ($record) => $record->isActive() && !$record->isCancelled()),
                    
                    Tables\Actions\Action::make('reactivate')
                        ->label('Reactivate Subscription')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Reactivate Subscription')
                        ->modalDescription('Restore this cancelled subscription?')
                        ->modalSubmitActionLabel('Yes, reactivate')
                        ->action(function ($record) {
                            $record->update([
                                'cancelled_at' => null,
                                'ends_at' => $record->plan === 'lifetime' 
                                    ? null 
                                    : ($record->ends_at ?: now()->addYear()),
                            ]);
                        })
                        ->visible(fn ($record) => $record->isCancelled()),
                        
                    Tables\Actions\DeleteAction::make()
                        ->label('Delete')
                        ->requiresConfirmation()
                        ->modalHeading('Delete Subscription')
                        ->modalDescription('Are you sure you want to delete this subscription? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete'),
                ])
                ->label('Actions')
                ->icon('heroicon-o-chevron-down')
                ->color('primary')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('cancel_bulk')
                        ->label('Cancel Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Cancel Subscriptions')
                        ->modalDescription('Are you sure you want to cancel the selected subscriptions?')
                        ->modalSubmitActionLabel('Yes, cancel all')
                        ->action(fn ($records) => $records->each->update([
                            'cancelled_at' => now(),
                            'ends_at' => now(),
                        ])),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export CSV')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('gray')
                        ->action(fn ($records) => static::exportSubscriptions($records)),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('30s');
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
                                    ->money('Ksh')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }

    protected static function exportSubscriptions($records)
    {
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, [
            'Customer', 'Email', 'Subscription', 'Plan', 'Price', 
            'Status', 'Start Date', 'End Date', 'Downloads Used', 'Download Limit', 'Order ID'
        ]);
        
        foreach ($records as $record) {
            fputcsv($csv, [
                $record->user?->name,
                $record->user?->email,
                $record->name,
                ucfirst($record->plan ?? ''),
                $record->price,
                $record->cancelled_at ? 'Cancelled' : ($record->ends_at && $record->ends_at->isPast() ? 'Expired' : 'Active'),
                $record->starts_at?->format('Y-m-d'),
                $record->ends_at?->format('Y-m-d'),
                $record->downloads_used,
                $record->download_limit ?? 'Unlimited',
                $record->order_id,
            ]);
        }
        
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'subscriptions-export-' . now()->format('Y-m-d') . '.csv');
    }
}