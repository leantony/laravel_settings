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
    {--setup : Insert new settings into the database.}
    {--bind : Bind values in the database to those in the app .}
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
        $settings = app(SettingsHelper::class);
        if ($this->option('setup')) {
            $this->doPutNew($settings);
            return 0;
        }
        if ($this->option('bind')) {
            $this->replace($settings);
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
        if ($settings->shouldCache()) {
            $this->info('Clearing the config cache...');
            $this->call('config:clear');
        }

        $this->info('Inserting new values...');
        $settings->insert($settings->grab());
        if ($settings->shouldCache()) {
            $this->info('Caching config...');
            $this->call('config:cache');
        }

        $this->info('done...');
    }

    /**
     * @param SettingsHelper $settings
     */
    protected function replace($settings)
    {
        $this->info("Binding laravel config variables to cached ones from db...");
        $settings->replaceLoaded();
        $this->info("Done...");
    }
}
