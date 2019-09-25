<?php
namespace Prateekkarki\Laragen\Commands;

use Illuminate\Console\Command;
use Artisan;

class Migrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laragen:migrate';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Laragen Migrate Database for your project';
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Artisan::call('migrate:fresh');
        $this->line(Artisan::output());

        $migrationDirs = [
            'laragenDir' => 'laragen/database/migrations',
            'laravelDir' => 'database/migrations/laragen'
        ];

        foreach ($migrationDirs as $dir) {
            if(is_dir(base_path($dir)) && count(glob( base_path($dir) . '*', GLOB_MARK ))){
                Artisan::call('migrate', [
                    '--path' => $dir
                ]);
                $this->line(Artisan::output());
            }
        }
    }
}
