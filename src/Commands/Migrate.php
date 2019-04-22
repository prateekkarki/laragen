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
        Artisan::call('migrate', [
            '--path' => 'database/migrations/laragen'
        ]);
        $this->line(Artisan::output());
    }
}
