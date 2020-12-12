<?php
/**
 * Created by PhpStorm.
 * User: vaibhav
 * Date: 3/11/20
 * Time: 5:05 PM
 */

namespace App\Http\Controllers;

use App\Http\Controllers\CustomTraits\ProductTrait;
use Laravel\Lumen\Routing\Controller as BaseController;

class ProductController extends BaseController
{
    use ProductTrait;
}