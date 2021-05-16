<?php
/**
 * Created by PhpStorm.
 * User: vaibhav
 * Date: 3/11/20
 * Time: 5:05 PM
 */

namespace App\Http\Controllers;

use App\Category;
use App\Feature;
use App\FeatureOption;
use App\Http\Controllers\CustomTraits\ProductTrait;
use App\Product;
use App\ProductCategoryRelation;
use App\ProductQueryConversation;
use App\ProductQueryStatus;
use App\Seller;
use App\Tax;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;

class ProductController extends BaseController
{
    use ProductTrait;

    public function productList(Request $request)
    {
        try {
            $tableData = $request->all();
            $status = '200';
            if($request->has('retrieve') && $request->retrieve == 'ids'){
                $response['productIds'] = Product::where('is_active',true)
                    ->orderBy('created_at','desc')
                    ->pluck('id');
            }
            if($request->has('retrieve') && $request->retrieve == 'data'){
                $response = Product::join('sellers','sellers.id','=','products.seller_id')
                    ->join('product_category','product_category.product_id','=','products.id')
                    ->join('categories as itemhead','itemhead.id','=','product_category.category_id')
                    ->join('categories as subcategory','subcategory.id','=','itemhead.category_id')
                    ->join('categories as root_category','root_category.id','=','subcategory.category_id')
                    ->whereIn('products.id',$request->ids)
                    ->select('products.id','products.product_name', 'products.created_at', 'sellers.company','products.discounted_price',
                        'products.quantity','itemhead.name as itemhead_name','subcategory.name as subcategory_name','root_category.name as root_category_name')
                    ->orderBy('products.created_at','desc')
                    ->get()->toArray();
            }

            if($request->has('filter') && $request->filter == true){
                if($request->has('ids') && !empty($request->ids)) {
                    $resultFlag = true;
                    //Root category
                    if($resultFlag && $request->has('root_category') &&  $tableData['root_category']!=""){
                        $tableData['root_category'] = trim($tableData['root_category']);
                        $response['productIds'] = Product::join('product_category','product_category.product_id','=','products.id')
                            ->join('categories as itemhead','itemhead.id','=','product_category.category_id')
                            ->join('categories as subcategory','subcategory.id','=','itemhead.category_id')
                            ->join('categories as root_category','root_category.id','=','subcategory.category_id')
                            ->where('root_category.name','ILIKE','%'.$tableData['root_category'].'%')
                            ->whereIn('products.id',$request->ids)
                            ->orderBy('products.created_at','asc')
                            ->pluck('products.id');
                        if (!empty($response['productIds'])) {
                            $resultFlag = false;
                        }
                    }
                    //sub_category
                    if($resultFlag && $request->has('sub_category') &&  $tableData['sub_category']!=""){
                        $tableData['sub_category'] = trim($tableData['sub_category']);
                        $response['productIds'] = Product::join('product_category','product_category.product_id','=','products.id')
                            ->join('categories as itemhead','itemhead.id','=','product_category.category_id')
                            ->join('categories as subcategory','subcategory.id','=','itemhead.category_id')
                            ->where('subcategory.name','ILIKE','%'.$tableData['sub_category'].'%')
                            ->whereIn('products.id',$request->ids)
                            ->orderBy('products.created_at','asc')
                            ->pluck('products.id');
                        if (!empty($response['productIds'])) {
                            $resultFlag = false;
                        }
                    }

                    //product_sku
                    if($request->has('itemhead') &&  $tableData['itemhead']!=""){
                        $tableData['itemhead'] = trim($tableData['itemhead']);
                        $response['productIds'] = Product::join('product_category','product_category.product_id','=','products.id')
                            ->join('categories','categories.id','=','product_category.category_id')
                            ->whereIn('products.id',$request->ids)
                            ->where('categories.name','ILIKE','%'.$tableData['itemhead'].'%')
                            ->orderBy('products.created_at','asc')
                            ->pluck('products.id');
                        if (!empty($response['productIds'])) {
                            $resultFlag = false;
                        }
                    }
                    // Search product with name
                    if ($request->has('product') &&  $tableData['product']!="") {
                        $tableData['product'] = trim($tableData['product']);
                        $response['productIds'] = Product::whereIn('id',$request->ids)
                            ->where('product_name','ILIKE','%'.$tableData['product'].'%')
                            ->orderBy('created_at','asc')
                            ->pluck('id');
                        if (!empty($response['productIds'])) {
                            $resultFlag = false;
                        }
                    }

                    // Search product with price
                    if ($request->has('price') &&  $tableData['price']!="") {
                        $tableData['price'] = trim($tableData['price']);
                        $response['productIds'] = Product::whereIn('id',$request->ids)
                            ->where('discounted_price',$tableData['price'])
                            ->orderBy('created_at','asc')
                            ->pluck('id');
                        if (!empty($response['productIds'])) {
                            $resultFlag = false;
                        }
                    }

                    // Search product with quantity
                    if ($request->has('quantity') &&  $tableData['quantity']!="") {
                        $tableData['quantity'] = trim($tableData['quantity']);
                        $response['productIds'] = Product::whereIn('id',$request->ids)
                            ->where('quantity',$tableData['quantity'])
                            ->orderBy('created_at','asc')
                            ->pluck('id');
                        if (!empty($response['productIds'])) {
                            $resultFlag = false;
                        }
                    }

                    // Search product with company name
                    if ($request->has('company') &&  $tableData['company']!="") {
                        $tableData['company'] = trim($tableData['company']);
                        $response['productIds'] = Product::join('sellers','sellers.id','=','products.seller_id')
                            ->whereIn('products.id',$request->ids)
                            ->where('sellers.company','ILIKE','%'.$tableData['company'].'%')
                            ->orderBy('products.created_at','asc')
                            ->pluck('products.id');
                        if (!empty($response['productIds'])) {
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

    public function productDetails(Request $request){
        try{
            $response = array();
            $response['status'] = 200;
            $product = Product::where('id',$request->id)->first();
            $featuresInfo = Feature::join('product_feature_relations','features.id','=','product_feature_relations.feature_id')
                ->where('product_feature_relations.product_id','=',$product['id'])
                ->orderBy('features.priority','asc')
                ->orderBy('features.name','asc')
                ->get()->toArray();
            $response['featureMaster'] = NULL;
            if(!empty($featuresInfo)){
                $i = 0;
                foreach($featuresInfo as $features){
                    $feature = Feature::findOrFail($features['feature_id'])->toArray();
                    $response['featureMaster'][$i]['name'] = $feature['name'];
                    if($features['feature_option_id']!=null){
                        $featureOption = FeatureOption::findOrFail($features['feature_option_id'])->toArray();
                        $response['featureMaster'][$i]['value'] = $featureOption['name'];
                    }else{
                        if($features['feature_text'] != null){
                            if($features['feature_measuring_unit']!=null){
                                $response['featureMaster'][$i]['value'] = $features['feature_text']." ".$features['feature_measuring_unit'];
                            }else{
                                $response['featureMaster'][$i]['value'] = $features['feature_text'];
                            }
                        }
                    }
                    $i++;
                }
            }
            $productImages = $product->images->toArray();
            $response['categoryData']= array();
            $i = 0;
            $response['productImageArray'] = NULL;
            foreach($productImages as $productImage){
                $file = $this->getProductImagePath($productImage['name'],$product->seller->user_id);
                if(!$file['status']){
                    $random = mt_rand(1,10000000000);
                    $response['productImageArray'][$i]['id'] = $productImage['id'];
                    $response['productImageArray'][$i]['name'] = $productImage['name'];
                    $response['productImageArray'][$i]['path'] = $file['path'];
                    $response['productImageArray'][$i]['position'] = $productImage['position'];
                    $response['productImageArray'][$i]['product_id'] = $productImage['product_id'];
                    $response['productImageArray'][$i]['alternate_text'] = $productImage['alternate_text'];
                    $response['productImageArray'][$i]['random'] = $random;
                    $i++;
                }
            }
            $categoryId = ProductCategoryRelation::findOrFail($request->id);
            $categoryInfo = Category::where('id',$categoryId->category_id)->get();
            foreach($categoryInfo as $dataInfo) {
                $response['categoryData']['itemHead'] = $dataInfo;
                $parentCategory = Category::where('id',$dataInfo['category_id'])->first();
                if($parentCategory != null ){
                    $response['categoryData']['parentCategory'] = $parentCategory;
                    $lastParentCategory = Category::where('id',$parentCategory['category_id'])->first();
                    if($lastParentCategory != null ){
                        $response['categoryData']['lastParentCategory'] = $lastParentCategory;
                    }
                }
            }
            $response['productStatus'] = ProductQueryStatus::findOrFail($product->product_query_status_id);
            $response['brand'] = $product->brand()->first();
            $response['product'] = $product;
        }catch (\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Get product details',
                'status' =>$status,
                'exception' => $e->getMessage(),
            ];
            Log::critical(json_encode($data));
            $response['status'] = $status;
        }
        return response()->json($response);
    }

    public function getProductImagePath($imageName,$productOwnerId){
        try{
            $ds = DIRECTORY_SEPARATOR;
            $sellerUploadConfig = env('SELLER_FILE_UPLOAD');
            $sha1UserId = sha1($productOwnerId);
            $sellerUploadPath = env('DOMAIN_NAME').$sellerUploadConfig;
            $sellerImageUploadPath = $sellerUploadPath.$sha1UserId.$ds.'product_images'.$ds.$imageName;
            /* Check file exists or not Directory If Not Exists */
            $file['status'] = false;
            if (file_exists($sellerImageUploadPath)) {
                $file['status'] = true;
            }
            $path = env('DOMAIN_NAME').$sellerUploadConfig.$sha1UserId.$ds.'product_images'.$ds.$imageName;
            $file['path'] = $path;
            return $file;
        }catch(\Exception $e){
            $data = [
                'image name' => $imageName,
                'product owner id' => $productOwnerId,
                'action' => 'get image path',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500,$e->getMessage());
        }
    }
}