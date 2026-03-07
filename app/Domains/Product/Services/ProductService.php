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
            $storedPath = Storage::disk('public')->putFile('products', $data['image']);
            if (!$storedPath) {
                throw new \RuntimeException('Falha ao enviar imagem do produto.');
            }

            $data['image_path'] = $storedPath;
        }

        // Compatibilidade com schema legado que ainda possui coluna "price" obrigatoria.
        $data['price'] = $data['sale_price'] ?? $data['price'] ?? 0;

        unset($data['image']);

        return Product::create($data);
    }

    public function replaceImage(Product $product, UploadedFile $file): Product
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $storedPath = Storage::disk('public')->putFile('products', $file);
        if (!$storedPath) {
            throw new \RuntimeException('Falha ao substituir imagem do produto.');
        }

        $product->image_path = $storedPath;
        $product->save();

        return $product;
    }
}
