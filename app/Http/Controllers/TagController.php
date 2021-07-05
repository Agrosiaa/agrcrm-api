<?php
/**
 * Created by PhpStorm.
 * User: vaibhav
 * Date: 4/11/20
 * Time: 9:10 AM
 */

namespace App\Http\Controllers;
use App\Agronomy;
use Carbon\Carbon;
use App\Category;
use App\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use Laravel\Lumen\Routing\Controller as BaseController;


class TagController extends BaseController
{
    public function getTags(Request $request){
        try {
            $status = '200';
            $response = array();
            if($request->has('last_update') && $request->last_update != null){
                $response['categories'] = Category::where('created_at','>=',$request->last_update)->select('name as tag_name')->get()->toArray();
                $response['products'] = Product::join('product_category','product_category.product_id','=','products.id')
                    ->join('categories as item_head','item_head.id','=','product_category.category_id')
                    ->join('categories as sub_cat','sub_cat.id','=','item_head.category_id')
                    ->join('categories as cat','cat.id','=','sub_cat.category_id')
                    ->select('products.product_name as tag_name','item_head.slug as item_head_slug','sub_cat.slug as sub_cat_slug','cat.slug as cat_slug')
                    ->get()->toArray();
                $response['agronomy'] = Agronomy::where('created_at','>=',$request->last_update)->select('crop_name as tag_name')->get()->toArray();
            }else{
                $response['categories'] = Category::select('name as tag_name')->get()->toArray();
                $response['products'] = Product::join('product_category','product_category.product_id','=','products.id')
                    ->join('categories as item_head','item_head.id','=','product_category.category_id')
                    ->join('categories as sub_cat','sub_cat.id','=','item_head.category_id')
                    ->join('categories as cat','cat.id','=','sub_cat.category_id')
                    ->select('products.product_name as tag_name','item_head.slug as item_head_slug','sub_cat.slug as sub_cat_slug','cat.slug as cat_slug')
                    ->get()->toArray();
                $response['agronomy'] = Agronomy::select('crop_name as tag_name')->get()->toArray();
            }
        } catch (\Exception $e) {
            $status = '500';
            $data = [
                'action' => 'Get product categories agronomy for tag',
                'status' => $status,
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
            $response = null;
        }
        return response()->json($response, $status);
    }
}