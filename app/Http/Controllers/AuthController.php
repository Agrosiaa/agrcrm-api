<?php
namespace App\Http\Controllers;

use App\Chat;
use App\Order;
use App\OrderHistory;
use App\OrderStatus;
use App\SalesUser;
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
            if($request->role_id == 1){
                $response['data']['pending_due_to_vendor'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','1')
                    ->where('orders.order_status_id','!=','7')
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['pending_for_vendor_cancel'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','2')
                    ->where('orders.order_status_id','!=','7')
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['pending_for_customer_cancel'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','3')
                    ->where('orders.order_status_id','!=','8')
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['pending_for_pickup'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','4')
                    ->where('orders.order_status_id','!=','7')
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['customer_issues'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->join('customer_issues','customer_issues.id','=','work_order_status_details.customer_issue_id')
                    ->where('work_order_status_details.work_order_status_id','=','5')
                    ->where('orders.order_status_id','!=','8')
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date',
                        'customer_issues.name')
                    ->get()->toArray();
                $response['data']['dispatch_orders'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','6')
                    ->where('orders.order_status_id','!=','7')
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
            }else{
                $response['data']['pending_due_to_vendor'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','1')
                    ->where('orders.order_status_id','!=','7')
                    ->where('orders.sales_id','=',$request->sales_id)
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['pending_for_vendor_cancel'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','2')
                    ->where('orders.order_status_id','!=','7')
                    ->where('orders.sales_id','=',$request->sales_id)
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['pending_for_customer_cancel'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','3')
                    ->where('orders.order_status_id','!=','8')
                    ->where('orders.sales_id','=',$request->sales_id)
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['pending_for_pickup'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','4')
                    ->where('orders.order_status_id','!=','7')
                    ->where('orders.sales_id','=',$request->sales_id)
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['customer_issues'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->join('customer_issues','customer_issues.id','=','work_order_status_details.customer_issue_id')
                    ->where('work_order_status_details.work_order_status_id','=','5')
                    ->where('orders.order_status_id','!=','8')
                    ->where('orders.sales_id','=',$request->sales_id)
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date',
                        'customer_issues.name')
                    ->get()->toArray();
                $response['data']['dispatch_orders'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','6')
                    ->where('orders.order_status_id','!=','7')
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
            }


        }catch(\Exception $e){
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
           $data['work_order_status_id'] = $request->work_order_status_id;
           $data['sales_id'] = $request->sales_id;
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
                $orderStatus = OrderStatus::where('slug', 'back_ordered')->first();
            }
            $order->update(array('order_status_id'=> $orderStatus->id,'updated_at' => $currentTime));
            $orderHistoryArray = array(
                'is_email_sent' => $is_email_sent,
                'order_id' => $request->order_id,
                'order_status_id' => $orderStatus->id,
                'user_id' => 4,
                'sales_id' => $request->sales_id,
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
    public function orderChats(Request $request){
        try{
                $status = 200;
                $chatHistoryData = array();
                $chatData = Chat::where('order_id',$request->order_id)->where('work_order_status_id',$request->work_order_status_id)->get()->toArray();
                $i = 0;
                foreach ($chatData as $value){
                    $chatHistoryData[$i]['userName'] = SalesUser::where('id',$value['sales_id'])->pluck('name')->first();
                    $chatHistoryData[$i]['time'] = $time = $this->humanTiming(strtotime($value['created_at']));
                    $chatHistoryData[$i]['message'] = $value['message'];
                    $chatHistoryData[$i]['order_id'] = $value['order_id'];
                    $i++;
                }
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
        return response()->json($chatHistoryData, $status);
    }
    function humanTiming ($time)
    {
        $time = time() - $time; // to get the time since that moment
        $time = ($time<1)? 1 : $time;
        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }

    }
}


