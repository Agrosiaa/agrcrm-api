<?php
/**
 * Created by Ameya Joshi.
 * Date: 10/7/17
 * Time: 3:40 PM
 */
namespace App\Http\Controllers;

use App\WorkOrderStatusDetail;
use App\WorkOrderStatusMaster;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseController
{
    public function getAppVersion(Request $request){
        try{
            $status = 200;
            $response  = WorkOrderStatusMaster::get();
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
}


