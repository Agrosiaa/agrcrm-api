<?php
namespace App\Http\Controllers;

use App\Chat;
use App\Customer;
use App\CustomerAddress;
use App\Order;
use App\OrderHistory;
use App\OrderStatus;
use App\PostOffice;
use App\Role;
use App\SalesUser;
use App\User;
use App\WorkOrderStatusDetail;
use Carbon\Carbon;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseController
{
    public function orderDetails(Request $request){
        try{
            $status = '200';
            $response = array();
            if($request->role_id == 1){
                $response['data']['pending_due_to_vendor'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','1')
                    ->whereNotIn('orders.order_status_id',[2,3,4,5,6,7,8,9,15])
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['pending_for_vendor_cancel'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','2')
                    ->whereNotIn('orders.order_status_id',[2,3,4,5,6,7,8,9,15])
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['pending_for_customer_cancel'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','3')
                    ->whereNotIn('orders.order_status_id',[5,6,8,9,15])
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['pending_for_pickup'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','4')
                    ->whereNotIn('orders.order_status_id',[5,6,8,9,15])
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['customer_issues'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->join('customer_issues','customer_issues.id','=','work_order_status_details.customer_issue_id')
                    ->where('work_order_status_details.work_order_status_id','=','5')
                    ->whereNotIn('orders.order_status_id',[4,5,6,8,9,15])
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date',
                        'customer_issues.name')
                    ->get()->toArray();
                $response['data']['dispatch_orders'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','6')
                    ->whereNotIn('orders.order_status_id',[1,2,3,4,6,7,9,15])
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
            }else{
                $response['data']['pending_due_to_vendor'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','1')
                    ->whereNotIn('orders.order_status_id',[2,3,4,5,6,7,8,9,15])
                    ->where('orders.sales_id','=',$request->sales_id)
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['pending_for_vendor_cancel'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','2')
                    ->whereNotIn('orders.order_status_id',[2,3,4,5,6,7,8,9,15])
                    ->where('orders.sales_id','=',$request->sales_id)
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['pending_for_customer_cancel'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','3')
                    ->whereNotIn('orders.order_status_id',[5,6,8,9,15])
                    ->where('orders.sales_id','=',$request->sales_id)
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['pending_for_pickup'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','4')
                    ->whereNotIn('orders.order_status_id',[5,6,8,9,15])
                    ->where('orders.sales_id','=',$request->sales_id)
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date')
                    ->get()->toArray();
                $response['data']['customer_issues'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->join('customer_issues','customer_issues.id','=','work_order_status_details.customer_issue_id')
                    ->where('work_order_status_details.work_order_status_id','=','5')
                    ->whereNotIn('orders.order_status_id',[4,5,6,8,9,15])
                    ->where('orders.sales_id','=',$request->sales_id)
                    ->select('orders.created_at','orders.sales_id','orders.consignment_number','work_order_status_details.order_id'
                        ,'work_order_status_details.work_order_status_id','work_order_status_details.role_id','work_order_status_details.updated_at as work_order_date',
                        'customer_issues.name')
                    ->get()->toArray();
                $response['data']['dispatch_orders'] = WorkOrderStatusDetail::join('orders','orders.id','=','work_order_status_details.order_id')
                    ->where('work_order_status_details.work_order_status_id','=','6')
                    ->whereNotIn('orders.order_status_id',[7,9,15])
                    ->where('orders.sales_id','=',$request->sales_id)
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
            $status = '500';
            $response = null;
        }
        return response()->json($response, $status);
    }

    public function createdCustomers(){
        try{
            $status = '200';
            $customerRole = Role::where('slug','=','customer')->value('id');
            $response = User::where('role_id','=',$customerRole)->pluck('mobile')->toArray();
        }catch (\Exception $e){
            $status = '500';
            $data = [
                'action' => 'Created Customers',
                'status' =>$status,
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
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
                    if($value['user_id'] != null){
                        $chatHistoryData[$i]['userName'] = \App\User::where('id',$value['user_id'])->pluck('first_name');
                    }else{
                        $chatHistoryData[$i]['userName'] = SalesUser::where('id',$value['sales_id'])->pluck('name');
                    }
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
    public function orderSearch(Request $request){
        try{
            $status = 200;
            $response = WorkOrderStatusDetail::where('order_id','=',$request->order)->with('orders')->get()->toArray();
        }catch(\Exception $exception){
            $status = 500;
            $data = [
                'action' => 'Order Search',
                'status' => $status,
                'exception' => $exception->getMessage()
            ];
            Log::critical(json_encode($data));
            $response = null;
        }
        return response()->json($response, $status);

    }

    public function createCustomer(Request $request){
        try{
            $status = 200;
            $userData['first_name'] = $request->name;
            $userData['email'] = $request->email;
            $userData['mobile'] = $request->mobile;
            $userData['dob'] = $request->dob;
            $userData['password'] = bcrypt($request->mobile);
            $userData['is_active'] = true;
            $userData['role_id'] = Role::where('slug','=','customer')->value('id');
            $user = User::create($userData);
            $customerData['user_id'] = $user->id;
            $customerData['is_web'] = true;
            $customer = Customer::create($customerData);
            $customerAddress['customer_id'] = $customer->id;
            $customerAddress['full_name'] = $request->name;
            $customerAddress['mobile'] = $request->mobile;
            $customerAddress['flat_door_block_house_no'] = $request->house_block;
            $customerAddress['name_of_premise_building_village'] = $request->village_premises;
            $customerAddress['area_locality_wadi'] = $request->area;
            $customerAddress['road_street_lane'] = $request->road_street;
            $customerAddress['at_post'] = $request->at_post;
            $customerAddress['taluka'] = $request->taluka;
            $customerAddress['district'] = $request->dist;
            $customerAddress['state'] = $request->state;
            $customerAddress['pincode'] = $request->pin;
            CustomerAddress::create($customerAddress);
        }catch(\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Create new customer',
                'status' =>$status,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $response = null;
        }
    }
    public function getPincode(Request $request){
        try {
            $status = 200;
            $requestPincode = trim($request['pincode']);
            if ($requestPincode == "" || $requestPincode == null) {
                $pincode = null;
            } else {
                $pincode = array();
                $pincodeData = PostOffice::where('pincode',$requestPincode)->select('state','office_name')->get();
                if(count($pincodeData) > 0){
                    foreach($pincodeData as $data){
                        if(array_key_exists($requestPincode,$pincode)){
                            $pincode[$requestPincode]['post_offices'] .= '<option value="'.$data->office_name.'">'.$data->office_name.'</option>';
                        }else{
                            $pincode[$requestPincode] = array();
                            $pincode[$requestPincode]['pincode'] = $requestPincode;
                            $pincode[$requestPincode]['post_offices'] = '<option value="'.$data->office_name.'">'.$data->office_name.'</option>';
                            $pincode[$requestPincode]['state'] = $data->state;
                        }
                    }
                }else{
                    $pincodeData = Curl::to('http://postalpincode.in/api/pincode/'.$requestPincode)->get();
                    $pincodeData = json_decode($pincodeData);
                    if($pincodeData->PostOffice != null){
                        foreach($pincodeData->PostOffice as $data){
                            if(array_key_exists($requestPincode,$pincode)){
                                $pincode[$requestPincode]['post_offices'] .= '<option value="'.$data->Name.'">'.$data->Name.'</option>';
                            }else{
                                $pincode[$requestPincode] = array();
                                $pincode[$requestPincode]['pincode'] = $requestPincode;
                                $pincode[$requestPincode]['post_offices'] = '<option value="'.$data->Name.'">'.$data->Name.'</option>';
                                $pincode[$requestPincode]['state'] = $data->State;
                            }
                        }
                    }else{
                        $pincode = null;
                    }
                }

            }
        }catch (\Exception $e){
            $status = 500;
            $pincode = null;
            $data = [
                'input_params' => $request->all(),
                'action' => 'get pincode',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        return response()->json($pincode,$status);
    }

    public function getPostOfficeInfo(Request $request,$postOffice){
        try{
            $postOffice = str_replace("%20"," ",$postOffice);
            $status = 200;
            $postOfficeInfo = PostOffice::where('pincode',$request->pincode)->where('office_name','ilike', trim($postOffice))->first();
            if($postOfficeInfo != null){
                $response = [
                    'taluka' => $postOfficeInfo->taluka,
                    'district' => $postOfficeInfo->district
                ];
            }else{
                $postOffice = str_replace(" ","%20",$postOffice);
                $postOfficeResponse = Curl::to('http://postalpincode.in/api/postoffice/'.($postOffice))->get();
                $postOfficeResponse = json_decode($postOfficeResponse);
                $response = array();
                if($postOfficeResponse->PostOffice != null){
                    foreach($postOfficeResponse->PostOffice as $postOffice){
                        $response = [
                            'taluka' => $postOffice->Taluk,
                            'district' => $postOffice->District
                        ];
                    }
                }
            }

        }catch (\Exception $e){
            $status = 500;
            $response = null;
            $data = [
                'input_params' => $request->all(),
                'action' => 'Get post office info',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        return response()->json($response,$status);
    }
}


