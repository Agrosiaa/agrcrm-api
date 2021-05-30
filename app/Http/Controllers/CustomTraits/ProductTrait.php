<?php
namespace App\Http\Controllers\CustomTraits;

use App\Product;
use App\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait ProductTrait{
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
        $relevantData = array();
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
                $relevantData[$k]['company'] = Seller::where('id',$tag[$i]['seller_id'])->value('company');
                $relevantData[$k]['name'] = ucwords($tag[$i]['name']);
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
                $relevantData[$k]['minimum_quantity'] = $tag[$i]['minimum_quantity'];
                $relevantData[$k]['maximum_quantity'] = $tag[$i]['maximum_quantity'];
                $k++;
            }
        }
        return $relevantData;
    }

    public function getTags($keywordLower,$searchResultsTake) {
        $products = Product::where('search_keywords','ILIKE','%'.$keywordLower.'%')->where('is_active',1)->where('quantity','!=',0)
            ->select('id','product_name','search_keywords','discounted_price','seller_sku','seller_id','minimum_quantity','maximum_quantity')
            ->orderBy('discounted_price','asc')->take($searchResultsTake)->skip(0)->get()->toArray();
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
                $keywordsData['seller_id'] = $product['seller_id'];
            } else {
                $keywordsData['key'] = $key;
                $keywordsData['keyword'] = $keywordsArray[$key];
                $keywordsData['percent'] = $maxValue;
                $keywordsData['product_id'] = $product['id'];
                $keywordsData['seller_id'] = $product['seller_id'];
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
                $tagData[$k]['seller_id'] = $product['seller_id'];
                $tagData[$k]['minimum_quantity'] = $product['minimum_quantity'];
                $tagData[$k]['maximum_quantity'] = $product['maximum_quantity'];
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
}