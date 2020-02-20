<?php

namespace Wuwx\LaravelAdminTimestampBetween;

use Encore\Admin\Grid\Filter;
use Illuminate\Support\ServiceProvider;

class LaravelAdminTimestampBetweenServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Filter::extend('timestampBetween', TimestampBetween::class);
    }
}
