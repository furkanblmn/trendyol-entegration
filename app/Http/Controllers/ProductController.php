<?php

namespace App\Http\Controllers;

use App\Helpers\Output;
use App\Jobs\ProcessProductsBatch;
use App\Models\Product;
use App\Services\TrendyolService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    protected $trendyolService;

    public function __construct(TrendyolService $trendyolService)
    {
        $this->trendyolService = $trendyolService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::query();

            return DataTables::of($products)
                ->addColumn('is_varianted', function ($data) {
                    return $data->is_varianted ? "Varyantlı" : "Tekli";
                })
                ->addColumn('process', function ($data) {
                    return '<a href="javascript:;" data-url="' . route('products.update', $data->id) . '" class="update_item btn btn-sm btn-danger">Güncelle</a>';
                })
                ->filter(function ($query) use ($request) {
                    if ($keyword = $request->input('search.value')) {
                        return $query->searchFields($keyword);
                    }
                    return $query;
                })
                ->rawColumns(['is_varianted', 'process'])
                ->make(true);
        }

        return view('products');
    }

    public function fetchProducts()
    {
        try {
            $size = 9598;
            Product::truncate();

            $batchSize = 200;
            $totalPages = ceil($size / $batchSize);
            for ($page = 0; $page <= $totalPages; $page++) {
                ProcessProductsBatch::dispatch($page, $batchSize);
            }

            return Output::ok(null, 'İşlem Başarılı');
        } catch (Exception $e) {
            return Output::fails($e->getMessage(), "Ürünler Çekilirken bir hata oluştu: {$e->getMessage()}");
        }
    }

    public function store($products)
    {
        foreach ($products['content'] as $product) {
            try {
                 if (!array_key_exists('productContentId', $product)) {
                    continue;
                }

                $this->processProduct($product);
            } catch (Exception $e) {
                Log::error("Ürün işlenirken hata oluştu: {$e->getMessage()}", ['product' => $product]);
            }
        }
    }

    private function processProduct($product)
    {
        $existingProduct = Product::where('product_code', $product['productContentId'])->first();

        if ($existingProduct) {
            $existingProduct->update(['is_varianted' => 1]);
            Log::info("Ürün güncellendi: " . $product['productContentId']);
        } else {
            $newProduct = Product::create([
                'barcode' => $product['barcode'],
                'product_code' => $product['productContentId'],
                'name' => $product['title'],
                'price' => $product['salePrice'],
                'stock_unit_type' => $product['stockUnitType'],
                'quantity' => $product['quantity'],
                'description' => $product['description'],
            ]);
            Log::info("Yeni ürün eklendi: " . $newProduct['product_code']);
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            $input = $request->all();
            $input['barcode'] = $product->barcode;

            $response = $this->trendyolService->updateProducts($input);
            if (isset($response['items']) && is_array($response['items']) && count($response['items']) > 0) {
                $firstItem = $response['items'][0];

                if ($firstItem['status'] === 'SUCCESS') {
                    DB::beginTransaction();
                    $product->update($input);
                    DB::commit();
                    return Output::ok($response, 'Ürün başarıyla güncellendi.');
                } else {
                    $failureReason = $firstItem['failureReasons'] ? $firstItem['failureReasons'][0] : ['Bilinmeyen hata'];
                    return Output::fails($failureReason, "Ürün güncellenirken hata oluştu. Hata: {$failureReason}");
                }
            }

            return Output::fails('Beklenmeyen yanıt formatı.', "Beklenmeyen yanıt formatı.", ['response' => $response]);
        } catch (Exception $e) {
            DB::rollBack();
            return Output::fails($e->getMessage(), "Ürün güncellenirken hata oluştu: {$e->getMessage()}");
        }
    }
}
