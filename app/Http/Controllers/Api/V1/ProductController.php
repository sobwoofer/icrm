<?php

namespace App\Http\Controllers\Api\V1;

use App\Eloquent\Product\Product;
use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class ProductController extends Controller
{
    private const ALLOW_PRODUCT_COLUMNS = [
        'product.id',
        'product.name',
        'product.price',
        'product.url',
        'product.image_url',
        'product.article',
        'product.description',
        'product.created_at',
        'product.updated_at',
    ];


    public function index(Request $request)
    {
        try {
            $query = Product::query()->select(self::ALLOW_PRODUCT_COLUMNS)->with(['images', 'priceOptions']);

            if ($minCreatedDate = $request->get('min_created_date')) {
                $query->where('product.created_at', '>', $minCreatedDate);
            }

            if ($minUpdatedDate = $request->get('min_updated_date')) {
                $query->where('product.updated_at', '>', $minUpdatedDate);
            }

            if ($vendorName = $request->get('vendor_name')) {
                $query->leftJoin('category', 'category.id', '=','product.category_id');
                $query->leftJoin('vendor', 'vendor.id', '=','category.vendor_id');
                $query->where('vendor.slug', '=', $vendorName);
            }

            return $query->get()->all();
        } catch (Exception $e) {
            return Response::create($e->getMessage(), 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $vendorName = $request->get('vendor_name');
            $article = $request->get('article');
            if ($vendorName && $article) {
                return Product::query()
                    ->select(self::ALLOW_PRODUCT_COLUMNS)
                    ->with(['images', 'priceOptions'])
                    ->leftJoin('category', 'category.id', '=','product.category_id')
                    ->leftJoin('vendor', 'vendor.id', '=','category.vendor_id')
                    ->where('article', $article)
                    ->where('vendor.slug', $vendorName)->firstOrFail();
            }
        } catch (Exception $e) {
            return Response::create($e->getMessage(), 500);
        }

        return Response::create('params [article, vendor_name] is required', 400);
    }
}
