<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function getPaginatedItems()
    {
        $items = Item::paginate(10);
        return response()->json($items, 200);
    }

    public function saveItem(Request $request)
    {
        $msg = isset($request['id']) ? 'updated' : 'created';
        $arrDetail = array(
            'name' => $request['name'],
            'sku' => $request['sku'],
            'price' => $request['price'],
        );
        $item = Item::updateOrCreate(['id' => $request['id']], $arrDetail);

        return response()->json([
            "message" => "Item has been ".$msg
        ], 200);
    }

    public function importItems(Request $request)
    {
        $msg = "";
        $statusCode = 200;
        $itemsToImport = json_decode($request['import_data']);

        DB::beginTransaction();
        try {
            foreach (array_chunk($itemsToImport, 500) as $itemsToImport_chunked)
            {

                $itemArr = array();
                foreach ($itemsToImport_chunked as $item) {
                    array_push($itemArr, array(
                        'name' => $item->item_name,
                        'sku' => $item->sku,
                        'price' => $item->price
                        )
                    );
                    // Item::updateOrCreate(
                    //     [
                    //         'sku' => $item->sku,
                    //     ],
                    //     [
                    //         'name' => $item->item_name,
                    //         'sku' => $item->sku,
                    //         'price' => $item->price
                    //     ]
                    // );

                }
                dd($itemArr);
                Item::upsert(
                    $itemArr,
                    ['sku'],
                    ['name', 'price']
                );
                // Log::create([
                //     'user_id' => 0,
                //     'log_type' => 'run-cron',
                //     'details' => json_encode($toAddMasterItem_chunked)
                // ]);
            }
            DB::commit();
        } catch (Exception $e) {
            dd('rollback');
            DB::rollback();
        }

        return response()->json([
            "message" => $msg
        ], $statusCode);
    }
}
