<?php
namespace App\Http\Controllers\CustomTraits;

use App\AppUsers;
use App\Brand;
use App\Category;
use App\CustomerAddress;
use App\DeliveryType;
use App\Order;
use App\OrderCustomerRelation;
use App\OrderHistory;
use App\OrderStatus;
use App\PaymentMethods;
use App\Product;
use App\ProductCategoryRelation;
use App\Seller;
use App\SellerAddress;
use App\ShippingMethod;
use App\Tax;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait OrderTrait{
    use SendMessageTrait;
    use DeliveryTrait;
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
                'action' => 'Customer Orders',
                'status' => $status,
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
            $response = null;
        }
        return response()->json($response, $status);
    }

    public function csrOrders(Request $request)
    {
        try {
            $status = '200';
            if($request->has('retrieve') && $request->retrieve == 'ids'){
                $response['orders'] = Order::join('customers', 'orders.customer_id', '=', 'customers.id')
                    ->join('products', 'orders.product_id', '=', 'products.id')
                    ->join('order_status', 'orders.order_status_id', '=', 'order_status.id')
                    ->join('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
                    ->where('orders.sales_id', $request->csr_id)
                    ->orderBy('orders.created_at','desc')
                    ->pluck('orders.id');
            }
            if($request->has('retrieve') && $request->retrieve == 'data'){
                $response = Order::join('customers', 'orders.customer_id', '=', 'customers.id')
                    ->join('users','customers.user_id','=','users.id')
                    ->join('products', 'orders.product_id', '=', 'products.id')
                    ->join('order_status', 'orders.order_status_id', '=', 'order_status.id')
                    ->join('shipping_methods','shipping_methods.id','=','orders.shipping_method_id')
                    ->join('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
                    ->where('orders.sales_id', $request->csr_id)
                    ->whereIn('orders.id', $request->filteredIds)
                    ->select('orders.id', 'orders.quantity', 'orders.created_at', 'orders.subtotal', 'orders.consignment_number', 'products.product_name', 'order_status.display_name as status', 'payment_methods.name as payment_mode','products.seller_sku','users.mobile','shipping_methods.name as shipment',DB::raw("CONCAT(users.first_name,' ',users.last_name) AS full_name"))
                    ->orderBy('orders.created_at','desc')
                    ->get()->toArray();
            }

            if($request->has('filter') && $request->filter == true){
                if($request->has('ids') && !empty($request->ids)) {
                    $resultFlag = true;
                    // Search customer mobile number
                    if ($request->has('order_no') && $request->order_no != "") {
                        $response['orders'] = Order::where('orders.sales_id', $request->csr_id)
                            ->where('orders.id', $request->order_no)
                            ->whereIn('orders.id',$request->ids)
                            ->orderBy('orders.created_at','desc')
                            ->pluck('orders.id');
                        if (!empty($response['orders'])) {
                            $resultFlag = false;
                        }
                    }
                    if ($request->has('product') && $request->product != "") {
                        $response['orders'] = Order::join('products', 'orders.product_id', '=', 'products.id')
                            ->where('orders.sales_id', $request->csr_id)
                            ->where('products.product_name','ilike','%'.$request->product.'%')
                            ->whereIn('orders.id',$request->ids)
                            ->orderBy('orders.created_at','desc')
                            ->pluck('orders.id');
                        if (!empty($response['orders'])) {
                            $resultFlag = false;
                        }
                    }
                    if ($request->has('customer') && $request->product != "") {
                        $response['orders'] = Order::join('customers', 'orders.customer_id', '=', 'customers.id')
                            ->join('users','customers.user_id','=','users.id')
                            ->where('orders.sales_id', $request->csr_id)
                            ->where('users.first_name','ilike','%'.$request->customer.'%')
                            ->whereIn('orders.id',$request->ids)
                            ->orderBy('orders.created_at','desc')
                            ->pluck('orders.id');
                        if (!empty($response['orders'])) {
                            $resultFlag = false;
                        }
                    }
                    if ($request->has('quantity') && $request->quantity != "") {
                        $response['orders'] = Order::where('orders.sales_id', $request->csr_id)
                            ->where('orders.quantity',$request->quantity)
                            ->whereIn('orders.id',$request->ids)
                            ->orderBy('orders.created_at','desc')
                            ->pluck('orders.id');
                        if (!empty($response['orders'])) {
                            $resultFlag = false;
                        }
                    }

                    if ($request->has('status') && $request->status != "") {
                        $response['orders'] = Order::join('order_status', 'orders.order_status_id', '=', 'order_status.id')
                            ->where('orders.sales_id', $request->csr_id)
                            ->where('order_status.display_name','ilike','%'.$request->status.'%')
                            ->whereIn('orders.id',$request->ids)
                            ->orderBy('orders.created_at','desc')
                            ->pluck('orders.id');
                        if (!empty($response['orders'])) {
                            $resultFlag = false;
                        }
                    }

                    if ($request->has('awb_no') && $request->awb_no != "") {
                        $response['orders'] = Order::join('customers', 'orders.customer_id', '=', 'customers.id')
                            ->where('orders.sales_id', $request->csr_id)
                            ->where('orders.consignment_number','ilike','%'.$request->awb_no.'%')
                            ->whereIn('orders.id',$request->ids)
                            ->orderBy('orders.created_at','desc')
                            ->pluck('orders.id');
                        if (!empty($response['orders'])) {
                            $resultFlag = false;
                        }
                    }
                    if ($request->has('shipment') && $request->shipment != "") {
                        $response['orders'] = Order::join('shipping_methods','shipping_methods.id','=','orders.shipping_method_id')
                            ->where('orders.sales_id', $request->csr_id)
                            ->where('shipping_methods.name','ilike','%'.$request->shipment.'%')
                            ->whereIn('orders.id',$request->ids)
                            ->orderBy('orders.created_at','desc')
                            ->pluck('orders.id');
                        if (!empty($response['orders'])) {
                            $resultFlag = false;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $status = '500';
            $data = [
                'action' => 'CSR Orders',
                'status' => $status,
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
            $response = null;
        }
        return response()->json($response, $status);
    }

    public function generate(Request $request){
        try{
            $data = $request->all();
            $user = User::where('id',$data['cust_id'])->first();
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