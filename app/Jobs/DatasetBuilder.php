<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
//use App\Dataset\Build;

class DatasetBuilder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $build;
    
    public function __construct($build)
    {
        $this->build = $build;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //\Log::debug("S2");
        $this->build->make();
        
    }
    
}
