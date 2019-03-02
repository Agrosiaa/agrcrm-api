<?php
namespace App\Http\Controllers;

use App\Chat;
use App\Order;
use App\OrderHistory;
use App\OrderStatus;
use App\WorkOrderStatusDetail;
use Carbon\Carbon;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseController
{
    public function orderDetails(Request $request){
        try{
            $status = 200;
            $response = array();
            $response['data']['pending_due_to_vendor'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                                                        ->where('work_order_status_details.work_order_status_id','=','1')
                                                        ->where('orders.order_status_id','!=','9')
                                                        ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                                                            ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.created_at as work_order_date')
                                                        ->get()->toArray();
            $response['data']['pending_for_vendor_cancel'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                                                            ->where('work_order_status_details.work_order_status_id','=','2')
                                                            ->where('orders.order_status_id','!=','9')
                                                            ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                                                                ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.created_at as work_order_date')
                                                            ->get()->toArray();
            $response['data']['pending_for_customer_cancel'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                                                            ->where('work_order_status_details.work_order_status_id','=','3')
                                                            ->where('orders.order_status_id','!=','8')
                                                            ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                                                                ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.created_at as work_order_date')
                                                            ->get()->toArray();
            $response['data']['pending_for_pickup'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                                                            ->where('work_order_status_details.work_order_status_id','=','4')
                                                            ->where('orders.order_status_id','!=','9')
                                                            ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                                                                ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.created_at as work_order_date')
                                                            ->get()->toArray();
            $response['data']['customer_issues'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                                                            ->where('work_order_status_details.work_order_status_id','=','5')
                                                            ->where('orders.order_status_id','!=','8')
                                                            ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                                                                ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.created_at as work_order_date')
                                                            ->get()->toArray();
            $response['data']['dispatch_orders'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                                                            ->where('work_order_status_details.work_order_status_id','=','6')
                                                            ->where('orders.order_status_id','!=','9')
                                                            ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                                                                ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.created_at as work_order_date')
                                                            ->get()->toArray();
        }catch (\Exception $e){
            $data = [
                'action' => 'Work order status details',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = null;
        }
        return response()->json($response, $status);
    }
    public function orderReply(Request $request){
        try{
           $status = 200;
           $data = array();
           $data['order_id'] = $request->order_id;
           $data['message'] = $request->reply_message;
           $query = Chat::create($data);

        }catch(\Exception $e){
            $status = 500;
            $data = [
                'action' => 'reply data',
                'status' =>$status,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $response = null;
        }
    }
    public function orderCancel(Request $request){
        try{
            $is_email_sent = 0;
            $currentTime = Carbon::now();
            $order = Order::findOrFail($request->order_id);
            $workOrderStatus = WorkOrderStatusDetail::where('order_id',$request->order_id)->first();
            if($workOrderStatus->work_order_status_id == 3 || $workOrderStatus->work_order_status_id == 5){
                $orderStatus = OrderStatus::where('slug', 'cancel')->first();
            }else{
                $orderStatus = OrderStatus::where('slug', 'declined')->first();
            }
            $order->update(array('order_status_id'=> $orderStatus->id,'updated_at' => $currentTime));
            $orderHistoryArray = array(
                'is_email_sent' => $is_email_sent,
                'order_id' => $request->order_id,
                'order_status_id' => $orderStatus->id,
                'user_id' => 4,
                'reason' => $request->cancel_text,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            );
            $query = OrderHistory::create($orderHistoryArray);

        }catch(\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Order Cancel',
                'status' => $status,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $response = null;
        }
    }
    public function orderChats(Request $request,$id){
        try{
                $status = 200;
                $response = Chat::where('order_id',$id)->get();
        }catch(\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Order Cancel',
                'status' => $status,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $response = null;
        }
        return response()->json($response, $status);
    }
}


