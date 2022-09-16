<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\OrderSubmissionMail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Events\OrderSubmissionEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as Img;

class OrderController extends Controller
{
    public function removeOrderDetail(Request $request)
    {
        $removeItemFromOrder = OrderDetail::where('id', $request['order_detail_id'])->delete();
        return response()->json([
            "message" => "Item has been deleted"
        ], 200);
    }

    public function getPaginatedOrdersForAdmin($status = "draft")
    {
        $orders = Order::where('status', $status)->with('user.profile', 'location', 'order_details', 'files')->latest()->paginate(10);
        // $orders = Order::where('status', 'submitted')->paginate(10);
        return response()->json($orders, 200);
    }

    public function getFilteredPaginatedOrdersForAdmin($status = "draft", $customer_code, $sales_rep_id)
    {
        // orders/get/paginated/{status?}/customer/{customer_id}/sales_rep/{sales_rep_id}
        $filteredOrders = new Order;
        $filteredOrders = $filteredOrders->where('status', $status);
        if($customer_code !== "ALL"){ // query with locations/customers
            $filteredOrders = $filteredOrders->whereHas('location', function ($query) use ($customer_code) {
                $query->where('code', $customer_code);
            });
        }
        if($sales_rep_id !== "0"){ // query with sales rep
            $filteredOrders = $filteredOrders->whereHas('user', function ($query) use ($sales_rep_id) {
                $query->where('id', $sales_rep_id);
            });
        }
        $resultCount = $filteredOrders->count();
        $filteredOrders = $filteredOrders->with('user.profile', 'location', 'order_details', 'files')->latest();
        $filteredOrders = $filteredOrders->paginate(10);
        return response()->json([
            "message" => "Orders has been fetched",
            "orders" => $filteredOrders,
            "result_count" => $resultCount
        ], 200);
    }


    public function getPaginatedOrders($status = "draft")
    {
        $orders = Order::where([
            'status' => $status,
            'user_id' => auth()->id(),
        ])->latest()->with('user.profile', 'location', 'order_details')->paginate(10);
        return response()->json($orders, 200);
    }

    public function addOrderDetail(Request $request)
    {
        $order = Order::where("order_number", $request['order_number'])->firstOrFail();
        $orderDetailArr = array(
            "sku" => $request['sku'],
            "item_id" => $request['item_id'],
            "item_name" => $request['item_name'],
            "uom" => $request['uom'],
            "non_foc_quantity" => $request['non_foc_quantity'],
            "foc_quantity" => $request['foc_quantity'],
            "total_quantity" => $request['total_quantity'],
            "price" => $request['price'],
            "line_price" => $request['line_price'],
            "remarks" => $request['remarks'],
        );
        if(isset($request['order_detail_id']) && $request['order_detail_id'] != null){
            $order->order_details()->where("id", $request['order_detail_id'])->update($orderDetailArr);
            // OrderDetail::where("id", $request['order_detail_id'])->update($orderDetailArr);
        }else{
            $order->order_details()->create($orderDetailArr);
        }
        // $order->order_details()->updateOrCreate(["item_id" => $request['item_id']], $orderDetailArr);

        return response()->json($order, 200);
    }

    public function getSingleOrder($ordernum)
    {
        $order = Order::where([
            "order_number" => $ordernum,
            "user_id" => auth()->id()
        ])->with('order_details', 'location', 'files')->first();
        return response()->json($order, 200);
    }

    public function updateERPOrder(Request $request)
    {
        $order = Order::where('id', $request['order_id'])->update([
            "status" => $request['erp'] == true ? 'completed' : 'submitted',
            "erp" => $request['erp']
        ]);
        return response()->json([
            'message' => "Order has been updated"
        ], 200);
    }

