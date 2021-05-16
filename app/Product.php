<?php
/**
 * Created by PhpStorm.
 * User: vaibhav
 * Date: 27/5/19
 * Time: 11:54 AM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'seller_sku','brand','product_name','slug','product_description','model_name','base_price','quantity',
        'minimum_quantity','maximum_quantity','max_quantity_equal_to_stock','key_specs_1','key_specs_2',
        'key_specs_3','search_keywords','weight','weight_measuring_unit','height','width','length','packaging_dimensions_measuring_unit',
        'final_weight_of_packed_material','final_weight_measuring_unit','product_pick_up_address','approved_date','is_active',
        'updated_price','updated_quantity','product_query_status_id','tax_id','seller_id','created_at',
        'updated_at','other_features_and_applications','sales_package_or_accessories','domestic_warranty','domestic_warranty_measuring_unit',
        'warranty_summary','warranty_service_type','warranty_items_covered','warranty_items_not_covered','discount','discounted_price',
        'seller_address_id','brand_id','admin_id','item_based_sku','out_of_stock_date','selling_price','subtotal','hsn_code_tax_relation_id',
        'configurable_width','logistic_percent','commission_percent','is_ps_campaign','agrosiaa_campaign_charges','vendor_campaign_charges'
    ];

    public function seller(){
        return $this->belongsTo('App\Seller','seller_id');
    }

    public function images(){
        return $this->hasMany('App\ProductImage','product_id');
    }

    public function features(){
        return $this->hasMany('App\ProductFeatureRelation','product_id');
    }

    public function productcategory(){
        return $this->hasMany('App\ProductCategoryRelation','product_id');
    }

    public function productCategoryRel(){
        return $this->hasOne('App\ProductCategoryRelation','product_id');
    }

    public function brand(){
        return $this->belongsTo('App\Brand','brand_id');
    }

    public function tax(){
        return $this->belongsTo('App\Tax' ,'tax_id');
    }

    public function hsnCodeTaxRelation(){
        return $this->belongsTo('App\HSNCodeTaxRelation','hsn_code_tax_relation_id');
    }
}