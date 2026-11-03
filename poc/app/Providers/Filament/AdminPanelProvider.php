<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Enums\ThemeMode;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->login(Login::class)
            ->brandName('GestãoCommerce')
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Slate,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->darkMode(true)
            ->defaultThemeMode(ThemeMode::Light)
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->navigationGroups([
                NavigationGroup::make('Equipe'),
                NavigationGroup::make('Marketplaces'),
                NavigationGroup::make('Configurações')->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => Blade::render("@vite('resources/js/kanban.js')"))
            ->renderHook(PanelsRenderHook::TOPBAR_START, fn () => Blade::render('<span class="hidden rounded-md bg-warning-50 px-2 py-1 text-xs font-semibold text-warning-700 ring-1 ring-warning-600/20 md:inline dark:bg-warning-400/10 dark:text-warning-400">Versão experimental (POC)</span>'))
            ->renderHook(PanelsRenderHook::USER_MENU_BEFORE, fn () => Blade::render('<x-theme-toggle />'))
            ->renderHook(PanelsRenderHook::SIMPLE_LAYOUT_START, fn () => Blade::render('<div class="fixed right-4 top-4 z-10"><x-theme-toggle class="bg-white dark:bg-gray-900" /></div>'))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
