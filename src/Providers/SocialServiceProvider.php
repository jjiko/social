<?php namespace Jiko\Social\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class SocialServiceProvider extends ServiceProvider {
  public function boot()
  {
    parent::boot();

    $this->loadViewsFrom(__DIR__ . '/../resources/views', 'social');
  }

  public function map()
  {

  }
}