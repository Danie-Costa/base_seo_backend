<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Product;
use App\Models\CashRegister;
use App\Policies\ProductPolicy;
use App\Models\Sale;
use App\Policies\SalePolicy;

use App\Policies\CashRegisterPolicy;



class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->role === 'admin') {
                return true;
            }
        });

        Gate::define('manager-access', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        Gate::define('cashier-access', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'cashier']);
        });
    }
}
