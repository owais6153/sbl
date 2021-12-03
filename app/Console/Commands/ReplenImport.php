<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReplenBatch;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ReplenImportJob;

class ReplenImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replen:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Replen Data';

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
     * @return int
     */
    public function handle()
    {
        $ReplenBatch = new ReplenBatch();
        $ReplenBatch->status = 'In-process';
        $ReplenBatch->save();

        $batch  = Bus::batch([])->dispatch();
        $batch->add(new ReplenImportJob($ReplenBatch->id));

        $this->info('Queue added for Replen import');
    }
}
