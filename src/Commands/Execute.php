<?php
namespace Prateekkarki\Laragen\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
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
     * Create a new command instance.
     *
     * @param Composer $composer
     * @return void
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
    }

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

        $this->composer->dumpOptimized();

        $bar->advance();
        Artisan::call('laragen:migrate');

        $bar->advance();
        Artisan::call('laragen:seed');
        $bar->finish();
        $this->line("\n");
        $this->line('Code generation, migration, seeding successfull.');
    }
}
