<?php

namespace Leantony\Settings\Commands;

use Illuminate\Console\Command;
use Leantony\Settings\SettingsHelper;

class ManageSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manage:settings 
    {--new=true : Insert new settings into the database.}
    {--reload=true : Reload cache by forcefully re-fetching from db.}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage application settings usually on config files, using a database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $settings = settings();
        $put = $this->option('new');
        if ($put) {
            $this->doPutNew($settings);
            return 0;
        }
    }

    /**
     * @param SettingsHelper $settings
     */
    protected function doPutNew($settings)
    {
        $this->info('Truncating the settings table...');
        $this->laravel['db']->table($settings->getTableName())->truncate();
        $this->info('Clearing the settings cache...');
        $settings->forget();
        $this->info('Inserting and caching new values...');
        $settings->insert($settings->grab());
        $settings->cacheAll();
        $this->info('done...');
    }

    /**
     * @param SettingsHelper $settings
     */
    protected function refreshCache($settings)
    {
        $this->info('Clearing the settings cache...');
        $settings->forget();
        $this->info('Caching new values...');
        $settings->cacheAll();
        $this->info('done...');
    }
}
