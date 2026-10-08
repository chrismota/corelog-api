<?php

namespace App\Providers;

use App\Contexts\OrganizationContext;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Shipment;
use App\Policies\AddressPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ShipmentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OrganizationContext::class, function () {
            return new OrganizationContext();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Shipment::class, ShipmentPolicy::class);
        Gate::policy(Address::class, AddressPolicy::class);
    }
}
