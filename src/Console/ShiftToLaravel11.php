<?php

namespace Granule\LaravelShifter\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

trait ShiftToLaravel11
{
    /**
     * Shift to Laravel 11.x
     *
     * @return void
     */
    public function shiftToLaravel11()
    {
        $this->info('Upgrading to Laravel 11.x');

        // change php 8.x to 8.2
        $this->replaceContent(base_path('composer.json'), [
            '"php": "^8.0"' => '"php": "^8.2"',
            '"php": "^8.1"' => '"php": "^8.2"',
        ]);

        // change package versions
        $this->runCommands([
            'composer require granule/starter-kit:dev-v6-dev --no-update --quiet',
            'composer require inertiajs/inertia-laravel:^1.3.0 laravel/framework:^11.22 laravel/reverb:^1.3 laravel/sanctum:^4.0 laravel/tinker:^2.9 --no-update --quiet',
            'composer require fakerphp/faker:^1.23 laravel/breeze:v2.1 laravel/pint:^1.13 laravel/sail:^1.26 mockery/mockery:^1.6 nunomaduro/collision:^8.1 --dev --no-update --quiet',
            'composer update -W --no-scripts --no-interaction'
        ]);

        // copy bootstrap/app.php stub to project
        copy(__DIR__.'/../../stubs/bootstrap/app.php', base_path('bootstrap/app.php'));

        // call refactoring methods
        $this->refactoringControllers();
        $this->refactoringModels();
        $this->refactoringConsole();
        $this->refactoringExceptions();
        $this->refactoringMiddleware();
        $this->refactoringProviders();
    }

    /**
     * Refactoring controllers to Laravel 11.x standards
     * 
     * @return void
     */
    private function refactoringControllers()
    {
        // copy stubs/app/Http/Controllers/Controller.php to project
        copy(__DIR__.'/../../stubs/app/Http/Controllers/Controller.php', base_path('app/Http/Controllers/Controller.php'));

        $this->components->info('Controllers refactored to Laravel 11.x standards');
    }

    /**
     * Refactoring models to Laravel 11.x standards
     * 
     * @return void
     */
    private function refactoringModels()
    {
        // get all files in the models directory
        $files = (new Filesystem)->allFiles(app_path('Models'));
        
        // get contents of each file
        foreach ($files as $file) {
            // get protected $casts = [ ... ]; from each file
            $contents = file_get_contents($file);
            $pattern = '/protected \$casts = \[(.*?)\];/s';
            preg_match($pattern, $contents, $matches);
            if (count($matches) > 0) {
                $newCasts = 'protected casts(): array' . PHP_EOL . '    {' . PHP_EOL . '        return [' . PHP_EOL . '            ' . $matches[1] . PHP_EOL . '        ];' . PHP_EOL . '    }';
                $this->replaceContent($file, [
                    $matches[0] => $newCasts,
                ]);
            }
        }

        $this->components->info('Models refactored to Laravel 11.x standards');
    }

    /**
     * Refactoring console to Laravel 11.x standards
     *
     * @return void
     */
    private function refactoringConsole()
    {
        // Register schedule in app/Console/Kernel.php to bootstrap/app.php
        $content = file_get_contents(base_path('app/Console/Kernel.php'));
        preg_match('/protected function schedule\(Schedule \$schedule\): void\n    {\n        (.*)\n    }\n\n    /s', $content, $matches);
        $this->replaceContent(base_path('bootstrap/app.php'), [
            '{{ schedule }}' => $matches[1],
        ]);

        // Remove app/Console directory
        (new Filesystem)->deleteDirectory(base_path('app/Console'));

        $this->components->info('Console refactored to Laravel 11.x standards');
    }

    /**
     * Refactoring exceptions to Laravel 11.x standards
     *
     * @return void
     */
    private function refactoringExceptions()
    {
        // Remove app/Exceptions
        (new Filesystem)->deleteDirectory(base_path('app/Exceptions'));

        $this->components->info('Exceptions refactored to Laravel 11.x standards');
    }

