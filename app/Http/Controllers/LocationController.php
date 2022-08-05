<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function getPaginatedLocations()
    {
        $locations = Location::paginate(10);
        return response()->json($locations, 200);
    }

    public function saveLocation(Request $request)
    {
        $msg = isset($request['id']) ? 'updated' : 'created';
        $arrDetail = array(
            'name' => $request['name'],
            'code' => $request['code']
        );
        $item = Location::updateOrCreate(['id' => $request['id']], $arrDetail);

        return response()->json([
            "message" => "Location has been ".$msg
        ], 200);
    }

    public function importLocations(Request $request)
    {
        $msg = "";
        $statusCode = 200;
        $locationsToImport = json_decode($request['import_data']);

        DB::beginTransaction();
        try {
            foreach (array_chunk($locationsToImport, 1000) as $locationsToImport_chunked){
                $itemArr = array();
                foreach ($locationsToImport_chunked as $item) {
                    array_push($itemArr, array(
                        'name' => $item->name,
                        'code' => $item->code,
                        )
                    );
                }
                $import = Location::upsert(
                    $itemArr,
                    ['code'],
                    ['name']
                );
                $msg = $import ? "Import success" : "Import failed";
                // Log::create([
                //     'user_id' => 0,
                //     'log_type' => 'run-cron',
                //     'details' => json_encode($toAddMasterItem_chunked)
                // ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $statusCode = 500;
            $msg = "Import failed";
        }

        return response()->json([
            "message" => $msg
        ], $statusCode);
    }
}
