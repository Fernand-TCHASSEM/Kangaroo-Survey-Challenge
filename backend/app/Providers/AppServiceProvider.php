<?php

namespace App\Providers;

use App\Aggregation\AggregatorRegistry;
use App\Aggregation\DateAggregator;
use App\Aggregation\NumericAggregator;
use App\Aggregation\QcmAggregator;
use App\Repositories\SurveyRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Adding a new question type = adding one line here.
        $this->app->singleton(AggregatorRegistry::class, fn () => new AggregatorRegistry([
            new QcmAggregator(),
            new NumericAggregator(),
            new DateAggregator(),
        ]));

        $this->app->singleton(
            SurveyRepository::class,
            fn () => new SurveyRepository(config('surveys.disk')),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
