<?php

namespace Hexatex\LaravelImage\Commands;

use Illuminate\Console\Command;

class LaravelImageCommand extends Command
{
    public $signature = 'laravel-image';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
