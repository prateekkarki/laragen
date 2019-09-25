<?php
namespace Prateekkarki\Laragen\Commands;

use Illuminate\Console\Command;
use Artisan;

class Seeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laragen:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds Database for your project';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Artisan::call('db:seed');

        Artisan::call('db:seed', [
            '--class' => 'LaragenSeeder'
        ]);
        $this->line(Artisan::output());

    }
}
