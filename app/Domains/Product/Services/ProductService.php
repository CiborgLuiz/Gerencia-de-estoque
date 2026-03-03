<?php

namespace App\Domains\Product\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function create(array $data): Product
    {
        if (($data['image'] ?? null) instanceof UploadedFile) {
            $data['image_path'] = $data['image']->store('products', 'public');
        }

        unset($data['image']);

        return Product::create($data);
    }

    public function replaceImage(Product $product, UploadedFile $file): Product
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->image_path = $file->store('products', 'public');
        $product->save();

        return $product;
    }
}
