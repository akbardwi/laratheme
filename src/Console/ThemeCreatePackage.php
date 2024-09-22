<?php

namespace Akbardwi\Laratheme\Console;

use Akbardwi\Laratheme\Contracts\ThemeContract;
use Akbardwi\Laratheme\Facades\Theme;
use Illuminate\Console\Command;
use ZipArchive;

class ThemeCreatePackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:package {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new theme package';

    /**
     * Filesystem.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function __construct()
    {
        parent::__construct();
        $this->files = new \Illuminate\Filesystem\Filesystem();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $themeName = $this->argument('name');
        if ($themeName == "") {
            $themes = $this->laravel[ThemeContract::class]->all();
            $output = [];
            foreach ($themes as $theme) {
                $output[] = $theme->get('name');
            }

            $themeName = $this->choice('Select a theme', $output);
        }

        $theme = $this->laravel[ThemeContract::class]->get($themeName);

        $themePath = $theme['path'];

        // package storage path
        $packagePath = Theme::packages_path();
        if (!is_dir($packagePath)) {
            mkdir($packagePath, 0755, true);
        }

        // Sanitize target filename
        $packageFileName = $theme['name'];
        $packageFileName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $packageFileName);
        $packageFileName = mb_ereg_replace("([\.]{2,})", '', $packageFileName);
        $packageFileName = Theme::packages_path("{$packageFileName}.zip");

        // Create temporary folder
        Theme::createTempFolder();

        // Copy theme files to temporary folder
        $this->files->copyDirectory($themePath, Theme::tempPath());

        // Zip Temp Folder contents
        $zip = new ZipArchive();
        if ($zip->open($packageFileName, ZipArchive::CREATE) === TRUE) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(Theme::tempPath()), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);
                if (in_array(substr($file, strrpos($file, '/') + 1), ['.', '..'])) {
                    continue;
                }
                $file = realpath($file);
                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace(Theme::tempPath() . '/', '', $file . '/'));
                } elseif (is_file($file) === true) {
                    $zip->addFromString(str_replace(Theme::tempPath() . '/', '', $file), file_get_contents($file));
                }
            }
            $zip->close();
        }

        // Remove Temp Folder
        Theme::clearTempFolder();

        $this->info("Theme package created at {$packageFileName}");
    }
}
