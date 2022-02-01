<?php
/**
 * Created by PhpStorm.
 * User: vaibhav
 * Date: 4/11/20
 * Time: 9:10 AM
 */

namespace App\Http\Controllers;
use App\Order;
use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use Laravel\Lumen\Routing\Controller as BaseController;


class ReportController extends BaseController
{
    public function reportData(Request $request){
        try{
            $status = '200';
            $data = array();
            $row = 0;
            switch($request->report) {
                case 'sales-orders':
                    $salesCommissionStart = Carbon::parse('2019-12-01');
                    $from_date = Carbon::parse($request->from_date);
                    $to_date = Carbon::parse($request->to_date);
                    $commissionAmountCalculatedOrders = array();
                    $ordersData = Order::join('order_customer_info','order_customer_info.id','=','orders.order_customer_info_id')
                        ->join('products','products.id','=','orders.product_id')
                        ->join('customers','customers.id','=','orders.customer_id')
                        ->join('sales_user','sales_user.id','=','orders.sales_id')
                        ->join('order_status','order_status.id','=','orders.order_status_id')
                        ->whereNotNull('orders.sales_id')
                        ->where(function ($query) {
                            $query->where('orders.referral_code','=','')
                                ->orWhereNull('orders.referral_code');})
                        ->whereBetween('orders.created_at',[$from_date,$to_date])
                        ->where('orders.created_at','>=',$salesCommissionStart)
                        ->select('orders.*','sales_user.name as sales_full_name','order_customer_info.billing_address','products.product_name','order_status.status as order_status_name')
                        ->get()->toArray();
                    $thisSalesOrders = Order::join('order_customer_info','order_customer_info.id','=','orders.order_customer_info_id')
                        ->join('products','products.id','=','orders.product_id')
                        ->join('customers','customers.id','=','orders.customer_id')
                        ->join('users','users.id','=','customers.user_id')
                        ->join('sales_user','sales_user.user_id','=','users.id')
                        ->join('order_status','order_status.id','=','orders.order_status_id')
                        ->where('sales_user.is_active',true)
                        ->whereNotNull('orders.sales_id')
                        ->whereBetween('orders.created_at',[$from_date,$to_date])
                        ->where('orders.created_at','>=',$salesCommissionStart)
                        ->select('orders.id')->get()->toArray();
                    foreach ($ordersData as $key => $orderDatum){
                        $data[$row]['Sales Name'] = $orderDatum['sales_full_name'];
                        $data[$row]['AWB Number'] = $orderDatum['consignment_number'];
                        $data[$row]['Order Nubmer'] = "AGR".$this->getStructuredOrderId($orderDatum['id']);
                        $data[$row]['Order Date'] = date('d M Y',strtotime($orderDatum['created_at']));
                        $data[$row]['Order Status'] = $orderDatum['order_status_name'];
                        $customerInfo = json_decode($orderDatum['billing_address']);
                        $data[$row]['Customer Name'] = $customerInfo->full_name;
                        $data[$row]['Customer Mobile'] = $customerInfo->mobile;
                        $data[$row]['Product Name'] = $orderDatum['product_name'];
                        if($orderDatum['is_configurable'] == true){
                            $orderAmount = (($orderDatum["discounted_price"]+$orderDatum["delivery_amount"]-$orderDatum['coupon_discount']) * (($orderDatum["length"]) * ($orderDatum["width"])) * $orderDatum['quantity']);
                        }else{
                            $orderAmount = (($orderDatum["discounted_price"]+$orderDatum["delivery_amount"]-$orderDatum['coupon_discount']) * $orderDatum['quantity']);
                        }
                        $data[$row]['Order Amount'] = strval($orderAmount);
                        $data[$row]['Order Weightage'] = $orderDatum['weight_logistic'];
                        /*if(!in_array($orderDatum['id'],$commissionAmountCalculatedOrders)){
                            if($orderDatum['consignment_number'] != '' || $orderDatum['consignment_number'] != null){
                                $awbAmount = 0;
                                if(substr($orderDatum['consignment_number'],-2) == 'IN'){
                                    $sameAWBNumberOrders = Order::where('consignment_number',$orderDatum['consignment_number'])
                                        ->whereIn('id',$thisSalesOrders)
                                        ->get()->toArray();
                                    foreach ($sameAWBNumberOrders as $AWBNumberOrder){
                                        $commissionAmountCalculatedOrders[] = $AWBNumberOrder['id'];
                                        if($AWBNumberOrder['is_configurable'] == true){
                                            $awbAmount = $awbAmount + (($AWBNumberOrder["discounted_price"]+$AWBNumberOrder["delivery_amount"]-$AWBNumberOrder['coupon_discount']) * (($AWBNumberOrder["length"]) * ($AWBNumberOrder["width"])) * $AWBNumberOrder['quantity']);
                                        }else{
                                            $awbAmount = $awbAmount + (($AWBNumberOrder["discounted_price"]+$AWBNumberOrder["delivery_amount"]-$AWBNumberOrder['coupon_discount']) * $AWBNumberOrder['quantity']);
                                        }
                                    }
                                }elseif (is_numeric($orderDatum['consignment_number'])){
                                    $sameAWBNumberOrders = Order::where('consignment_number',$orderDatum['consignment_number'])
                                        ->whereIn('id',$thisSalesOrders)
                                        ->get()->toArray();
                                    foreach ($sameAWBNumberOrders as $AWBNumberOrder){
                                        $commissionAmountCalculatedOrders[] = $AWBNumberOrder['id'];
                                        if($AWBNumberOrder['is_configurable'] == true){
                                            $awbAmount = $awbAmount + (($AWBNumberOrder["discounted_price"]+$AWBNumberOrder["delivery_amount"]-$AWBNumberOrder['coupon_discount']) * (($AWBNumberOrder["length"]) * ($AWBNumberOrder["width"])) * $AWBNumberOrder['quantity']);
                                        }else{
                                            $awbAmount = $awbAmount + (($AWBNumberOrder["discounted_price"]+$AWBNumberOrder["delivery_amount"]-$AWBNumberOrder['coupon_discount']) * $AWBNumberOrder['quantity']);
                                        }
                                    }
                                } else{
                                    $commissionAmountCalculatedOrders[] = $orderDatum['id'];
                                    if($orderDatum['is_configurable'] == true){
                                        $awbAmount = (($orderDatum["discounted_price"]+$orderDatum["delivery_amount"]-$orderDatum['coupon_discount']) * (($orderDatum["length"]) * ($orderDatum["width"])) * $orderDatum['quantity']);
                                    }else{
                                        $awbAmount = (($orderDatum["discounted_price"]+$orderDatum["delivery_amount"]-$orderDatum['coupon_discount']) * $orderDatum['quantity']);
                                    }
                                }
                                if($awbAmount <= 300){
                                    $commission = 0;
                                }elseif ($awbAmount > 300 && $awbAmount <= 1000){
                                    $commission = $awbAmount/200;
                                }elseif ($awbAmount > 1000 && $awbAmount <= 2000){
                                    $commission = (3 * $awbAmount)/400;
                                }elseif ($awbAmount > 2000 && $awbAmount <= 5000){
                                    $commission = $awbAmount/100;
                                }elseif ($awbAmount > 5000) {
                                    $commission = (5 * $awbAmount)/400;
                                }
                                $data[$row]['Commission Amount'] = $commission;
                            }else{
                                $data[$row]['Commission Amount'] = 0;
                            }
                        }else{
                            $data[$row]['Commission Amount'] = 0;
                        }*/
                        $row++;
                    }
                    break;
                }
        }catch(\Exception $e){
            $status = '500';
            $logData = [
                'action' => 'Customer Orders',
                'status' => $status,
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($logData));
            $data = null;
        }
        return response()->json($data, $status);
    }

    public function getStructuredOrderId($orderId)
    {
        return str_pad($orderId, 9, "0", STR_PAD_LEFT);
    }
}
