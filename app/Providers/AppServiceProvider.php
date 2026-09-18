<?php

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\Chemical;
use App\Models\Supplier;
use App\Models\User;
use App\Policies\AuditLogPolicy;
use App\Policies\ChemicalPolicy;
use App\Policies\StockPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {

    }

    public function boot(): void
    {

        Paginator::useTailwind();

        Gate::policy(AuditLog::class, AuditLogPolicy::class);
        Gate::policy(Chemical::class, ChemicalPolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        Gate::define('stockIn',     [StockPolicy::class, 'stockIn']);
        Gate::define('stockOut',    [StockPolicy::class, 'stockOut']);
        Gate::define('adjustStock', [StockPolicy::class, 'adjust']);
    }
}

