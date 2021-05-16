<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $table = 'features';

    protected $fillable = [
        'name', 'code', 'visibility', 'required', 'searchable', 'use_in_filter', 'comparable',
        'category_id', 'input_type_id', 'added_by', 'updated_by', 'measuring_unit_id', 'created_at',
        'updated_at', 'excel_column_description',
        'excel_column_input_type_description', 'excel_column_example', 'excel_column_measurable_unit_input_type_description',
        'excel_column_measurable_unit_description', 'excel_column_measurable_unit_example','priority','name_mr'

    ];

    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }

    public function inputs()
    {
        return $this->belongsTo('App\InputTypes', 'input_type_id');
    }

    public function options()
    {
        return $this->hasMany('App\FeatureOption', 'feature_id');
    }

    public function measuringUnits()
    {
        return $this->belongsTo('App\MeasuringUnit', 'measuring_unit_id');
    }

    public function productFeatureRelation(){
        return $this->hasone('App\ProductFeatureRelation','feature_id');
    }
}