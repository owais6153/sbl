<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;
use App\Models\OnHand;
use App\Models\OnReciving;
use Illuminate\Bus\Batchable;
use File;
class ImportCSV implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $file ;
    public $table;    
    public $user_id;   
    public $upload_id;  
    
    public function __construct($file,  $table, $user_id, $upload_id)
    {
        $this->file   = $file;
        $this->table = $table;    
        $this->user_id = $user_id;   
        $this->upload_id = $upload_id;    
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $header = null;
        $delimiter = ',';
        if (($handle = fopen( $this->file, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header){
                    $header = $row;
                }
                else{                     
                    if($this->table == 'on_hand'){                       
                        $OnHand = new OnHand();
                        $OnHand->upload_id = $this->upload_id;
                        $OnHand->user_id = $this->user_id;
                        $OnHand->brand = $row[0];
                        $OnHand->item_number = $row[1];
                        $OnHand->item_name = $row[2];
                        $OnHand->warehouse = $row[3];
                        $OnHand->on_hand = $row[4];
                        $OnHand->available = $row[5];
                        $OnHand->reserved = $row[6];
                        $OnHand->in_transit = $row[7];
                        $OnHand->on_sales_order = $row[8];
                        $OnHand->on_purchase_order = $row[9];
                        $OnHand->save();
                    }
                    else if($this->table == 'on_reciving'){ 
                        $OnReciving = new OnReciving();
                        $OnReciving->upload_id = $this->upload_id;
                        $OnReciving->user_id = $this->user_id;
                        $OnReciving->brand = $row[0];
                        $OnReciving->item_number = $row[1];
                        $OnReciving->item_name = $row[2];
                        $OnReciving->warehouse = $row[3];
                        $OnReciving->on_hand = $row[4];
                        $OnReciving->available = $row[5];
                        $OnReciving->reserved = $row[6];
                        $OnReciving->in_transit = $row[7];
                        $OnReciving->on_sales_order = $row[8];
                        $OnReciving->on_purchase_order = $row[9];
                        $OnReciving->save();
                    }
                }
            }
            fclose($handle);
        }         
    }
    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
    }
}
