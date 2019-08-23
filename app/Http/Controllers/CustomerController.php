<?php
/**
 * Created by PhpStorm.
 * User: ganesh
 * Date: 8/7/19
 * Time: 11:00 AM
 */

namespace App\Http\Controllers;

use App\Cart;
use App\Product;
use App\User;
use Carbon\Carbon;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Customer;
use App\CustomerAddress;
use App\Role;
use App\DeliveryType;
use App\PaymentMethods;


class CustomerController extends BaseController
{
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
            Log::info($request->mobile);
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

    public function getCustomers(Request $request) {
        try{
            $status = 200;
            $keyword = trim($request->customer_data);
            if($keyword == ''){
                $relevantResult = "";
                $status = 500;
            }else{
                $relevantResult = $this->getRelevantResult($request->customer_data);
            }
        }catch (\Exception $e){
            $status = 500;
            $data = [
                'input_params' => $request->all(),
                'action' => 'auto suggested customers',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $relevantResult = '';
        }
        return response()->json($relevantResult,$status);
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

    public function getRelevantResult($keyword)
    {
        $searchResultsTake = env('SEARCH_RESULT');
        $keywordLower = strtolower($keyword);
        $customers = User::join('customers','users.id','=','customers.user_id')
            ->join('customer_addresses','customers.id','=','customer_addresses.customer_id')
            ->where('users.first_name','ILIKE','%'.$keywordLower.'%')
            ->orWhere('users.last_name','ILIKE','%'.$keywordLower.'%')
            ->orWhere('customer_addresses.full_name','ILIKE','%'.$keywordLower.'%')
            ->where('users.is_active',1)
            ->where('users.role_id','=',4)
            ->distinct('users.id')
            ->select('users.id','users.first_name as name','users.email','users.mobile')
            ->orderBy('id','asc')->take($searchResultsTake)->skip(0)->get()->toArray();
        $tags = $this->getTags($keywordLower,$searchResultsTake);
        $tag = $tags['data'];
        $tagCount = count($tag);
        $custCount = count($customers);
        $max = max($tagCount,$custCount);
        $k = 0;
        for($i = 0 ; $i  < $max ; $i++) {
            if(!empty($tag[$i])) {
                $relevantData[$k]['id'] = $tag[$i]['id'];
                $relevantData[$k]['fname'] = $tag[$i]['fname'];
                $relevantData[$k]['lname'] = $tag[$i]['lname'];
                $relevantData[$k]['class'] = "btn-danger";
                $relevantData[$k]['url_param'] = 'null';
                $relevantData[$k]['mobile'] = $tag[$i]['mobile'];
                $relevantData[$k]['email'] = $tag[$i]['email'];
                $k++;
            }
        }
        return $relevantData;
    }

    public function getTags($keywordLower,$searchResultsTake) {
        $customers = User::join('customers','users.id','=','customers.user_id')
            ->join('customer_addresses','customers.id','=','customer_addresses.customer_id')
            ->where('users.first_name','ILIKE','%'.$keywordLower.'%')
            ->orWhere('users.last_name','ILIKE','%'.$keywordLower.'%')
            ->orWhere('customer_addresses.full_name','ILIKE','%'.$keywordLower.'%')
             ->where('users.is_active',1)
            ->where('users.role_id','=',4)
            ->distinct('users.id')
            ->select('users.id','users.first_name as f_name','users.last_name as l_name','users.mobile as user_mobile','users.email as email')->orderBy('id','asc')->take($searchResultsTake)->skip(0)->get()->toArray();
        $k = 0;
        $tagData = array();
        foreach($customers as $customer) {
                $tagData[$k]['id'] = $customer['id'];
                $tagData[$k]['fname'] = $customer['f_name'];
                $tagData[$k]['lname'] = $customer['l_name'];
                $tagData[$k]['mobile'] = $customer['user_mobile'];
                $tagData[$k]['email'] = $customer['email'];
                $k++;
        }
        if (!empty($tagData)) {
            $tag['data'] = $tagData;
            $tag['condition'] = TRUE;
        }else{
            $tag['data'] = NULL;
            $tag['condition'] = FALSE;
        }
        return $tag;
    }

    public function abandonedListing(Request $request){
        try {
            $status = '200';
            if($request->has('retrieve') && $request->retrieve == 'ids'){
                $response['cart'] = User::join('customers', 'customers.user_id', '=', 'users.id')
                    ->join('cart', 'cart.customer_id', '=', 'customers.id')
                    ->where('users.mobile', $request->mobile)
                    ->whereNull('cart.deleted_at')
                    ->whereNotNull('cart.customer_id')
                    ->where('cart.is_purchased',false)
                    ->where('cart.is_delete_backend',false)
                    ->orderBy('cart.created_at','desc')
                    ->pluck('cart.id');
            }
            if($request->has('retrieve') && $request->retrieve == 'data'){
                $response = User::join('customers', 'customers.user_id', '=', 'users.id')
                    ->join('cart', 'cart.customer_id', '=', 'customers.id')
                    ->where('users.mobile', $request->mobile)
                    ->whereIn('cart.id', $request->filteredIds)
                    ->select('customers.id','customers.is_web','cart.created_at','cart.updated_at')
                    ->orderBy('cart.created_at','desc')
                    ->get()->toArray();
            }

            if($request->has('filter') && $request->filter == true){
                if($request->has('ids') && !empty($request->ids)) {
                    if($request->has('toDate') && $request->has('fromDate') && $request->toDate != '' && $request->fromDate != '' ){
                        $selectedToDate = Carbon::parse($request->toDate);
                        $selectedFromDate = Carbon::parse($request->fromDate);
                        $response['cart'] = Cart::whereIn('id',$request->ids)
                            ->where('created_at','<=',$selectedToDate)
                            ->where('created_at','>=',$selectedFromDate)
                            ->orderBy('created_at','desc')
                            ->pluck('id');
                    }
                    if($request->has('toUpdatedDate') && $request->has('fromUpdatedDate') && $request->toUpdatedDate != '' && $request->fromUpdatedDate != '' ){
                        $selectedToDate = Carbon::parse($request->toUpdatedDate);
                        $selectedFromDate = Carbon::parse($request->fromUpdatedDate);
                        $response['cart'] = Cart::whereIn('id',$request->ids)
                            ->where('updated_at','<=',$selectedToDate)
                            ->where('updated_at','>=',$selectedFromDate)
                            ->orderBy('created_at','desc')
                            ->pluck('id');
                    }
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

    public function abandonedDetails(Request $request ,$id){
        try{
            $status= '200';
            $response['data'] = Customer::where('id',$id)->with('user')->with('addresses')->first();
            $response['cartData'] = Cart::join('sellers','sellers.id','=','cart.seller_id')
                ->where('cart.customer_id',$id)
                ->whereNull('cart.deleted_at')
                ->where('cart.is_purchased',false)
                ->where('cart.is_delete_backend',false)
                ->orderBy('cart.created_at','desc')
                ->select('cart.*','sellers.company')
                ->get()->toArray();
            $grandTotal = 0;
            foreach ($response['cartData'] as $key => $value){
                $grandTotal += (($value['unit_price'] * $value['quantity']));
            }
            $response['grandTotal'] = $grandTotal;
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
}