<?php

namespace Granule\LaravelShifter\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class UpgradeCommand extends Command implements PromptsForMissingInput
{
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
        $this->info('Upgrade command is not implemented yet.');
        return 1;
    }
}
