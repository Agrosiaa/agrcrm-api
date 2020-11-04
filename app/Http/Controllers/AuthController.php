<?php
namespace App\Http\Controllers;

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
use App\PostOffice;
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

}


