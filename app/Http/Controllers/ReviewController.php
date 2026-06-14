<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Http\Requests\StoreReviewRequest;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Product $product)
    {
        $validated = $request->validated();

        Review::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]
        );

        return redirect()->route('product.detail', $product->id)
            ->with('success', 'Review berhasil disimpan!');
    }

    public function destroy(Product $product)
    {
        Review::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->delete();

        return redirect()->route('product.detail', $product->id)
            ->with('success', 'Review berhasil dihapus.');
    }
}
