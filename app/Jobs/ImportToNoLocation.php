<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Items;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;
use App\Models\InventoryLocationTracking as InventoryModel;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryLocation;


class ImportToNoLocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,Batchable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $offset = 1000;
        $limit = 0;
        $loop = true;
        echo "job started \n";

        while($loop){
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://161.35.3.201/products?limit=' . $limit . '&offset=' . $offset,
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
                $error_msg = curl_error($curl);
                \Log::info("Curl error in ImportItems Command: " . $error_msg);
            } else {
                $res = json_decode($response);
                
                if (!empty($res->data)) {
                    foreach ($res->data as $item) {
                        if (!empty($item->inventory)) {
                            foreach ($item->inventory as $inventory) {
                                if (isset($inventory->warehouse) && $inventory->warehouse == "Default Ridgefield" && isset($inventory->onHand)) {
                                    foreach ($item->productIdentifiers as $productIdentifier) {
                                        if ($productIdentifier->identifierType == 'UPC') {


                                            $LocationDetails =  DB::table('inventory_location')
                                                ->select(DB::raw('SUM(`count`) as `quantity`,barcode,item_id'))
                                                ->where('barcode', '=', $productIdentifier->productIdentifier)->orWhere('barcode', '=', '0' . $productIdentifier->productIdentifier)->orWhere('barcode', '=',  substr($productIdentifier->productIdentifier, 1))
                                                ->where('deleted_at', '=', null)->whereRaw("LOWER(`location`) != 'shipping' and LOWER(`location`) != 'production' and LOWER(`location`) != 'adjustment' and LOWER(`location`) != 'receiving'")
                                                ->first();
                                                if(isset($LocationDetails->barcode)){
                                                    $barcode  = $LocationDetails->barcode;
                                                }else{
                                                    $barcode  =$productIdentifier->productIdentifier;
                                                }
                                                if(isset($LocationDetails->item_id) || $LocationDetails->item_id !="null"){
                                                    $item_id =$LocationDetails->item_id;
                                                }else{
                                                    $item_id = null;
                                                }
                                              
                                            if ($LocationDetails->quantity < $inventory->onHand) {
                                               

                                                $Inventory_track = new InventoryModel();
                                                $Inventory_track->user_id = 1;
                                                $Inventory_track->barcode = $barcode;
                                                $Inventory_track->quantity = $inventory->onHand - $LocationDetails->quantity;
                                                $Inventory_track->from = 'Adjustment';
                                                $Inventory_track->item_id = $item_id;
                                                $Inventory_track->to = 'NoLocation';
                                                $Inventory_track->save();
                                                $ToLocation = new InventoryLocation();
                                                $ToLocation->barcode = $barcode;
                                                $ToLocation->count =  $inventory->onHand - $LocationDetails->quantity;
                                                $ToLocation->location = 'NoLocation';
                                                $ToLocation->item_id = $item_id;
                                                $ToLocation->inventory_track_id = $Inventory_track->id;
                                                $ToLocation->save();
                                            }
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }
                if($offset >= $res->totalRecords){
                    $loop = false;
                    break;
                }
                $offset = $offset+1000;
                echo "\n offset".$offset;
            }
        }       
         \Log::info("Items Imported  To NoLocation. ");  
        echo "job ended Suceessfully";

        curl_close($curl);
    }
}
