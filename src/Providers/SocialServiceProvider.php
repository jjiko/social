<?php namespace Jiko\Social\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Jiko\Social\Twitter\Commands\TwitterRelationships;
use Jiko\Social\Twitter\Commands\TwitterRelationshipsUserLookup;
use Jiko\Social\Twitter\Commands\TwitterSend;

class SocialServiceProvider extends ServiceProvider
{
  public function boot()
  {
    parent::boot();

    if ($this->app->runningInConsole()) {
      $this->commands([
        TwitterRelationships::class,
        TwitterRelationshipsUserLookup::class,
        TwitterSend::class
      ]);
    }

    $this->loadViewsFrom(__DIR__ . '/../resources/views', 'social');
  }

  public function map()
  {
    require_once(__DIR__ . '/../Http/routes.php');
  }
}