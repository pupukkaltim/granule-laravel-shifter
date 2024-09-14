<?php

namespace Granule\LaravelShifter\Console;

use Granule\LaravelShifter\Utils\DefaultUtility;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class UpgradeCommand extends Command implements PromptsForMissingInput
{
    use DefaultUtility, ShiftToLaravel11;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:upgrade';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade the Laravel application to newer version.';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        // check laravel version
        $this->components->info('Laravel version: ' . app()->version());

        // confirmation if the user wants to upgrade to newer version
        $installedVersion = (int) explode('.', app()->version())[0];
        if (!$this->confirm('Do you want to upgrade to laravel ' . $installedVersion + 1 . '.x?')) {
            return 0;
        }
        
        if ($installedVersion === 10 && $this->verifyLaravel11Requirements()) {
            $this->shiftToLaravel11();
        } else if ($installedVersion === 11) {
            $this->components->error('Upgrading to Laravel 12.x is not supported yet.');
        }

        return 1;
    }

    /**
     * Verify the requirements for upgrading to Laravel 11.x
     * 
     * @return bool
     */
    public function verifyLaravel11Requirements(): bool
    {
        // Verify PHP version
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            $this->components->error('PHP 8.2.0 or higher is required to upgrade to Laravel 11.x');
            return false;
        }

        return true;
    }

}
