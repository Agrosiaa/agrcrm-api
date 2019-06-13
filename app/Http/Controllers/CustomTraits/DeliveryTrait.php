<?php
namespace App\Http\Controllers\CustomTraits;


use App\Cart;
use App\Holidays;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use DatePeriod;

trait DeliveryTrait{

    public function getNormalDeliveryDate($datetime,$deliveryType){
        try{
            $holidaysList = Holidays::select('date')->get()->toArray();
            $workingCount = 0;
            $holidayCount = 0;
            $time = date($datetime);
            $time = strtotime($time);
            $time = date('H:i:s',$time);
            $datetime = date('Y-m-d',  strtotime($datetime));
            if($deliveryType == "Fast"){
                $deliveryPeriod = 2;
            }else{
                $deliveryPeriod = 9;
            }
            if($time > "11:59:00" && date('D',strtotime($datetime)) != 'Sun' && !in_array($datetime,$holidaysList)){
                $deliveryPeriod++;
            }
            while($workingCount != $deliveryPeriod){
                if(date('D',strtotime($datetime))=='Sun' || in_array($datetime,$holidaysList)){
                    $datetime =  strtotime($datetime);
                    $datetime = date('Y-m-d',  strtotime("+1 day", $datetime));
                    $holidayCount++;
                }else{
                    $datetime =  strtotime($datetime);
                    $datetime = date('Y-m-d',  strtotime("+1 day", $datetime));
                    $workingCount++;
                }
            }
            $deliveryDate = $this->addWorkingDays($holidaysList,$datetime);
            return $deliveryDate;
        }catch(\Exception $e){
            $data = [
                'input_params' => null,
                'dateTime' => $datetime,
                'action' => 'calculate normal delivery date',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500,$e->getMessage());
        }
    }

    public function getFastDispatchDate($datetime){
        try{
            $time = date($datetime);
            $strToTime = strtotime($time);
            $time = date('H:i:s',$strToTime);
            $year = date('Y',$strToTime);
            $month = date('m',$strToTime);
            $day = date('d',$strToTime);
            $hours = date('H',$strToTime);
            $minutes = date('i',$strToTime);
            $holidaysList = Holidays::lists('date')->toArray();
            if($time > "10:00:00" && $time < "15:59:00"){
                $datetime = date('Y-m-d',strtotime($datetime));
                if(date('D',strtotime($datetime))=='Sun' || in_array($datetime,$holidaysList)){
                    $flag = 0;
                    while($flag != 1){
                        $datetime = date('Y-m-d', strtotime($datetime . " + 1 day"));
                        if(date('D',strtotime($datetime))!='Sun' && (!in_array($datetime,$holidaysList))){
                            $flag = 1 ;
                        }
                    }
                    $dispatchDateTime = $datetime." 11:59:00 ";
                }else{
                    $dt = Carbon::create($year,$month, $day, $hours,$minutes);
                    $dt->toDateTimeString();
                    $dt->addHours(2);
                    $dispatchDateTime = $dt->toDateTimeString();
                }
            }elseif($time > "16:00:00" && $time < "23:59:00"){
                $date = strtotime($datetime);
                $dispatchDateTime = date('Y-m-d', strtotime("+1 day", $date));
                if(date('D',strtotime($dispatchDateTime))=='Sun' || in_array($dispatchDateTime,$holidaysList)){
                    $flag = 0;
                    while($flag != 1){
                        $dispatchDateTime = date('Y-m-d', strtotime($dispatchDateTime . " + 1 day"));
                        if(date('D',strtotime($dispatchDateTime))!='Sun' && (!in_array($dispatchDateTime,$holidaysList))){
                            $flag = 1 ;
                        }
                    }
                }
                $dispatchDateTime = $dispatchDateTime ." 11:59:00 ";
            }else{
                $datetime = date('Y-m-d', strtotime($datetime));
                if(date('D',strtotime($datetime))=='Sun' || in_array($datetime,$holidaysList)){
                    $flag = 0;
                    while($flag != 1){
                        $datetime = date('Y-m-d', strtotime($datetime . " + 1 day"));
                        if(date('D',strtotime($datetime))!='Sun' && (!in_array($datetime,$holidaysList))){
                            $flag = 1 ;
                        }
                    }
                }
                $dispatchDateTime = $datetime." 11:59:00 ";
            }
            return $dispatchDateTime;
        }catch(\Exception $e){
            $data = [
                'input_params' => null,
                'dateTime' => $datetime,
                'action' => 'calculate fast dispatch date',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500,$e->getMessage());
        }
    }

