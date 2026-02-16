<?php
namespace App\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentIcon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Pages\Page;

class Bulksms extends Page{
    protected static string $routePath = '../sms';

    protected static ?int $navigationSort = -1;

    /**
     * @var view-string
     */
    protected static string $view = 'sms';

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ??
            static::$title ??'Bulk sms';
    }

    public static function getNavigationIcon(): string | Htmlable | null
    {
        return static::$navigationIcon?? "heroicon-o-bell";
    }

    public static function getRoutePath(): string
    {
        return static::$routePath;
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return Filament::getWidgets();
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getVisibleWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getWidgets());
    }

    /**
     * @return int | string | array<string, int | string | null>
     */
    public function getColumns(): int | string | array
    {
        return 2;
    }

    public function getTitle(): string | Htmlable
    {
        return static::$title ?? 'Bulk sms';
    }
}