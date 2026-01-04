<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProductPrintController extends Controller
{
    public function __invoke(Request $request)
    {
        $products = Product::query()
            ->orderByDesc('created_at')
            ->get();

        $pdf = Pdf::loadView('print.products', [
            'products' => $products,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('products.pdf');
    }
}
