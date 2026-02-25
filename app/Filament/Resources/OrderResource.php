<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource as FilamentResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\KeyValueEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class OrderResource extends FilamentResource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationGroup = 'Library Resources';
    
    protected static ?string $navigationLabel = 'Orders';
    
    protected static ?string $pluralModelLabel = 'Orders';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'order_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Order Information')
                    ->description('Customer and resource details')
                    ->icon('heroicon-o-shopping-bag')
                    ->columns(2)
                    ->schema([
                        TextInput::make('order_number')
                            ->label('Order Number')
                            ->default(fn () => 'ORD-' . strtoupper(Str::random(10)))
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(Order::class, 'order_number', ignoreRecord: true)
                            ->columnSpan(1),
                        
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
                        
                        Select::make('resource_id')
                            ->label('Resource')
                            ->relationship('resource', 'title')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $resource = Resource::find($state);
                                    if ($resource) {
                                        $set('subtotal', $resource->price);
                                        $set('total', $resource->price);
                                        $set('total_items', 1);
                                    }
                                }
                            })
                            ->columnSpan(1),
                        
                        ToggleButtons::make('order_status')
                            ->label('Order Status')
                            ->options([
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->colors([
                                'processing' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                            ])
                            ->icons([
                                'processing' => 'heroicon-o-arrow-path',
                                'completed' => 'heroicon-o-check-circle',
                                'cancelled' => 'heroicon-o-x-circle',
                            ])
                            ->default('processing')
                            ->required()
                            ->columnSpan(1),
                        
                        TextInput::make('total_items')
                            ->label('Quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $subtotal = $get('subtotal') ?? 0;
                                $tax = $get('tax') ?? 0;
                                $set('total', ($subtotal * $state) + $tax);
                            })
                            ->columnSpan(1),
                    ]),
                
                Section::make('Payment Details')
                    ->description('Payment method and transaction information')
                    ->icon('heroicon-o-credit-card')
                    ->columns(2)
                    ->schema([
                        ToggleButtons::make('payment_status')
                            ->label('Payment Status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->colors([
                                'pending' => 'warning',
                                'paid' => 'success',
                                'failed' => 'danger',
                                'refunded' => 'gray',
                            ])
                            ->icons([
                                'pending' => 'heroicon-o-clock',
                                'paid' => 'heroicon-o-check-circle',
                                'failed' => 'heroicon-o-x-circle',
                                'refunded' => 'heroicon-o-arrow-uturn-left',
                            ])
                            ->default('pending')
                            ->required()
                            ->live()
                            ->columnSpan(1),
                        
                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'mpesa' => 'M-Pesa',
                                'stripe' => 'Stripe',
                                'paypal' => 'PayPal',
                                'bank_transfer' => 'Bank Transfer',
                                'cash' => 'Cash',
                                'admin' => 'Admin Created',
                            ])
                            ->searchable()
                            ->required()
                            ->columnSpan(1),
                        
                        TextInput::make('reference')
                            ->label('Transaction Reference')
                            ->placeholder('MPESA12345, ch_xxx, etc.')
                            ->columnSpan(1),
                        
                        DateTimePicker::make('paid_at')
                            ->label('Paid Date')
                            ->native(false)
                            ->displayFormat('M d, Y H:i')
                            ->visible(fn (Forms\Get $get): bool => $get('payment_status') === 'paid')
                            ->required(fn (Forms\Get $get): bool => $get('payment_status') === 'paid')
                            ->columnSpan(1),
                    ]),
                
                Section::make('Order Totals')
                    ->description('Financial summary')
                    ->icon('heroicon-o-calculator')
                    ->columns(3)
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Unit Price')
                            ->numeric()
                            ->prefix('Ksh ')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $quantity = $get('total_items') ?? 1;
                                $tax = $get('tax') ?? 0;
                                $set('total', ($state * $quantity) + $tax);
                            })
                            ->columnSpan(1),
                        
                        TextInput::make('tax')
                            ->label('Tax')
                            ->numeric()
                            ->prefix('Ksh ')
                            ->default(0)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $subtotal = $get('subtotal') ?? 0;
                                $quantity = $get('total_items') ?? 1;
                                $set('total', ($subtotal * $quantity) + $state);
                            })
                            ->columnSpan(1),
                        
                        TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->prefix('Ksh ')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),
                    ]),
                
                Section::make('Additional Information')
                    ->schema([
                        KeyValue::make('order_data')
                        ->label('Order Data Details')
                        // Convert nested array to dot notation: ['user' => ['name' => 'John']] becomes ['user.name' => 'John']
                        ->formatStateUsing(fn ($state) => \Illuminate\Support\Arr::dot($state ?? []))
                        // Optional: If you need to turn it back into a nested array before saving
                        //->dehydrateStateUsing(fn ($state) => \Illuminate\Support\Arr::undot($state ?? []))
                        ->columnSpanFull()
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(['users.name', 'users.email'])
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->user?->email),
                
                TextColumn::make('item_display')
                    ->label('Item')
                    ->formatStateUsing(function ($record) {
                        if ($record->resource && !empty($record->resource->title)) {
                            return $record->resource->title;
                        }
                        
                        if (!empty($record->order_data['package_name'])) {
                            return $record->order_data['package_name'];
                        }
                        
                        return '—';
                    })
                    ->limit(30)
                    ->badge()
                    ->color(fn ($record) => $record->resource ? 'primary' : 'success')
                    ->icon(fn ($record) => $record->resource ? 'heroicon-o-document' : 'heroicon-o-star')
                    ->suffix(fn ($record) => !$record->resource && !empty($record->order_data['package_name']) ? ' (Membership)' : '')
                    ->url(fn ($record) => $record->resource 
                        ? route('filament.admin.resources.resources.view', $record->resource)
                        : (!empty($record->order_data['package_id']) 
                            ? route('filament.admin.resources.membership-packages.view', $record->order_data['package_id'])
                            : null
                        )
                    )
                    ->wrap(),
                
                /*TextColumn::make('total_items')
                    ->label('Qty')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),*/
                
                TextColumn::make('total')
                    ->label('Total')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->weight('bold')
                    ->color('info'),
                
                BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'gray' => 'refunded',
                    ])
                    ->icons([
                        'pending' => 'heroicon-o-clock',
                        'paid' => 'heroicon-o-check-circle',
                        'failed' => 'heroicon-o-x-circle',
                        'refunded' => 'heroicon-o-arrow-uturn-left',
                    ])
                    ->sortable(),
                
                BadgeColumn::make('order_status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'processing',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->icons([
                        'processing' => 'heroicon-o-arrow-path',
                        'completed' => 'heroicon-o-check-circle',
                        'cancelled' => 'heroicon-o-x-circle',
                    ])
                    ->sortable(),
                
                TextColumn::make('paid_at')
                    ->label('Paid Date')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Ordered')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
                
                SelectFilter::make('order_status')
                    ->label('Order Status')
                    ->options([
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                
                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'mpesa' => 'M-Pesa',
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'bank_transfer' => 'Bank Transfer',
                    ]),
                
                SelectFilter::make('user_id')
                    ->label('Customer')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('resource_id')
                    ->label('Resource')
                    ->relationship('resource', 'title')
                    ->searchable()
                    ->preload(),
                
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('ordered_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('ordered_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['ordered_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['ordered_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->columns(2)
                    ->columnSpan(2),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('View Details'),
                    
                    Tables\Actions\EditAction::make()
                        ->label('Edit Order'),
                    
                    Tables\Actions\Action::make('mark_as_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->action(function ($record) {
                            $record->update([
                                'payment_status' => 'paid',
                                'paid_at' => now(),
                            ]);
                            self::makeDownloadable($record);
                        })
                        ->visible(fn ($record) => $record->payment_status !== 'paid'),
                    
                    Tables\Actions\Action::make('mark_as_completed')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check-badge')
                        ->action(function ($record) {
                            $record->update([
                                'order_status' => 'completed',
                            ]);
                            self::makeDownloadable($record);
                        })
                        ->visible(fn ($record) => $record->order_status !== 'completed'),
                    
                    Tables\Actions\DeleteAction::make()->label('Delete Order'),
                ])
                ->label('Actions')
                ->icon('heroicon-o-chevron-down')
                ->color('primary')
                ->button()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('update_payment_status')
                        ->label('Update Payment Status')
                        ->icon('heroicon-o-credit-card')
                        ->form([
                            Select::make('payment_status')
                                ->label('Payment Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'paid' => 'Paid',
                                    'failed' => 'Failed',
                                    'refunded' => 'Refunded',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $updateData = ['payment_status' => $data['payment_status']];
                                if ($data['payment_status'] === 'paid') {
                                    $updateData['paid_at'] = now();
                                }
                                $record->update($updateData);
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Order Summary')
                    ->schema([
                        InfolistGrid::make(3)
                            ->schema([
                                TextEntry::make('order_number')
                                    ->label('Order Number')
                                    ->weight('bold')
                                    ->color('primary')
                                    ->copyable()
                                    ->size(TextEntry\TextEntrySize::Large),
                                
                                TextEntry::make('created_at')
                                    ->label('Order Date')
                                    ->dateTime('F j, Y - g:i A')
                                    ->weight('bold'),
                                
                                TextEntry::make('order_status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match($state) {
                                        'processing' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),
                
                InfolistSection::make('Customer Information')
                    ->schema([
                        InfolistGrid::make(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Name')
                                    ->weight('bold')
                                    ->url(fn ($record) => route('filament.admin.resources.users.view', $record->user))
                                    ->color('primary'),
                                
                                TextEntry::make('user.email')
                                    ->label('Email')
                                    ->copyable()
                                    ->copyMessage('Email copied'),
                                
                                TextEntry::make('user.created_at')
                                    ->label('Customer Since')
                                    ->date('M d, Y'),
                            ]),
                    ]),
                
                InfolistSection::make('Resource Purchased')
                    ->schema([
                        InfolistGrid::make(2)
                            ->schema([
                                TextEntry::make('resource.title')
                                    ->label('Resource')
                                    ->weight('bold')
                                    ->url(fn ($record) => $record->resource 
                                        ? route('filament.admin.resources.resources.view', $record->resource) 
                                        : null
                                    )
                                    ->color('primary'),
                                
                                TextEntry::make('resource.price')
                                    ->label('Unit Price')
                                    ->money('Ksh'),
                                
                                TextEntry::make('total_items')
                                    ->label('Quantity')
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ]),
                
                InfolistSection::make('Payment Information')
                    ->schema([
                        InfolistGrid::make(3)
                            ->schema([
                                TextEntry::make('payment_status')
                                    ->label('Payment Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match($state) {
                                        'pending' => 'warning',
                                        'paid' => 'success',
                                        'failed' => 'danger',
                                        'refunded' => 'gray',
                                        default => 'gray',
                                    }),
                                
                                TextEntry::make('payment_method')
                                    ->label('Payment Method')
                                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                                
                                TextEntry::make('reference')
                                    ->label('Transaction Reference')
                                    ->prose() 
                                    ->markdown()
                                    ->copyable()
                                    ->copyMessage('Reference copied')
                                    ->placeholder('N/A')
                                    ->columnSpanFull(),
                                
                                TextEntry::make('paid_at')
                                    ->label('Paid Date')
                                    ->dateTime('F j, Y - g:i A')
                                    ->placeholder('Not paid yet')
                                    ->visible(fn ($record) => $record->paid_at),
                            ]),
                    ]),
                
                InfolistSection::make('Order Totals')
                    ->schema([
                        InfolistGrid::make(3)
                            ->schema([
                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->money('Ksh'),
                                
                                TextEntry::make('tax')
                                    ->label('Tax')
                                    ->money('Ksh'),
                                
                                TextEntry::make('total')
                                    ->label('Total')
                                    ->money('Ksh')
                                    ->weight('bold')
                                    ->size(TextEntry\TextEntrySize::Large),
                            ]),
                    ]),
                
                InfolistSection::make('Additional Notes')
                    ->schema([
                        KeyValueEntry::make('flattened_order_data')
                            ->label('Order Information')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->order_data)),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    static function makeDownloadable($record): void
    {
        if ($record->payment_status === 'paid') {
            $exists = $record->user->downloads()
                ->where('order_id', $record->id)
                ->exists();
                
            if (!$exists && $record->resource_id) {
                $record->user->downloads()->create([
                    'resource_id' => $record->resource_id,
                    'order_id' => $record->id,
                    'access_type' => 'one_time_purchase',
                    'amount_paid' => $record->total,
                    'downloaded_at' => null,
                    'download_count' => 0,
                ]);
            }
        }
    }
}