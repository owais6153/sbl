<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;
use App\Models\Items;
use App\Models\ReplenBatch;
use App\Models\ReplenDetail;
use Log;
class ReplenImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $timeout = 0;
    public $replenbatch_id;
    public function __construct($replenbatch_id)
    {
        $this->replenbatch_id = $replenbatch_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        $limit = 200;
        $offset = 0;
        $totalItemsInDB = Items::where('ridgefield_onhand', '!=', null)->where('ridgefield_onhand', '>', 0)->count();
        while($totalItemsInDB > $offset){
            $Items = Items::where('ridgefield_onhand', '!=', null)->where('ridgefield_onhand', '>', 0)->limit($limit)->offset($offset)->get();
            if (count($Items) < 1) {
                continue;
            }

            $itemsSepratedByCommas = '[';

            foreach ($Items as $itemIndex => $item){  
                $itemsSepratedByCommas .= '"' . $item->item_number . '"';
                $itemsSepratedByCommas .= (isset($Items[$itemIndex + 1])) ? ',' : '';
            }

            $itemsSepratedByCommas .= ']';

            $itemsSepratedByCommas = urlencode($itemsSepratedByCommas);

            // Total Items
            $getTotalInventoriesByCurl = curl_init();
            curl_setopt_array($getTotalInventoriesByCurl, array(
              CURLOPT_URL => 'https://api.gorillaroi.com/V1/Inventory?sellerId=A16HZ3J3WEU71V&email=joe@lybcorp.com&marketplace=US&asin=' . $itemsSepratedByCommas,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Basic djlvc1N1Yyt6T3MyNGJxLzl1Ry9zZ1YvaXg4bytuY2U=',
                'Cookie: NB_SRVID=srv328360'
              ),
            ));
            $getTotalInventoriesByCurlResponse = curl_exec($getTotalInventoriesByCurl);

            if (curl_errno($getTotalInventoriesByCurl)) {
                $error_msg = curl_error($getTotalInventoriesByCurl);
                \Log::info("Curl error in Replen Import Queue " . $error_msg . '. Batch ID: '. $this->replenbatch_id. " Failed to import. Curl: 1");
                $ReplenBatch = ReplenBatch::find($this->replenbatch_id)->update(['status' => 'error']);
                continue;
            }
            else{
                $getTotalInventoriesByCurlRes = json_decode($getTotalInventoriesByCurlResponse);
                if (!isset($getTotalInventoriesByCurlRes->Status)) {
                   continue;
                }
                if ($getTotalInventoriesByCurlRes->Status != 'Success' ) {
                    \Log::info("API Response Status is: ". $getTotalInventoriesByCurlRes->Status . " Batch ID: ". $this->replenbatch_id. " Failed to import. Curl: 1");
                    $ReplenBatch = ReplenBatch::find($this->replenbatch_id)->update(['status' => 'error']);
                    continue;
                }
                else{
                     \Log::info('Total Inventory API Results'. count($getTotalInventoriesByCurlRes->Results));
                    $getTotalUnsellableByCurl = curl_init();

                    curl_setopt_array($getTotalUnsellableByCurl, array(
                      CURLOPT_URL => 'https://api.gorillaroi.com/V1/Inventory?sellerId=A16HZ3J3WEU71V&email=joe@lybcorp.com&marketplace=US&asin='.$itemsSepratedByCommas.'&status=unsellable',
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'GET',
                      CURLOPT_HTTPHEADER => array(
                        'Authorization: Basic djlvc1N1Yyt6T3MyNGJxLzl1Ry9zZ1YvaXg4bytuY2U=',
                        'Cookie: NB_SRVID=srv328360'
                      ),
                    ));

                    $getTotalUnsellableByCurlResponse = curl_exec($getTotalUnsellableByCurl);
                    if (curl_errno($getTotalUnsellableByCurl)) {
                        $error_msg = curl_error($getTotalUnsellableByCurl);
                        \Log::info("Curl error in Replen Import Queue " . $error_msg . '. Batch ID: '. $this->replenbatch_id. " Failed to import. Curl: 2");
                        $ReplenBatch = ReplenBatch::find($this->replenbatch_id)->update(['status' => 'error']);
                        continue;
                    }
                    else{
                        $getTotalUnsellableByCurlRes = json_decode($getTotalUnsellableByCurlResponse);
                        if (!isset($getTotalUnsellableByCurlRes->Status)) {
                           continue;
                        }
                        if ($getTotalUnsellableByCurlRes->Status != 'Success') {
                            \Log::info("API Response Status is: ". $getTotalUnsellableByCurlRes->Status . " Batch ID: ". $this->replenbatch_id. " Failed to import. Curl: 2");
                            $ReplenBatch = ReplenBatch::find($this->replenbatch_id)->update(['status' => 'error']);
                            continue;
                        }
                        else{

                         \Log::info('Total Unsellable API Results'. count($getTotalUnsellableByCurlRes->Results));

                            $getLast30DaysSaleBycurl = curl_init();

                            curl_setopt_array($getLast30DaysSaleBycurl, array(
                              CURLOPT_URL => 'https://api.gorillaroi.com/V1/SalesCount?sellerId=A16HZ3J3WEU71V&email=joe@lybcorp.com&marketplace=US&period=last%2030%20days&asin=' . $itemsSepratedByCommas,
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_ENCODING => '',
                              CURLOPT_MAXREDIRS => 10,
                              CURLOPT_TIMEOUT => 0,
                              CURLOPT_FOLLOWLOCATION => true,
                              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                              CURLOPT_CUSTOMREQUEST => 'GET',
                              CURLOPT_HTTPHEADER => array(
                                'Authorization: Basic djlvc1N1Yyt6T3MyNGJxLzl1Ry9zZ1YvaXg4bytuY2U=',
                                'Cookie: NB_SRVID=srv328360'
                              ),
                            ));

                            $getLast30DaysSaleBycurlResponse = curl_exec($getLast30DaysSaleBycurl);
                            if (curl_errno($getLast30DaysSaleBycurl)) {
                                $error_msg = curl_error($getLast30DaysSaleBycurl);
                                \Log::info("Curl error in Replen Import Queue " . $error_msg . '. Batch ID: '. $this->replenbatch_id. " Failed to import. Curl: 3");
                                $ReplenBatch = ReplenBatch::find($this->replenbatch_id)->update(['status' => 'error']);
                                continue;
                            }
                            else{
                                $getLast30DaysSaleBycurlRes = json_decode($getLast30DaysSaleBycurlResponse);
                                if (!isset($getLast30DaysSaleBycurlRes->Status)) {
                                   continue;
                                }
                                if ($getLast30DaysSaleBycurlRes->Status != 'Success') {
                                    \Log::info("API Response Status is: ". $getLast30DaysSaleBycurlRes->Status . " Batch ID: ". $this->replenbatch_id. " Failed to import. Curl: 3");
                                    $ReplenBatch = ReplenBatch::find($this->replenbatch_id)->update(['status' => 'error']);
                                    continue;
                                }
                                else{

                                 \Log::info('Total 30 days sale API Results'. count($getLast30DaysSaleBycurlRes->Results));
                                    foreach($Items as $key => $item){
                                        $item_id = $unsellable = $totalInventory = $last30DaysSale = $best_replen = $replen_amount = $itemInAmazonWithouUnsellable = 0;
                                        $ridgefield_onhand = ($item->ridgefield_onhand != null) ? $item->ridgefield_onhand : 0;
                                        $item_id = $item->id;
                                        $totalInventory = (isset($getTotalInventoriesByCurlRes->Results[$key])) ? $getTotalInventoriesByCurlRes->Results[$key] : 0;
                                        $unsellable = (isset($getTotalUnsellableByCurlRes->Results[$key])) ? $getTotalUnsellableByCurlRes->Results[$key] : 0;
                                        $last30DaysSale = (isset($getLast30DaysSaleBycurlRes->Results[$key])) ? $getLast30DaysSaleBycurlRes->Results[$key] : 0;
                                        $itemInAmazonWithouUnsellable = $totalInventory - $unsellable;

                                        $best_replen = ($last30DaysSale * 2.5) - $itemInAmazonWithouUnsellable;
                                        // Get Replen amount
                                        if ($best_replen > 0) {
                                            if ($best_replen <= $ridgefield_onhand) {
                                                $replen_amount = $best_replen;
                                            }
                                            else{
                                                $replen_amount = $ridgefield_onhand;
                                            }
                                        }

                                        $ReplenDetail = new ReplenDetail();
                                        $ReplenDetail->item_id = $item_id;
                                        $ReplenDetail->item_name = $item->item_number;
                                        // $ReplenDetail->urlid = $item->urlid;
                                        // $ReplenDetail->store_sku = $item->store_sku;
                                        // $ReplenDetail->store = $item->store;
                                        $ReplenDetail->days_30_sales = $last30DaysSale;
                                        $ReplenDetail->amazon_inventory = $totalInventory;
                                        $ReplenDetail->unsellable = $unsellable;
                                        $ReplenDetail->on_hand_ridgefield = $ridgefield_onhand;
                                        $ReplenDetail->amount_to_replen = $replen_amount;
                                        $ReplenDetail->replen_batch_id = $this->replenbatch_id;
                                        $ReplenDetail->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            $offset = $offset + 200;
        }
        $ReplenBatch = ReplenBatch::find($this->replenbatch_id)->update(['status' => 'completed']); 
    }

    
}
