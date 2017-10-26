<?php

namespace Terrazine\LaravelIdeHelper;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Terrazine\ComposerEvents\PostAutoloadDump;

class TerrazineLaravelIdeHelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            Event::listen(PostAutoloadDump::class, function () {
                return function (InputInterface $input, OutputInterface $output){
                    Artisan::call('ide-helper:models', [
                        '--write' => true,
                        '--reset' => true,
                    ], $output);
                    Artisan::call('ide-helper:generate', [], $output);
                    Artisan::call('ide-helper:meta', [], $output);
                };
            });
        }
    }
}
