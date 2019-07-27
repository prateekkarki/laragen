<?php
namespace Prateekkarki\Laragen\Commands;

use Illuminate\Console\Command;

class Initialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laragen:init';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializing laragen';
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        copy(__DIR__ . '/../../src/resources/stubs/RouteServiceProvider.stub', app_path('Providers/LaragenRouteServiceProvider.php'));
        if (!is_dir('routes/backend')) {
            try {
                mkdir(base_path('routes/backend'), 0777, true);
            } catch (\Exception $e) {
                exit("Couldn't make directory. Make sure you have right permissions.");
            }
            copy(__DIR__ . '/../../src/resources/stubs/Route.stub', base_path('routes/backend/web.php'));
            copy(__DIR__ . '/../../src/resources/stubs/Route.stub', base_path('routes/backend/auth.php'));
        }
        if (!is_dir('routes/frontend')) {
            try {
                mkdir(base_path('routes/frontend'), 0777, true);
            } catch (\Exception $e) {
                exit("Couldn't make directory. Make sure you have right permissions.");
            }
            copy(__DIR__ . '/../../src/resources/stubs/Route.stub', base_path('routes/frontend/web.php'));
        }
    }
}