    public function getPickUpDate($datetime,$deliveryType){
        try{
            if($deliveryType == "normal"){
                $holidaysList = Holidays::select('date')->get()->toArray();
                $workingCount = 0;
                $deliveryPeriod = 1;
                $holidayCount = 0;
                while($workingCount != $deliveryPeriod){
                    if(date('D',strtotime($datetime))=='Sun' || in_array($datetime,$holidaysList)){
                        $datetime =  strtotime($datetime);
                        $datetime = date('Y-m-d',  strtotime("+1 day", $datetime));
                        $holidayCount++;
                    }else{
                        $datetime =  strtotime($datetime);
                        $datetime = date('Y-m-d',  strtotime("+1 day", $datetime));
                        $workingCount++;
                    }
                }
                $dispatchDate = $this->addWorkingDays($holidaysList,$datetime);
                $dispatchDate = $dispatchDate ." 11:59:00";
            }else {
                $dispatchDate = $this->getFastPickUpDate($datetime);
            }
            return $dispatchDate;
        }catch(\Exception $e){
            $data = [
                'input_params' => null,
                'dateTime' => $datetime,
                'action' => 'calculate order Pick Up Date',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500,$e->getMessage());
        }
    }

    public function addWorkingDays($holidaysList,$deliveryDate){
        $flag = 0;
        while($flag != 1){
            if(date('D',strtotime($deliveryDate))=='Sun' || in_array($deliveryDate,$holidaysList)){
                $deliveryDate = date('Y-m-d', strtotime($deliveryDate . " + 1 day"));
            }else{
                $flag = 1 ;
            }
        }
        return $deliveryDate;
    }

    public function getFastPickUpDate($datetime){
        try{
            $holidaysList = Holidays::select('date')->get()->toArray();
            $time = date($datetime);
            $strToTime = strtotime($time);
            $year = date('Y',$strToTime);
            $month = date('m',$strToTime);
            $day = date('d',$strToTime);
            $hours = date('H',$strToTime);
            $minutes = date('i',$strToTime);
            $dt = Carbon::create($year,$month, $day, $hours,$minutes);
            $dt->toDateTimeString();
            $dt->addHours(4);
            $hours = $dt->hour;
            $minutes = $dt->minute;
            $time = "$hours:$minutes";
            $workingCount = 0;
            $holidayCount =0;
            if($time > "18:00"){
                while($workingCount != 1){
                    if(date('D',strtotime($datetime))=='Sun' || in_array($datetime,$holidaysList)){
                        $datetime =  strtotime($datetime);
                        $datetime = date('Y-m-d',  strtotime("+1 day", $datetime));
                        $holidayCount++;
                    }else{
                        $datetime =  strtotime($datetime);
                        $datetime = date('Y-m-d',  strtotime("+1 day", $datetime));
                        $workingCount++;
                    }
                }
                $PickUpDateTime = $this->addWorkingDays($holidaysList,$datetime);
                $PickUpDateTime = $PickUpDateTime ."  10:00:00 ";
            }else{
                $PickUpDateTime = $dt->toDateTimeString();
            }
            return $PickUpDateTime;
        }catch(\Exception $e){
            $data = [
                'input_params' => null,
                'dateTime' => $datetime,
                'action' => 'calculate fast dispatch date',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500,$e->getMessage());
        }
    }

    public function getStructuredOrderId($orderId){
        return str_pad($orderId, 9, "0", STR_PAD_LEFT);
    }
}