<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {   
        //增加自定义验证 【验证字符是否超过指定长度 指定字符集】
        Validator::extend('mb_max', function($attribute, $value, $parameters, $validator) {
            return (mb_strlen($value, 'utf-8') <= $parameters[0]);
        });
        Validator::extend('mb_min', function($attribute, $value, $parameters, $validator) {
            return (mb_strlen($value, 'utf-8') >= $parameters[0]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
