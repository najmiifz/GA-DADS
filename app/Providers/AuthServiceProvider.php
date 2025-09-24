<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\ReimburseRequestPolicy;
use App\Models\ReimburseRequest;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
        protected $policies = [
            // Model => Policy mappings
            ReimburseRequest::class => ReimburseRequestPolicy::class,
        ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define custom gates
        Gate::define('kelola-akun', function ($user) {
            return in_array($user->role, ['admin', 'super-admin']);
        });

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            // Grant all permissions to super-admin and admin roles
            if (in_array($user->role, ['admin', 'super-admin'])) {
                return true;
            }
            return null;
        });
    }
}
