<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\EditProfile;
use App\Filament\Resources\MyCarryRequestResource;
use App\Filament\Resources\MyTravelResource;
use Exception;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    /**
     * @throws Exception
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->topNavigation()
            ->colors([
                'danger' => Color::Red,
                'info' => Color::Blue,
                'primary' => Color::Emerald,
                'white' => Color::hex('#fff'),
                'black' => Color::hex('#000'),
            ])
            ->id('app')
            ->path('app')
            ->viteTheme('resources/css/filament/app/theme.css')
            ->brandLogo(fn() => view('filament.brand'))
            ->brandLogoHeight('30px')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //
            ])
            ->login(Login::class)
            ->registration(Register::class)
            ->emailVerification()
            ->passwordReset()
            ->profile(page: EditProfile::class, isSimple: false)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->renderHook(
                'panels::auth.login.form.after',
                fn () => view('filament.auth.google')
            )
            ->authMiddleware([
                Authenticate::class,
            ])->userMenuItems([
                'my-travels' => MenuItem::make()
                    ->label('My Travels')
                    ->icon('heroicon-o-briefcase') // optional icon
                    ->url(fn() => MyTravelResource::getUrl('index')),
                'my-carry-requests' => MenuItem::make()
                    ->label('My Carry Requests')
                    ->icon('heroicon-o-truck') // optional icon
                    ->url(fn() => MyCarryRequestResource::getUrl('index')),
            ]);
    }
}
