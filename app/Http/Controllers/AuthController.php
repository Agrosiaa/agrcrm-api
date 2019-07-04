<?php
namespace App\Http\Controllers;

use App\AppUsers;
use App\Brand;
use App\Category;
use App\Chat;
use App\Customer;
use App\CustomerAddress;
use App\DeliveryType;
use App\Holidays;
use App\Order;
use App\OrderCustomerRelation;
use App\OrderHistory;
use App\OrderStatus;
use App\PaymentMethods;
use App\PostOffice;
use App\Product;
use App\ProductCategoryRelation;
use App\ProductImage;
use App\Role;
use App\SalesUser;
use App\Seller;
use App\SellerAddress;
use App\ShippingMethod;
use App\Tax;
use App\User;
use App\WorkOrderStatusDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Ixudra\Curl\Facades\Curl;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CustomTraits\SendMessageTrait;
use App\Http\Controllers\CustomTraits\DeliveryTrait;


class AuthController extends BaseController
{
    use SendMessageTrait;
    use DeliveryTrait;
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
                'action' => 'Created Customer',
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
            $userData['first_name'] = $request->fname;
            $userData['last_name'] = $request->lname;
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
            if($request->has('house_block') && $request->has('village_premises')){
                if($request->house_block != '' && $request->village_premises != ''){
                    $customerAddress['customer_id'] = $customer->id;
                    $customerAddress['full_name'] = $request->address_fname;
                    $customerAddress['mobile'] = $request->address_mobile;
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
                }
            }
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

