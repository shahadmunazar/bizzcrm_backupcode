<?php
namespace App\Providers;

use App\Helpers\BoxPermissionHelper;
use App\Models\BoxUser;
use App\Models\CrmSource;
use App\Models\ContactStage;
use App\Models\LeadSubStatus;
use App\Models\TableColumnPreference;
use App\Models\SystemField;
use App\Models\Industry;
use App\Models\BoxCustomField;
use App\Models\Lead;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {


        if (config('app.env') === 'production' || str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        if ($this->app->runningInConsole()) {
            $this->app->resolving(\Illuminate\Console\Command::class, function ($command, $app) {
                if (method_exists($command, 'setLaravel')) {
                    $command->setLaravel($app);
                }
            });
        }

        // Blade directive to check if user has permission
        Blade::if('hasPermission', function ($permission) {
            $user = Auth::user();
            if (!$user) {
                return false;
            }

            $boxUser = BoxUser::where('user_id', $user->id)->first();
            if (!$boxUser) {
                return false;
            }

            // Check if user is admin (via role or staff_role)
            if (strtolower($boxUser->staff_role ?? '') === 'admin' || $user->hasRole('admin', 'web')) {
                return true;
            }

            return BoxPermissionHelper::boxUserHasPermission($boxUser, $permission);
        });

        // Blade directive to check if user has any of the given permissions
        Blade::if('hasAnyPermission', function (...$permissions) {
            $user = Auth::user();
            if (!$user) {
                return false;
            }

            $boxUser = BoxUser::where('user_id', $user->id)->first();
            if (!$boxUser) {
                return false;
            }

            // Check if user is admin (via role or staff_role)
            if (strtolower($boxUser->staff_role ?? '') === 'admin' || $user->hasRole('admin', 'web')) {
                return true;
            }

            foreach ($permissions as $permission) {
                if (BoxPermissionHelper::boxUserHasPermission($boxUser, $permission)) {
                    return true;
                }
            }

            return false;
        });

        // Blade directive to check if user has all of the given permissions
        Blade::if('hasAllPermissions', function (...$permissions) {
            $user = Auth::user();
            if (!$user) {
                return false;
            }

            $boxUser = BoxUser::where('user_id', $user->id)->first();
            if (!$boxUser) {
                return false;
            }

            // Check if user is admin (via role or staff_role) - admins have all permissions
            if (strtolower($boxUser->staff_role ?? '') === 'admin' || $user->hasRole('admin', 'web')) {
                return true;
            }

            foreach ($permissions as $permission) {
                if (!BoxPermissionHelper::boxUserHasPermission($boxUser, $permission)) {
                    return false;
                }
            }

            return true;
        });
    }
}
