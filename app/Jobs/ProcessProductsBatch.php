<?php

namespace App\Jobs;

use App\Helpers\Output;
use App\Http\Controllers\ProductController;
use App\Services\TrendyolService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessProductsBatch implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    protected $page;
    protected $size;


    public function __construct($page, $size)
    {
        $this->page = $page;
        $this->size = $size;
    }

    public function handle(TrendyolService $trendyolService)
    {
        $startTime = microtime(true);

        try {
            $productController = new ProductController($trendyolService);
            $products = $trendyolService->getProducts($this->page, $this->size);
            $productController->store($products);
            $status = true;
        } catch (Exception $e) {
            $status = false;
            Output::fails($e->getMessage());
        } finally {

            $endTime = microtime(true);
            $duration = $endTime - $startTime;

            if ($status) {
                Log::info("Kuyruk işlemi tamamlandı. Süre: " . round($duration, 2) . " saniye.");
            } else {
                Log::error("Kuyruk işlemi tamamlanamadı. Süre: " . round($duration, 2) . " saniye. Hata: " . $e->getMessage());
            }
        }
    }
}
