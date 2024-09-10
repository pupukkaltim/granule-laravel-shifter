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
        // cek laravel version
        $this->components->info('Laravel version: ' . app()->version());

        // choice version
        $version = $this->choice('Which version do you want to upgrade to?', [
            '11.x',
            '12.x',
        ], 0);

        if ($version === '11.x') {
            $this->shiftToLaravel11();
        } else if ($version === '12.x') {
            $this->info('Upgrading to Laravel 12.x');
        }

        return 1;
    }
}
