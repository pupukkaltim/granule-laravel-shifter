<?php

namespace Granule\LaravelShifter\Utils;

use Symfony\Component\Process\Process;

trait DefaultUtility
{
    /**
     * Run the given commands.
     *
     * @param  array  $commands
     * @return void
     */
    protected function runCommands($commands)
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (\RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    '.$line);
        });
    }

    /**
     * Replace content in file
     *
     * @param string $file
     * @param array $replacements
     * @return void
     */
    protected function replaceContent($file, $replacements)
    {
        $content = file_get_contents($file);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        file_put_contents($file, $content);
    }
}
