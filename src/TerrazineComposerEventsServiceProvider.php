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

                    $this->ignore('.phpstorm.meta.php');
                    $this->ignore('_ide_helper.php');

                    file_put_contents(...[
                        base_path('_ide_helper.php'),
                        file_get_contents(__DIR__ . '/append.php.stub'),
                        FILE_APPEND,
                    ]);
                };
            });
        }
    }

    public function gitignorePath(): string {
        return base_path('.gitignore');
    }

    public function ignore(string $file) {
        $gitignore = file_get_contents($this->gitignorePath());

        preg_match_all("/^{$file}$/m", $gitignore, $matches);

        if (empty($matches[0])) {
            file_put_contents($this->gitignorePath(), $file . PHP_EOL, FILE_APPEND);
        }
    }
}
