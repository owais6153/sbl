<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Items;
use App\Models\ItemsCron;
use App\Models\ItemIdentifier;
use Log;
class ImportItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importItems:hourly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Items from API usign cron every hour.';

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
        $offset = 0;
        $limit = 1000;
        $lastImport = ItemsCron::latest()->first();
        if (!empty($lastImport)) {
            $offset = 1000 + $lastImport->item_offset;
            $limit =  $limit + $lastImport->item_limit;
        }



        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://161.35.3.201/products?limit='.$limit.'&offset='. $offset,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic amFjazpzZWNyZXQ='
          ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($ch);
            \Log::info("Curl error in ImportItems Command: " . $error_msg);
        }
        else{
            $res = json_decode($response);
            if ($res->error) {
                 \Log::info("API is returning error in ImportItems Command");
            }
            else{
                if (!empty($res->data)) {
                    $cron_data = new ItemsCron();
                    $cron_data->remaining = $res->remaining;
                    $cron_data->totalRecords = $res->totalRecords;
                    $cron_data->item_offset = $offset;
                    $cron_data->item_limit = $limit;
                    $cron_data->save();
                    foreach ($res->data as $item){
                        if (isset($item->productIdentifiers[0])) {
                            $items = new Items();
                            $items->item_number = $item->itemNumber;
                            $items->save();
                            foreach ($item->productIdentifiers as $productIdentifier){
                                if ($productIdentifier->identifierType == 'UPC') {
                                    $ItemIdentifier = new ItemIdentifier();
                                    $ItemIdentifier->item_id = $items->id;
                                    $ItemIdentifier->productIdentifier = $productIdentifier->productIdentifier;
                                    $ItemIdentifier->save();
                                }
                            }                                                       
                        }
                    }
                }
            }
        }

        $this->info('Items Imported successfully!');   
         // \Log::info("Items Imported. ");                 
        curl_close($curl);
    }
}
