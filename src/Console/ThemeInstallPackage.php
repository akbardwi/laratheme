<?php

namespace Akbardwi\Laratheme\Console;

use Akbardwi\Laratheme\Facades\Theme;
use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use ZipArchive;

class ThemeInstallPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:install {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a theme package';

    /**
     * Filesystem.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Config.
     *
     * @var \Illuminate\Support\Facades\Config
     */
    protected $config;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function __construct(Repository $config)
    {
        parent::__construct();
        $this->files = new \Illuminate\Filesystem\Filesystem();
        $this->config = $config;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $package = $this->argument('name');
        if ($package == "") {
            $filenames = $this->files->glob(Theme::packages_path('*.zip'));
            $packages = array_map(function ($file) {
                return basename($file, '.zip');
            }, $filenames);
            if (count($packages) == 0) {
                $this->info("No theme packages found to install at ".Theme::packages_path());
                return;
            }

            $package = $this->choice('Select a theme package', $packages);
        }

        $package = Theme::packages_path($package.'.zip');
        
        // Create Temp Folder
        Theme::createTempFolder();

        // Extract Package
        try {
            $zip = new ZipArchive;
            if ($zip->open($package) === true) {
                $zip->extractTo(Theme::tempPath());
                $zip->close();
            }
        } catch (\Exception $e) {
            $this->error('Failed to extract package: '.$e->getMessage());
            return;
        }
        
        $themeJson = file_get_contents(Theme::tempPath().DIRECTORY_SEPARATOR.'theme.json');
        $themeJson = json_decode($themeJson, true);
        $themePath = $this->config->get('theme.theme_path').DIRECTORY_SEPARATOR.$themeJson['name'];

        // Check if theme is already installed
        if (Theme::theme_installed($themeJson['name'])) {
            $this->error('Theme '.$themeJson['name'].' is already installed');
            Theme::clearTempFolder();
            return;
        }

        // If paths don't exist, move theme from temp to target paths
        if (!file_exists($themePath)) {
            $this->files->move(Theme::tempPath(), $themePath);
            $this->info('Theme '.$themeJson['name'].' installed successfully');
        } else {
            $this->error('Theme '.$themeJson['name'].' already exists');
        }

        // Clear Temp Folder
        Theme::clearTempFolder();

        // Remove Package
        $this->files->delete($package);
    }
}