    /**
     * Refactoring middleware to Laravel 11.x standards
     *
     * @return void
     */
    private function refactoringMiddleware()
    {
        // Remove files that are not needed in Laravel 11.x
        (new Filesystem)->delete([
            app_path('Http/Middleware/Authenticate.php'),
            app_path('Http/Middleware/EncryptCookies.php'),
            app_path('Http/Middleware/PreventRequestsDuringMaintenance.php'),
            app_path('Http/Middleware/RedirectIfAuthenticated.php'),
            app_path('Http/Middleware/TrimStrings.php'),
            app_path('Http/Middleware/TrustHosts.php'),
            app_path('Http/Middleware/TrustProxies.php'),
            app_path('Http/Middleware/ValidateSignature.php'),
            app_path('Http/Middleware/VerifyCsrfToken.php'),
        ]);

        // get content of app/Http/Kernel.php
        $content = file_get_contents(base_path('app/Http/Kernel.php'));

        // get content inside protected $middleware = [ ... ];
        preg_match('/protected \$middleware = \[(.*?)\];/s', $content, $matches);
        $excludeMiddleware = [
            '// \App\Http\Middleware\TrustHosts::class',
            '\App\Http\Middleware\TrustProxies::class',
            '\Illuminate\Http\Middleware\HandleCors::class',
            '\App\Http\Middleware\PreventRequestsDuringMaintenance::class',
            '\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class',
            '\App\Http\Middleware\TrimStrings::class',
            '\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class',
            '',
        ];
        $middleware = $matches[1];
        $middleware = explode(',', $middleware);
        $middleware = array_map(function ($item) {
            return trim($item);
        }, $middleware);
        $middleware = array_diff($middleware, $excludeMiddleware);

        // get content inside protected $middlewareGroups = [ web => [ ... ] ];
        $excludeMiddlewareGroupsWeb = [
            '\App\Http\Middleware\EncryptCookies::class',
            '\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class',
            '\Illuminate\Session\Middleware\StartSession::class',
            '\Illuminate\View\Middleware\ShareErrorsFromSession::class',
            '\App\Http\Middleware\VerifyCsrfToken::class',
            '\Illuminate\Routing\Middleware\SubstituteBindings::class',
            '',
        ];
        preg_match('/protected \$middlewareGroups = \[(.*?)\];/s', $content, $matches);
        $middlewareGroupsWeb = $matches[1];
        preg_match('/\'web\' => \[(.*?)\],/s', $middlewareGroupsWeb, $matches);
        $middlewareGroupsWeb = $matches[1];
        $middlewareGroupsWeb = explode(',', $middlewareGroupsWeb);
        $middlewareGroupsWeb = array_map(function ($item) {
            return trim($item);
        }, $middlewareGroupsWeb);
        $middlewareGroupsWeb = array_diff($middlewareGroupsWeb, $excludeMiddlewareGroupsWeb);

        // get content inside protected $middlewareGroups = [ api => [ ... ] ];
        $excludeMiddlewareGroupsApi = [
            '\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class',
            '\Illuminate\Routing\Middleware\ThrottleRequests::class.\':api\'',
            '\Illuminate\Routing\Middleware\SubstituteBindings::class',
            '',
        ];
        preg_match('/protected \$middlewareGroups = \[(.*?)\];/s', $content, $matches);
        $middlewareGroupsApi = $matches[1];
        preg_match('/\'api\' => \[(.*?)\],/s', $middlewareGroupsApi, $matches);
        $middlewareGroupsApi = $matches[1];
        $middlewareGroupsApi = explode(',', $middlewareGroupsApi);
        $middlewareGroupsApi = array_map(function ($item) {
            return trim($item);
        }, $middlewareGroupsApi);
        $middlewareGroupsApi = array_diff($middlewareGroupsApi, $excludeMiddlewareGroupsApi);

        // get content inside protected $middlewareAliases = [ ... ];
        $excludeMiddlewareAliases = [
            '\'auth\' => \App\Http\Middleware\Authenticate::class',
            '\'auth.basic\' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class',
            '\'auth.session\' => \Illuminate\Session\Middleware\AuthenticateSession::class',
            '\'cache.headers\' => \Illuminate\Http\Middleware\SetCacheHeaders::class',
            '\'guest\' => \App\Http\Middleware\RedirectIfAuthenticated::class',
            '\'password.confirm\' => \Illuminate\Auth\Middleware\RequirePassword::class',
            '\'precognitive\' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class',
            '\'signed\' => \App\Http\Middleware\ValidateSignature::class',
            '\'throttle\' => \Illuminate\Routing\Middleware\ThrottleRequests::class',
            '\'verified\' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class',
            '',
        ];
        preg_match('/protected \$middlewareAliases = \[(.*?)\];/s', $content, $matches);
        $middlewareAliases = $matches[1];
        $middlewareAliases = explode(',', $middlewareAliases);
        $middlewareAliases = array_map(function ($item) {
            return trim($item);
        }, $middlewareAliases);
        $middlewareAliases = array_diff($middlewareAliases, $excludeMiddlewareAliases);

        $additionalMiddleware = [
            'middleware' => $middleware,
            'middlewareGroupsWeb' => $middlewareGroupsWeb,
            'middlewareGroupsApi' => $middlewareGroupsApi,
            'middlewareAliases' => $middlewareAliases,
        ];

        // replace content {{ middleware-global }}, {{ middleware-web }}, {{ middleware-api }}, {{ middleware-alias }} in bootstrap/app.php
        $middlewareGlobal = empty($additionalMiddleware['middleware']) ? '// ...' : implode(",\n            ", $additionalMiddleware['middleware']);
        $middlewareWeb = empty($additionalMiddleware['middlewareGroupsWeb']) ? '// ...' : implode(",\n            ", $additionalMiddleware['middlewareGroupsWeb']);
        $middlewareApi = empty($additionalMiddleware['middlewareGroupsApi']) ? '// ...' : implode(",\n            ", $additionalMiddleware['middlewareGroupsApi']);
        $middlewareAlias = empty($additionalMiddleware['middlewareAliases']) ? '// ...' : implode(",\n            ", $additionalMiddleware['middlewareAliases']);

        $this->replaceContent(base_path('bootstrap/app.php'), [
            '{{ middleware-global }}' => $middlewareGlobal,
            '{{ middleware-web }}' => $middlewareWeb,
            '{{ middleware-api }}' => $middlewareApi,
            '{{ middleware-alias }}' => $middlewareAlias,
        ]);

        // remove app/Http/Kernel.php
        (new Filesystem)->delete(base_path('app/Http/Kernel.php'));

        $this->components->info('Middleware refactored to Laravel 11.x standards');
    }

    /**
     * Refactoring providers to Laravel 11.x standards
     * 
     * @return void
     */
    private function refactoringProviders()
    {
        // copy stubs/bootstrap/providers.php to project
        copy(__DIR__.'/../../stubs/bootstrap/providers.php', base_path('bootstrap/providers.php'));

        // get all classes in app/Providers directory
        $files = (new Filesystem)->files(base_path('app/Providers'));
        $classes = array_map(function ($file) {
            $content = file_get_contents($file);
            preg_match('/class (.*) extends /', $content, $matches);
            return $matches[1];
        }, $files);
        $excludeClasses = [
            'AppServiceProvider',
            'AuthServiceProvider',
            'BroadcastServiceProvider',
            'EventServiceProvider',
            'RouteServiceProvider',
        ];
        $classes = array_diff($classes, $excludeClasses);
        $classes = array_map(function ($class) {
            return 'App\Providers\\'.$class.'::class';
        }, $classes);

        $this->replaceContent(base_path('bootstrap/providers.php'), [
            '{{ providers }}' => empty($classes) ? '// ...' : implode(",\n    ", $classes),
        ]);

        $this->components->info('Providers refactored to Laravel 11.x standards');
    }
}
