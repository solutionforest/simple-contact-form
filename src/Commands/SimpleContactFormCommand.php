<?php

namespace SolutionForest\SimpleContactForm\Commands;

use Illuminate\Console\Command;

class SimpleContactFormCommand extends Command
{
    public $signature = 'simple-contact-form';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
