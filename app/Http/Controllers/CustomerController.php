<?php
/**
 * Created by PhpStorm.
 * User: ganesh
 * Date: 8/7/19
 * Time: 11:00 AM
 */

namespace App\Http\Controllers;

use App\Product;
use App\User;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends BaseController
{

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

    public function getRelevantResult($keyword)
    {
        $searchResultsTake = env('SEARCH_RESULT');
        $keywordLower = strtolower($keyword);
        $customers = User::join('customers','users.id','=','customers.user_id')
            ->join('customer_addresses','customers.id','=','customer_addresses.customer_id')
            ->where('users.first_name','ILIKE','%'.$keywordLower.'%')
            ->orWhere('users.last_name','ILIKE','%'.$keywordLower.'%')
            ->orWhere('users.mobile','ILIKE','%'.$keywordLower.'%')
            ->orWhere('customer_addresses.full_name','ILIKE','%'.$keywordLower.'%')
            ->orWhere('customer_addresses.mobile','ILIKE','%'.$keywordLower.'%')
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
            ->orWhere('users.mobile','ILIKE','%'.$keywordLower.'%')
            ->orWhere('customer_addresses.full_name','ILIKE','%'.$keywordLower.'%')
            ->orWhere('customer_addresses.mobile','ILIKE','%'.$keywordLower.'%')
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
}