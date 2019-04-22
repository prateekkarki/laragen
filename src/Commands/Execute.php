<?php
namespace Prateekkarki\Laragen\Commands;

use Illuminate\Console\Command;
use Artisan;

class Execute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laragen:exec';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate/Migrate/Seed your project';
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(3);
        $bar->setOverwrite(true);
        $bar->start();

        Artisan::call('laragen:make');

        $bar->advance();
        Artisan::call('laragen:migrate');

        $bar->advance();
        Artisan::call('laragen:seed');
        $bar->finish();
        $this->line("\n");
        $this->line('Code generation, migration, seeding successfull.');
    }
}
