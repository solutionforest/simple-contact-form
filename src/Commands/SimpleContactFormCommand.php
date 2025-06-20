<?php

namespace SolutionForest\SimpleContactForm\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SimpleContactFormCommand extends Command
{
    public $signature = 'simple-contact-form:publish {--tag=* : Tag that should be used to publish language files (defaults to "simple-contact-form-lang")}';

    public $description = 'Publish Simple Contact Form language files';

    public function handle(): int
    {
        $tags = $this->option('tag');

        if (empty($tags) || in_array('simple-contact-form-lang', $tags)) {
            $this->publishLanguageFiles();
        }

        return self::SUCCESS;
    }

    protected function publishLanguageFiles(): void
    {
        $langPath = base_path('lang/vendor/simple-contact-form');
        
        if (File::isDirectory($langPath)) {
            $this->info('Language files already exist. Overwriting...');
        }
        
        $sourcePath = __DIR__ . '/../../resources/lang';
        
        if (! File::isDirectory($sourcePath)) {
            $this->error('Language files source directory not found!');
            return;
        }
        
        File::ensureDirectoryExists($langPath);
        
        // Copy all language files
        File::copyDirectory($sourcePath, $langPath);
        
        $this->info('Simple Contact Form language files published successfully!');
        $this->info('You can now customize them at: ' . $langPath);
    }
}
