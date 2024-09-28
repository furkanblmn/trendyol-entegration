<?php

namespace App\Services;

use App\Helpers\Output;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrendyolService
{
    protected $supplierId = "732323";
    protected $apiKey = "m5Yw7mrhl6gzGqnRdUNm";
    protected $secretKey = "pmdibpKG2BKsKQjJ0AQe";

    private function sendRequest($method, $url, $data = null)
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, $this->secretKey)
                ->withHeaders(['User-Agent' => "{$this->supplierId} - SelfIntegration"])
                ->$method("https://api.trendyol.com/sapigw/suppliers/{$this->supplierId}/products{$url}", $data);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (Exception $e) {
            return Output::fails($e->getMessage(), "Trendyol SendRequest. Hata: {$e->getMessage()}");
        }
    }

    public function getProducts(int $page = 0, int $size = 1)
    {
        $url = "?page={$page}&size={$size}";
        return $this->sendRequest('get', $url);
    }

    public function updateProducts(array $input)
    {
        $url = "/price-and-inventory";

        $data = [
            'items' => [
                [
                    "barcode" => $input['barcode'],
                    "quantity" => $input['quantity'],
                    "salePrice" => $input['price'],
                    "listPrice" => $input['price']
                ]
            ]
        ];


        $response = $this->sendRequest('post', $url, $data);

        if (isset($response['batchRequestId'])) {
            sleep(1);
            return $this->checkBatchRequest($response['batchRequestId']);
        }

        return Output::fails('BatchRequest ID bulunamadı.');
    }

    public function checkBatchRequest($batchRequestId)
    {
        $url = "/batch-requests/{$batchRequestId}";

        return $this->sendRequest('get', $url);
    }
}
