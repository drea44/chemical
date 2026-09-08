<?php

namespace App\Providers;

use App\Models\Chemical;
use App\Models\Supplier;
use App\Models\User;
use App\Policies\ChemicalPolicy;
use App\Policies\StockPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Use Tailwind pagination views
        Paginator::useTailwind();

        // Register Policies
        Gate::policy(Chemical::class, ChemicalPolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // Stock gates (model-less actions)
        Gate::define('stockIn',     [StockPolicy::class, 'stockIn']);
        Gate::define('stockOut',    [StockPolicy::class, 'stockOut']);
        Gate::define('adjustStock', [StockPolicy::class, 'adjust']);
    }
}