    public function updateOrder(Request $request)
    {

        $message = "Order has been updated";
        $responseCode = 200;

        $idsToSync = array();
        $fileArray = array();
        $uploadDate = Carbon::now()->format('YmdHis');
        $files = Collection::wrap(request()->file('file'));
        $userStorage = '/uploads';
        if (!Storage::exists($userStorage)) {
            Storage::makeDirectory($userStorage, 0755, true);
        }

        if(request()->file('file')){
            $orderRequest = json_decode($request['order']);
        }else{
            $orderRequest = $request;
        }

        DB::beginTransaction();
        try {
            if(request()->file('file')){
                $files->each(function ($file, $key) use (&$userStorage, &$fileArray, &$uploadDate) {
                    $userStorageDir = storage_path() . '/app' . $userStorage;
                    $fileName = $file->getClientOriginalName();
                    $title = pathinfo($fileName, PATHINFO_FILENAME);
                    $extn = strtolower($file->getClientOriginalExtension());
                    $slugTitle = Str::slug($title, '-');
                    $path = $slugTitle."-".$uploadDate.".".$extn;
                    $mime = $file->getClientMimeType();

                    if($extn != 'pdf'){
                        // File Optimization
                        $img = Img::make($file);
                        $img->encode($extn, 50);

                        // Save file to storage directory
                        $img->save($userStorageDir . '/' . $path);
                    }else{
                        $file->move($userStorageDir, $path);
                    }
                    $file_type = $extn == 'pdf' ? 'doc' : 'image';

                    // Setup data into array
                    array_push( $fileArray, array(
                        'original_name' => $fileName,
                        'title' => $title,
                        'disk' => 'local',
                        'path' => $path,
                        'file_type' => $file_type,
                        'mime' => $mime,
                        'user_id' => auth()->id(),
                        'created_at' => Carbon::now(),
                    ));
                });

                // Insert into database at once
                // $uploadedFiles = File::insert($fileArray);

                // Recursive create
                foreach ($fileArray as $singleFile) {
                    $id = DB::table('files')->insertGetId($singleFile);
                    array_push($idsToSync, $id);
                }
            }

            // $orderArr = array(
            //     'status' => $request['status'],
            //     'is_cash_sale' => $request['is_cash_sale'],
            //     'cash_sale_customer' => $request['is_cash_sale'] == true ? $request['cash_sale_customer'] : "",
            //     'location_id' => $request['is_cash_sale'] == false ? $request['location_id'] : null,
            //     'user_id' => auth()->id()
            // );
            $orderArr = array(
                'instructions' => $orderRequest->instructions,
                'status' => $orderRequest->status,
                'is_cash_sale' => $orderRequest->is_cash_sale,
                'cash_sale_customer' => $orderRequest->is_cash_sale == true ? $orderRequest->cash_sale_customer : "",
                'location_id' => $orderRequest->is_cash_sale == false ? $orderRequest->location_id : null,
                'user_id' => auth()->id()
            );
            $order = Order::where('order_number', $orderRequest->order_number)->update($orderArr);
            $theOrder = Order::where('order_number', $orderRequest->order_number)->firstOrFail();

            // Sync files to customer feedback
            if(request()->file('file')){
                if($theOrder){
                    $theOrder->files()->sync($idsToSync);
                }
            }else{
                if($orderRequest->remove_file == true){
                    $theOrder->files()->detach();
                }
            }

            // email notification
            // if($order && $orderRequest->status == 'submitted'){
            //     // get all admin
            //     $recipients = User::where(['role' => 'admin', 'status' => 'active'])->get();

            //     event(new OrderSubmissionEvent($recipients, $theOrder));
            // }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $responseCode = 500;
            $message = "Error saving order";
        }

        return response()->json([
            'message' => $message
        ], $responseCode);
    }

    public function createOrder(Request $request)
    {
        // $max = Order::max('id');
        $latestID = Order::latest()->limit(1)->get();
        $incremental = 1;
        if(count($latestID) == 1){
            $incremental = $latestID[0]->id + 1;
        }
        $order = Order::create([
            'status' => 'draft',
            'order_number' => auth()->id()."-".date('dmy')."-".$incremental,
            'user_id' => auth()->id()
        ]);
        return response()->json($order, 200);
    }

    public function saveOrder(Request $request)
    {
        $msg = isset($request['id']) ? 'updated' : 'created';
        $orderArr = array(
            'status' => $request['status'],
            'order_number' => $request['item_name'],
            'location_id' => $request['location_id'],
            'instructions' => $request['instructions'],
            'user_id' => auth()->id(),
        );
        $orderDetailsArr = array(
            'item_id' => $request['item_id'],
            'sku' => $request['sku'],
            'item_name' => $request['item_name'],
            'non_foc_quantity' => $request['non_foc_quantity'],
            'foc_quantity' => $request['foc_quantity'],
            'foc_quantity' => $request['foc_quantity'],
            'total_quantity' => $request['total_quantity'],
            'uom' => $request['uom'],
            'price' => $request['price'],
            'line_price' => $request['line_price'],
            'remarks' => $request['remarks'],
        );
        $order = Order::updateOrCreate(['id' => $request['id']], $orderArr);
        $order->order_details()->create($orderDetailsArr);

        return response()->json([
            "message" => "Item has been ".$msg
        ], 200);
    }

}
