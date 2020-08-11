<?php

namespace App\Providers;

use App\Core\Layout\Content;
use App\Core\Layout\Menu;
use App\Core\Layout\SectionManager;
use Illuminate\Support\Fluent;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerServices();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
        $this->registerDefaultSections();
    }

    /**
     * 默认 section 注册.
     */
    protected function registerDefaultSections()
    {
        Content::composing(function () {
            Menu::make()->register();
        }, true);
    }

    protected function registerServices()
    {
        $this->app->singleton('core.context', Fluent::class);
        $this->app->singleton('core.sections', SectionManager::class);
    }
}