    public function customerProfile(Request $request)
    {
        try {
            $status = '200';
            $response['profile'] = User::where('mobile', $request->mobile)->first();
            $response['address'] = User::join('customers', 'customers.user_id', '=', 'users.id')
                ->join('customer_addresses', 'customer_addresses.customer_id', '=', 'customers.id')
                ->where('users.mobile', $request->mobile)
                ->select('customer_addresses.*')
                ->get()->toArray();
            $response['orders'] = User::join('customers', 'customers.user_id', '=', 'users.id')
                ->join('orders', 'orders.customer_id', '=', 'customers.id')
                ->join('products', 'orders.product_id', '=', 'products.id')
                ->join('order_status', 'orders.order_status_id', '=', 'order_status.id')
                ->join('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
                ->where('users.mobile', $request->mobile)
                ->select('orders.id', 'orders.quantity', 'orders.created_at', 'orders.subtotal', 'orders.consignment_number', 'products.product_name', 'order_status.status', 'payment_methods.name as payment_mode')
                ->orderBy('orders.created_at','desc')
                ->get()->toArray();
            $response['returns'] = User::join('customers', 'customers.user_id', '=', 'users.id')
                ->join('orders', 'orders.customer_id', '=', 'customers.id')
                ->join('order_rma', 'order_rma.order_id', '=', 'orders.id')
                ->join('order_status', 'orders.order_status_id', '=', 'order_status.id')
                ->join('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
                ->where('users.mobile', $request->mobile)
                ->select('orders.id', 'orders.quantity', 'orders.created_at', 'orders.subtotal', 'orders.consignment_number', 'order_rma.product_name', 'order_status.status', 'payment_methods.name as payment_mode')
                ->orderBy('orders.created_at','desc')
                ->get()->toArray();
            $response['deliveryTypes'] = DeliveryType::all();
            $response['paymentTypes'] = PaymentMethods::all();
        } catch (\Exception $e) {
            $status = '500';
            $data = [
                'action' => 'Created Customer',
                'status' => $status,
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
            $response = null;
        }
        return response()->json($response, $status);
    }

    public function customerOrders(Request $request)
    {
        try {
            $status = '200';
            if($request->has('retrieve') && $request->retrieve == 'ids'){
                $response['orders'] = User::join('customers', 'customers.user_id', '=', 'users.id')
                    ->join('orders', 'orders.customer_id', '=', 'customers.id')
                    ->join('products', 'orders.product_id', '=', 'products.id')
                    ->join('order_status', 'orders.order_status_id', '=', 'order_status.id')
                    ->join('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
                    ->where('users.mobile', $request->mobile)
                    ->orderBy('orders.created_at','desc')
                    ->pluck('orders.id');
            }
            if($request->has('retrieve') && $request->retrieve == 'data'){
                $response = User::join('customers', 'customers.user_id', '=', 'users.id')
                    ->join('orders', 'orders.customer_id', '=', 'customers.id')
                    ->join('products', 'orders.product_id', '=', 'products.id')
                    ->join('order_status', 'orders.order_status_id', '=', 'order_status.id')
                    ->join('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
                    ->where('users.mobile', $request->mobile)
                    ->whereIn('orders.id', $request->filteredIds)
                    ->select('orders.id', 'orders.quantity', 'orders.created_at', 'orders.subtotal', 'orders.consignment_number', 'products.product_name', 'order_status.display_name as status', 'payment_methods.name as payment_mode','products.seller_sku')
                    ->orderBy('orders.created_at','desc')
                    ->get()->toArray();
            }

            if($request->has('filter') && $request->filter == true){
                if($request->has('ids') && !empty($request->ids)) {
                    $resultFlag = true;
                    // Search customer mobile number
                    if ($request->has('order_no') && $request->order_no != "") {
                        $response['orders'] = User::join('customers', 'customers.user_id', '=', 'users.id')
                            ->join('orders', 'orders.customer_id', '=', 'customers.id')
                            ->join('products', 'orders.product_id', '=', 'products.id')
                            ->join('order_status', 'orders.order_status_id', '=', 'order_status.id')
                            ->join('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
                            ->where('users.mobile', $request->mobile)
                            ->where('orders.id', $request->order_no)
                            ->whereIn('orders.id',$request->ids)
                            ->orderBy('orders.created_at','desc')
                            ->pluck('orders.id');
                        if (!empty($response['orders'])) {
                            $resultFlag = false;
                        }
                    }
                    if ($request->has('product') && $request->product != "") {
                        $response['orders'] = User::join('customers', 'customers.user_id', '=', 'users.id')
                            ->join('orders', 'orders.customer_id', '=', 'customers.id')
                            ->join('products', 'orders.product_id', '=', 'products.id')
                            ->join('order_status', 'orders.order_status_id', '=', 'order_status.id')
                            ->join('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
                            ->where('users.mobile', $request->mobile)
                            ->where('products.product_name','ilike','%'.$request->product.'%')
                            ->whereIn('orders.id',$request->ids)
                            ->orderBy('orders.created_at','desc')
                            ->pluck('orders.id');
                        if (!empty($response['orders'])) {
                            $resultFlag = false;
                        }
                    }
                    if ($request->has('quantity') && $request->quantity != "") {
                        $response['orders'] = User::join('customers', 'customers.user_id', '=', 'users.id')
                            ->join('orders', 'orders.customer_id', '=', 'customers.id')
                            ->join('products', 'orders.product_id', '=', 'products.id')
                            ->join('order_status', 'orders.order_status_id', '=', 'order_status.id')
                            ->join('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
                            ->where('users.mobile', $request->mobile)
                            ->where('orders.quantity',$request->quantity)
                            ->whereIn('orders.id',$request->ids)
                            ->orderBy('orders.created_at','desc')
                            ->pluck('orders.id');
                        if (!empty($response['orders'])) {
                            $resultFlag = false;
                        }
                    }

                    if ($request->has('skuid') && $request->skuid != "") {
                        $response['orders'] = User::join('customers', 'customers.user_id', '=', 'users.id')
                            ->join('orders', 'orders.customer_id', '=', 'customers.id')
                            ->join('products', 'orders.product_id', '=', 'products.id')
                            ->join('order_status', 'orders.order_status_id', '=', 'order_status.id')
                            ->join('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
                            ->where('users.mobile', $request->mobile)
                            ->where('products.seller_sku','ilike','%'.$request->skuid.'%')
                            ->whereIn('orders.id',$request->ids)
                            ->orderBy('orders.created_at','desc')
                            ->pluck('orders.id');
                        if (!empty($response['orders'])) {
                            $resultFlag = false;
                        }
                    }

                    if ($request->has('status') && $request->status != "") {
                        $response['orders'] = User::join('customers', 'customers.user_id', '=', 'users.id')
                            ->join('orders', 'orders.customer_id', '=', 'customers.id')
                            ->join('products', 'orders.product_id', '=', 'products.id')
                            ->join('order_status', 'orders.order_status_id', '=', 'order_status.id')
                            ->join('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
                            ->where('users.mobile', $request->mobile)
                            ->where('order_status.display_name','ilike','%'.$request->status.'%')
                            ->whereIn('orders.id',$request->ids)
                            ->orderBy('orders.created_at','desc')
                            ->pluck('orders.id');
                        if (!empty($response['orders'])) {
                            $resultFlag = false;
                        }
                    }

                    if ($request->has('awb_no') && $request->awb_no != "") {
                        $response['orders'] = User::join('customers', 'customers.user_id', '=', 'users.id')
                            ->join('orders', 'orders.customer_id', '=', 'customers.id')
                            ->join('products', 'orders.product_id', '=', 'products.id')
                            ->join('order_status', 'orders.order_status_id', '=', 'order_status.id')
                            ->join('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
                            ->where('users.mobile', $request->mobile)
                            ->where('orders.consignment_number','ilike','%'.$request->awb_no.'%')
                            ->whereIn('orders.id',$request->ids)
                            ->orderBy('orders.created_at','desc')
                            ->pluck('orders.id');
                        if (!empty($response['orders'])) {
                            $resultFlag = false;
                        }
                    }
                    // Filter Customer listing with respect to sales parson name
                }
            }

        } catch (\Exception $e) {
            $status = '500';
            $data = [
                'action' => 'Created Customer',
                'status' => $status,
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
            $response = null;
        }
        return response()->json($response, $status);
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
                    $pincodeData = Curl::to('http://www.postalpincode.in/api/pincode/'.$requestPincode)->get();
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
                Log::info($postOffice);
                $postOfficeResponse = Curl::to('http://www.postalpincode.in/api/postoffice/'.$postOffice)->get();
                Log::info($postOfficeResponse);
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
    public function editProfile(Request $request){
        try{
            $userData['first_name'] = $request->f_name;
            $userData['last_name'] = $request->l_name;
            $userData['email'] = $request->email;
            $userData['mobile'] = $request->mobile;
            $userData['dob'] = $request->dob;
            User::where('id',$request->id)->update($userData);
        }catch (\Exception $exception){
                $status = 500;
                $response = null;
                $data = [
                    'input_params' => $request->all(),
                    'action' => 'Get post office info',
                    'exception' => $exception->getMessage()
                ];
                Log::critical(json_encode($data));
        }
    }

    public function deleteAddress(Request $request){
        try{
           CustomerAddress::where('id',$request->address_id)->delete();
        }catch (\Exception $exception){
            $status = 500;
            $response = null;
            $data = [
                'input_params' => $request->all(),
                'action' => 'delete address',
                'exception' => $exception->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getProducts(Request $request) {
        try{
            $status = 200;
            $keyword = trim($request->product_name);
            if($keyword == ''){
                $relevantResult = "";
                $status = 500;
            }else{
                $relevantResult = $this->getRelevantResult($request->product_name);
            }
        }catch (\Exception $e){
            $status = 500;
            $data = [
                'input_params' => $request->all(),
                'action' => 'auto suggest',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $relevantResult = '';
        }
        return response()->json($relevantResult,$status);
    }

    public function getRelevantResult($keyword)
    {
        $product_id = array();
        $searchResultsTake = env('SEARCH_RESULT');
        $keywordLower = strtolower($keyword);
        $products = Product::whereIn('id',$product_id)->where('is_active',1)->where('quantity','!=',0)->select('id','product_name as name','slug','discounted_price')->orderBy('discounted_price','asc')->take($searchResultsTake)->skip(0)->get()->toArray();
        $tags = $this->getTags($keywordLower,$searchResultsTake);
        $tag = $tags['data'];
        $tagCount = count($tag);
        $productCount = count($products);
        $max = max($tagCount,$productCount);
        $k = 0;
        for($i = 0 ; $i  < $max ; $i++) {
            if(!empty($tag[$i])) {

                $relevantData[$k]['id'] = $tag[$i]['id'];
                $relevantData[$k]['name'] = $tag[$i]['name'];
                $relevantData[$k]['translated_name'] = $tag[$i]['translated_name'];
                $stringPosition = stripos($tag[$i]['name'],$keywordLower);
                if(is_int($stringPosition)){
                    $relevantData[$k]['position'] = $stringPosition;
                } else {
                    $relevantData[$k]['position'] = 25;
                }
                $relevantData[$k]['translated_slug'] = trans('product');
                $relevantData[$k]['class'] = "btn-danger";
                $relevantData[$k]['url_param'] = '';
                $relevantData[$k]['discounted_price'] = $tag[$i]['discounted_price'];
                $relevantData[$k]['seller_sku'] = $tag[$i]['seller_sku'];
                $k++;
            }
        }
        return $relevantData;
    }


    public function getTags($keywordLower,$searchResultsTake) {
        $products = Product::where('search_keywords','ILIKE','%'.$keywordLower.'%')->where('is_active',1)->where('quantity','!=',0)->select('id','product_name','search_keywords','discounted_price','seller_sku')->orderBy('discounted_price','asc')->take($searchResultsTake)->skip(0)->get()->toArray();
        $k = 0;
        $tagData = array();
        foreach($products as $product) {
            $keywordsArray = explode(",", $product['search_keywords']);
            $j = 0;
            foreach($keywordsArray as $keyword) {
                $keyword = strtolower($keyword);
                $percent[$j] = similar_text($keyword, $keywordLower);
                $j++;
            }
            $maxValue = max($percent);
            $key = array_search($maxValue,$percent);
            if(!empty($data) && $data['percent'] < $maxValue){
                $keywordsData['key'] = $key;
                $keywordsData['keyword'] = $keywordsArray[$key];
                $keywordsData['percent'] = $maxValue;
                $keywordsData['product_id'] = $product['id'];
            } else {
                $keywordsData['key'] = $key;
                $keywordsData['keyword'] = $keywordsArray[$key];
                $keywordsData['percent'] = $maxValue;
                $keywordsData['product_id'] = $product['id'];
            }
            $alreadyFlag = false;
            foreach($tagData as $tags){
                if($tags['name'] == $keywordsData['keyword']){
                    $alreadyFlag = true;
                    break;
                }
            }
            if(!$alreadyFlag){
                $tagData[$k]['id'] = $keywordsData['product_id'];
                $tagData[$k]['name'] = $keywordsData['keyword'];
                $tagData[$k]['translated_name'] = $keywordsData['keyword'];
                $tagData[$k]['percent'] = $keywordsData['percent'];
                $tagData[$k]['discounted_price'] = $product['discounted_price'];
                $tagData[$k]['seller_sku'] = $product['seller_sku'];
                $k++;
            }
        }
        if (!empty($tagData)) {
            foreach ($tagData as $key => $part) {
                $sort[$key] = $part['percent'];
            }
            array_multisort($sort, SORT_DESC, $tagData);
            $tag['data'] = $tagData;
            $tag['condition'] = TRUE;
        }else{
            $tag['data'] = NULL;
            $tag['condition'] = FALSE;
        }
        return $tag;
    }

    public function addAddress(Request $request){
        try{
           $customerId = Customer::where('user_id',$request->user_id)->value('id');
           $customerAddress = CustomerAddress::where('customer_id',$customerId)->get()->toArray();
           if(count($customerAddress) < 3){
               $addressData['customer_id'] = $customerId;
               $addressData['full_name'] = $request->address_fname;
               $addressData['mobile'] = $request->address_mobile;
               $addressData['flat_door_block_house_no'] = $request->house_block;
               $addressData['name_of_premise_building_village'] = $request->village_premises;
               $addressData['area_locality_wadi'] = $request->area;
               $addressData['road_street_lane'] = $request->road_street;
               $addressData['at_post'] = $request->at_post;
               $addressData['taluka'] = $request->taluka;
               $addressData['district'] = $request->dist;
               $addressData['state'] = $request->state;
               $addressData['pincode'] = $request->pin;
               CustomerAddress::create($addressData);
           }
        }catch (\Exception $exception){
            $status = 500;
            $response = null;
            $data = [
                'input_params' => $request->all(),
                'action' => 'add address',
                'exception' => $exception->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function editAddress(Request $request){
        try{
            $addressData['full_name'] = $request->address_fname;
            $addressData['mobile'] = $request->address_mobile;
            $addressData['flat_door_block_house_no'] = $request->house_block;
            $addressData['name_of_premise_building_village'] = $request->village_premises;
            $addressData['area_locality_wadi'] = $request->area;
            $addressData['road_street_lane'] = $request->road_street;
            $addressData['at_post'] = $request->at_post;
            $addressData['taluka'] = $request->taluka;
            $addressData['district'] = $request->dist;
            $addressData['state'] = $request->state;
            $addressData['pincode'] = $request->pin;
            $customerAddress = CustomerAddress::where('id',$request->address_id)->update($addressData);
        }catch (\Exception $exception){
            $status = 500;
            $response = null;
            $data = [
                'input_params' => $request->all(),
                'action' => 'edit address',
                'exception' => $exception->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }
    public function getProductImagePathUser($imageName,$productOwnerId){
        try{
            $ds = DIRECTORY_SEPARATOR;
            $sellerUploadConfig = env('URL').env('SELLER_FILE_UPLOAD');
            $sha1UserId = sha1($productOwnerId);
            $path = $sellerUploadConfig.$sha1UserId.$ds.'product_images'.$ds.$imageName;
            $file['path'] = $path;
            return $file;
        }catch(\Exception $e){
            $data = [
                'image name' => $imageName,
                'product owner id' => $productOwnerId,
                'action' => 'user side get image path',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500,$e->getMessage());
        }
    }

   public function generate(Request $request){
        try{
            $data = $request->all();
            $user = User::where('id',$data['cust_id'])->first();
            Log::info(json_encode($data));
            $grandTotal = 0;
            $grandTotalBeforeTaxnew = 0;
            $grandTotaltaxAmount = 0;
            $paymentMethod = PaymentMethods::findOrFail(1);
            if($paymentMethod->slug=='citrus'){
                $jsonData = json_decode($data['citrusData']);
                if($jsonData->TxStatus == 'SUCCESS'){
                    $is_success = true;
                    $orderStatus = OrderStatus::where('slug','to_pack')->first();
                }else if($jsonData->TxStatus == 'FAIL'){
                    if($jsonData->pgRespCode == 1){
                        $orderStatus = OrderStatus::where('slug','abort')->first();
                    }else{
                        $orderStatus = OrderStatus::where('slug','failed')->first();
                    }
                    $is_success = false;
                }else if($jsonData->TxStatus == 'CANCELED'){
                    $is_success = false;
                    $orderStatus = OrderStatus::where('slug','abort')->first();
                }
            }else{
                $is_success = true;
                $orderStatus = OrderStatus::where('slug','to_pack')->first();
            }
            $shippingMethod = ShippingMethod::where('slug','agrosiaa_shipment')->first();
            $delivery = DeliveryType::where('slug','=','normal')->first();
            $paymentMeth = PaymentMethods::where('slug','cod')->first();
            $customerAddress = CustomerAddress::findOrFail($data['address_id'])->toJson();
            $mailParameters['customer'] = json_decode($customerAddress);
            $currentTime = Carbon::now();
            $mailParameters['orderedOn'] = date('l, d M Y',strtotime($currentTime));
            $address = OrderCustomerRelation::create([
                'billing_address'=>$customerAddress,
                'shipping_address'=>$customerAddress,
                'created_at'=> $currentTime,
                'updated_at'=> $currentTime
            ]);
            $other = [
                'order_status_id' => $orderStatus->id,
                'shipping_method_id' => $shippingMethod->id,
                'delivery_type_id' => $delivery->id,
                'payment_method_id' => $paymentMeth->id,
                'order_customer_info_id'=>$address->id,
                'created_at'=> $currentTime,
                'updated_at'=> $currentTime
            ];
            if($paymentMeth->slug=='citrus'){
                $paymentGatewayData = $data['citrusData'];
            }else{
                $paymentGatewayData = NULL;
            }
            $deliveryType = DeliveryType::where('slug','=','normal')->select('name','amount')->first();
            $mailParameters['shippingCharges'] = $deliveryType->amount;
            $mailParameters['deliveryName'] = $deliveryType->name;
            $delivery_date = $this->getNormalDeliveryDate($currentTime,$deliveryType->name);
            $mailParameters['deliveryDate'] = date('l, d M',strtotime($delivery_date));
            foreach ($request->product_id as $item){
                $orderData = array();
                $orderData['address_id'] = $request->address_id;
                $orderData['product_id'] = $item;
                $orderData['quantity'] = $request->product_qnt[$item];
                $customerId = CustomerAddress::select('customer_id')->where('id',$request->address_id)->first();
                $orderData['customer_id'] = $customerId['customer_id'];
                $orderData['payment_gateway_data'] = $paymentGatewayData;
                $orderData = array_merge($orderData,$other);
                $product = Product::where('id',$item)->first();
                $orderData['seller_id'] = $product['seller_id'];
                $taxRate = Tax::where('id',$product['tax_id'])->first();
                $mailParameters['productNames'][] = $product['product_name'];
                $mailParameters['productBrands'][] = $product['brand_name'];
                $orderData['tax_rate'] = $taxRate['rate'];
                $sellerAddress = SellerAddress::findOrFail($product->seller_address_id)->toArray();
                $orderData['seller_address'] = json_encode($sellerAddress);
                $orderData['tax_information'] = json_encode($taxRate);
                $deliveryTypeName = DeliveryType::where('slug','=','normal')->pluck('slug');
                if($delivery['slug'] == 'normal'){
                    $dispatchDate = $this->getNormalDeliveryDate(Carbon::now(),'Fast');//To get
                    $dispatchDate = $dispatchDate ." 11:59:00";
                }else{
                    $dispatchDate = $this->getFastDispatchDate(Carbon::now());
                }
                $orderData['dispatch_date'] = $dispatchDate;
                $orderData['pick_up_date'] = $this->getPickUpDate($dispatchDate,$delivery['slug']);
                $orderData['delivery_date'] = $this->getNormalDeliveryDate(Carbon::now(),$delivery['slug']);
                $orderData['base_price'] = $product['base_price'];
                $orderData['subtotal'] = $product['subtotal'];
                $orderData['selling_price'] = $product['selling_price'];
                $orderData['discounted_price'] = $product['discounted_price'];
                $orderData['hsn_code_tax_relation_id'] = $product['hsn_code_tax_relation_id'];
                $orderData['is_web_order'] = true;
                if($product['logistic_percent'] != null){
                    $orderData['logistic_percent'] = $product['logistic_percent'];
                }else{
                    $categoryId = ProductCategoryRelation::where('product_id',$product['id'])->value('category_id');
                    $logisticPercent = Category::where('id',$categoryId)->value('logistic_percentage');
                    $orderData['logistic_percent'] = $logisticPercent;
                }
                if($product['commission_percent'] != null){
                    $orderData['commission_percent'] = $product['commission_percent'];
                }else{
                    $categoryId = ProductCategoryRelation::where('product_id',$product['id'])->value('category_id');
                    $commissionPercent = Category::where('id',$categoryId)->value('commission');
                    $orderData['commission_percent'] = $commissionPercent;
                }
                $orderData['is_ps_campaign'] = $product['is_ps_campaign'];
                $orderData['agrosiaa_campaign_charges'] = $product['agrosiaa_campaign_charges'];
                $orderData['vendor_campaign_charges'] = $product['vendor_campaign_charges'];
                $krishimitraId = AppUsers::join('krishimitra','krishimitra.id','=','app_users.krishimitra_id')
                    ->where('app_users.mobile',$user['mobile'])
                    ->where('krishimitra.is_active', true)
                    ->select('app_users.krishimitra_id as krishimitra_id')->first();
                $orderData['krishimitra_id'] = $krishimitraId['krishimitra_id'];
                if($request->sales_id != null){
                    $orderData['sales_id'] = $request->sales_id;
                }
                $orderData['referral_code'] = $data['referral_code'];
                $order = Order::create($orderData);
                $orderIds[] = $order['id'];
                $mailParameters['orderIds'][] = $this->getStructuredOrderId($order['id']);
                $updatedQuantity = $product['quantity'] - $orderData['quantity'];
                if($paymentMeth->slug=='citrus'){
                    $jsonData = json_decode($data['citrusData']);
                    if($jsonData->TxStatus == 'SUCCESS'){
                        $product->update([
                            'quantity'=>$updatedQuantity
                        ]);
                    }
                }else{
                    $product->update([
                        'quantity'=>$updatedQuantity
                    ]);
                }
                if(Empty($product['out_of_stock_date']) && ($product['quantity'] <= $product['minimum_quantity'] || $product['quantity'] < 0)){
                    $outOfStockDate = Carbon::now();
                    $product->update([
                        'out_of_stock_date' => $outOfStockDate
                    ]);
                }
                $orderHistory['order_id'] = $order->id;
                $orderHistory['order_status_id'] = $orderStatus->id;
                $orderHistory['user_id'] = $user['id'];
                $orderHistory['created_at'] =  $currentTime;
                $orderHistory['updated_at'] =  $currentTime;
                $orderHistoryData = OrderHistory::create($orderHistory);

                $deliveryAmnt = Order::where('id',$order->id)->select('delivery_amount')->get();
                if($order['is_configurable'] == true){
                    $area = $order['length'] * $order['width'];
                    $mailParameters['productSubtotals'][] = ($order->discounted_price * $area) * $order['quantity'];
                    $totalBeforeTaxnewPerUnit = round(($order->discounted_price * $area) / (($order->tax_rate / 100)+1),2);
                }else{
                    $mailParameters['productSubtotals'][] = $order->discounted_price * $order['quantity'];
                    $totalBeforeTaxnewPerUnit = round($order->discounted_price / (($order->tax_rate / 100)+1),2);
                }
                $taxAmount = ($order->tax_rate / 100) * $totalBeforeTaxnewPerUnit * $order->quantity;
                $totalBeforeTaxnew = $totalBeforeTaxnewPerUnit * $order->quantity;
                $mailParameters['totalBeforeTax'][] = $totalBeforeTaxnew;
                $mailParameters['taxAmount'][] = $taxAmount;
                if(strtoupper($sellerAddress['state']) != strtoupper(json_decode($customerAddress)->state)){
                    $igst_applied = true;
                }else{
                    $igst_applied = false;
                }
                $mailParameters['tax_rate'][] = $order->tax_rate;
                $mailParameters['igst_applied'][] = $igst_applied;
                $grandTotalBeforeTaxnew += $totalBeforeTaxnew;
                $grandTotaltaxAmount += $taxAmount;
                $structuredOrder['id'] = $this->getStructuredOrderId($order['id']);
                if($is_success == true){
                    $sendSMS = $this->sendOrderSms($user['mobile'],"Your Agrosiaa order AGR".$structuredOrder['id']." with item ".ucwords($product->product_name)." is successfully placed.");
                }
            }
            Log::info('out of loop');
            $grandTotal = round(($grandTotalBeforeTaxnew + $grandTotaltaxAmount + $deliveryType->amount));
            //$mailParameters['couponDiscount'] = $discount;
            $mailParameters['user'] = $user->toArray();
            $mailParameters['grandTotal'] = $grandTotal;
            //Sending mail.
            $structuredOrderId = $this->getStructuredOrderId($order['id']);
            Log::info('above mail');
            Log::info($is_success);
            Log::info(json_encode($user));
            if($user->email != null){
                Log::info('true');
            }
            if($user['email'] != NULL && $user['is_email'] == true && $is_success == true){
                $user = $user->toArray();
                Log::info('in mail');
                Log::info(json_encode($mailParameters));
                Mail::send('emails.Customer.order',$mailParameters, function ($m) use ($user,$structuredOrderId) {
                    $m->subject('Congratulations, order placed at Agrosiaa');
                    $m->to('vaibhav.woxi@gmail.com');
                    $m->bcc((env('APP_ENV') == 'live') ? 'bharat.woxi@gmail.com' : []);
                    $m->from(env('FROM_EMAIL'));
                });
                Log::info('mail sen to user');
            }
            if(count(Mail::failures()) == 0){
                $orderHistory['is_email_sent'] = 1;
                Log::info('success');
            }
           // $firstOrder = Order::where('cart_items',json_encode($data['cart_items']))->first();
            $allSellers = Product::whereIn('id',$data['product_id'])->select('seller_id')->distinct('seller_id')->get()->toArray();
            //$allSellers = Order::where('cart_items',json_encode($data['cart_items']))->distinct('seller_id')->get();
            //$allSellers = Order::select('seller_id')->where('cart_items',json_encode($data['cart_items']))->distinct('seller_id')->get();
            $DeliveryTypeInfo = DeliveryType::where('slug','=','normal')->first();
            foreach($allSellers as $seller){
                $total = 0;
                $orderSeller = Order::whereIn('id',$orderIds)->where('seller_id',$seller['seller_id'])->get();
                $structuredOrderIds = array();
                for($i = 0 ; $i < count($orderSeller); $i++){
                    $structuredOrderIds[$i] = "AGR".$this->getStructuredOrderId($orderSeller[$i]['id']);
                    $orderSeller[$i]['order_format'] = $this->getStructuredOrderId($orderSeller[$i]['id']);
                    $product = Product::where('id',$orderSeller[$i]['product_id'])->select('product_name','brand_id')->first();
                    $orderSeller[$i]['product_name'] = ucwords($product['product_name']);
                    $orderSeller[$i]['brand_name'] = Brand::where('id',$product['brand_id'])->pluck('name');
                    $delivery_amnt = DeliveryType::where('id',$orderSeller[$i]['delivery_type_id'])->value('amount');
                    if($orderSeller[$i]['is_configurable'] == true){
                        $total = $total + $delivery_amnt + (($orderSeller[$i]['discounted_price'] * ($orderSeller[$i]['length'] * $orderSeller[$i]['width'])) * $orderSeller[$i]['quantity']);
                    }else{
                        $total = $total + $delivery_amnt + ($orderSeller[$i]['discounted_price'] * $orderSeller[$i]['quantity']);
                    }
                }
                $AllOrderIds = implode("," , $structuredOrderIds);
                $sellerData = Seller::where('id',$seller['seller_id'])->first()->toArray();
                $sellerUser = User::where('id',$sellerData['user_id'])->first()->toArray();
                if($sellerUser['email'] != NULL && $sellerUser['is_email'] == true && $is_success == true){
                    $singleOrder = Order::whereIn('id',$orderIds)->first();
                    $DeliveryTypeInfo = DeliveryType::where('id',$singleOrder['delivery_type_id'])->select('name','amount')->first();
                    $customerAddress = OrderCustomerRelation::where('id',$singleOrder['order_customer_info_id'])->value('billing_address');
                    $address = json_decode($customerAddress);
                    $orderedOn = date('l, d M',strtotime($singleOrder['created_at']));
                    $deliveryDate = date('l, d M',strtotime($singleOrder['delivery_date']));
                    $sellerMailParameters = array('sellerUser'=>$sellerUser,'orderedOn' => $orderedOn,'deliveryDate'=>$deliveryDate,'DeliveryTypeInfo'=>$DeliveryTypeInfo,'structuredOrderId'=>$structuredOrderId,'address'=>$address,'orderSeller'=>$orderSeller,'total'=>$total);
                    Mail::send('emails.Seller.order',$sellerMailParameters, function ($message) use ($sellerUser,$AllOrderIds,$sellerData) {
                        $message->subject('Agrosiaa Order(s) '.$AllOrderIds.' Received');
                        $message->to('vaibhav.woxi@gmail.com');
                        $message->from(env('FROM_EMAIL'));
                    });
                }
                $orderCount = $orderSeller->count();
                if($DeliveryTypeInfo->id != 1){
                    if($orderCount == 1){
                        for($i=0 ; $i< $orderCount; $i++){
                            DB::table('orders')
                                ->where('id', $orderSeller[$i]['id'])
                                ->update(['delivery_amount' =>$DeliveryTypeInfo->amount]);
                        }
                    } else {

                        $beforeDecimal = $this->amountAdjustment($DeliveryTypeInfo->amount,$orderCount);
                        $orders = $orderSeller->toArray();
                        for($i=0 ; $i< $orderCount; $i++){
                            DB::table('orders')
                                ->where('id', $orderSeller[$i]['id'])
                                ->update(['delivery_amount' => $beforeDecimal[$i]]);
                        }
                    }
                }
            }
            Log::info('at end');
        }catch(\Exception $e){
            $data = [
                'action' => 'Place order from checkout',
                'request'=> $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function amountAdjustment($amount,$orderCount){
        try{
            $beforeDecimal = array();
            $afterDecimal = array();
            $numbers = array();
            Log::info($orderCount);
            $numberFormat = number_format(($amount/$orderCount),2);
            for($i=0; $i<$orderCount; $i++){
                array_push($numbers,$numberFormat);
            }
            foreach($numbers as $number){
                $explodeNumber = explode('.',$number);
                $beforeDecimal[] = intval($explodeNumber[0]);
                $afterDecimal[] = $explodeNumber[1];
            }
            $sumAfterDecimal = array_sum($afterDecimal)/100;
            for($i=0 ; $i< round($sumAfterDecimal); $i++){
                $beforeDecimal[$i] = $beforeDecimal[$i]+1;
            }
            return $beforeDecimal;
        }catch (\Exception $e){
            $data = [
                'action' => 'Amount adjustment',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }
}


