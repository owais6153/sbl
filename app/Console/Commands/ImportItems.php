<?php

namespace App\Console\Commands;

use App\Models\ItemChildren;
use Illuminate\Console\Command;
use App\Models\Items;
use App\Models\ItemsCron;
use App\Models\ItemIdentifier;
use App\Models\Itemlisting;
use App\Models\SkippedItemIdentifiers;
use League\Fractal\Resource\Item;
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
                    foreach ($res->data as $item) {

                        $checkItem = Items::where('itemNumber', '=', $item->itemNumber)->count();
                        if ($checkItem < 1) {
                            $items = new Items();
                            $items->item_number = $item->itemNumber;
                            $items->avg_cost = $item->avgCost;
                            $items->avg_cost_source = $item->avgCostSource;
                            if(!empty($item->inventory)){
                                foreach($item->inventory as $inventory){
                                    if (isset($inventory->warehouse) && $inventory->warehouse == "Default Ridgefield") {
                                        $inventory->ridgefield_onhand = $inventory->onHand;
                                        break;
                                    }
                                }
                            }
                            $items->save();
                            if(!empty($item->listings)){
                                foreach($item->listings as $listings){
                                    $list = new Itemlisting();
                                    $list->item_id =$items->id;
                                    $list->_id =$listings->_id;
                                    $list->storeSKU =$listings->storeSKU;
                                    $list->listingId =$listings->listingId;
                                    $list->fnSKU =$listings->fnSKU;
                                    $list->listingName =$listings->listingName;
                                    $list->store =$listings->store;
                                    $list->urlId =$listings->urlId;
                                    $list->fulfilledBy =$listings->fulfilledBy;
                                    $list->save();
                                }
                            }
                            if(!empty($item->productChildren)){
                                foreach($item->productChildren as $pchild){
                                    $child = new ItemChildren();
                                    $child->kit_item_id =$items->id;
                                    $check = Items::where('item_number',$pchild->childItemNumber)->first();
                                    if($check){
                                        $child->child_item_id =$check->id;
                                    }
                                    $child->qty =$pchild->childQuantity;
                                    $child->save();
                                }
                            }
                            
                            foreach ($item->productIdentifiers as $productIdentifier){
                                if ($productIdentifier->identifierType == 'UPC') {
                                    $check = ItemIdentifier::where('productIdentifier', '=', $productIdentifier->productIdentifier)->count();
                                    if($check < 1){
                                        $ItemIdentifier = new ItemIdentifier();
                                        $ItemIdentifier->item_id = $items->id;
                                        $ItemIdentifier->productIdentifier = $productIdentifier->productIdentifier;
                                        $ItemIdentifier->save();
                                    }
                                    else{

                                        $getIdentifier = ItemIdentifier::select('item_id', 'id', 'productIdentifier')->where('productIdentifier', '=', $productIdentifier->productIdentifier)->first();
                                        $SkippedItemIdentifiers = new SkippedItemIdentifiers();
                                        $SkippedItemIdentifiers->item_id = $getIdentifier->item_id;
                                        $SkippedItemIdentifiers->identifier_id = $getIdentifier->id;
                                        $SkippedItemIdentifiers->barcode = $getIdentifier->productIdentifier;
                                        $SkippedItemIdentifiers->duplicate_item_id = $items->id;
                                        $SkippedItemIdentifiers->save();

                                    }
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
