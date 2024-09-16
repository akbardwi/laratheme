<?php

namespace Akbardwi\Laratheme\Console;

use Illuminate\Console\Command;
use Akbardwi\Laratheme\Contracts\ThemeContract;

class ThemeRemoveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:remove {themeName?} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available themes';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get themes name
        $themeName = $this->argument('themeName');
        if ($themeName == "") {
            $themes = $this->laravel[ThemeContract::class]->all();
            $output = [];
            foreach ($themes as $theme) {
                $output[] = $theme->get('name');
            }

            $themeName = $this->choice('Which theme would you like to remove?', $output);
        }

        // Remove without confirmation?
        $force = $this->option('force');

        // Check that theme exists
        if (!$this->laravel[ThemeContract::class]->has($themeName)) {
            $this->error('Theme not found!');
            return;
        }

        // Confirm removal
        if (!$force && !$this->confirm('Are you sure you want to remove this theme?')) {
            return;
        }

        // Remove theme
        try {
            $this->laravel[ThemeContract::class]->remove($themeName);
            $this->info('Theme removed successfully.');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
