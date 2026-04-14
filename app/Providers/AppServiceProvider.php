<?php

namespace App\Providers;

use App\Models\ExperienceInquiry;
use App\Models\PaymentTransaction;
use App\Observers\ExperienceInquiryObserver;
use App\Observers\PaymentTransactionObserver;
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
        PaymentTransaction::observe(PaymentTransactionObserver::class);
        ExperienceInquiry::observe(ExperienceInquiryObserver::class);
    }
}
