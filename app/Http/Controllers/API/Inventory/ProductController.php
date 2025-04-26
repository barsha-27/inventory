<?php

namespace App\Http\Controllers\API\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf; // For PDF export
use Illuminate\Support\Facades\Storage; // Optional: for storing the files

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::with('category')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'quantity' => 'sometimes|integer',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $product->update($validated);
        return response()->json($product);
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    public function byCategory($categoryId)
    {
        $products = Product::with('category')->where('category_id', $categoryId)->get();
        return response()->json($products);
    }

    // Add this method for CSV export
public function exportCsv()
{
    $products = Product::with('category')->get();

    $filename = 'products.csv';
    $handle = fopen($filename, 'w+');
    fputcsv($handle, ['ID', 'Name', 'Price', 'Quantity', 'Category']);

    foreach ($products as $product) {
        fputcsv($handle, [
            $product->id,
            $product->name,
            $product->price,
            $product->quantity,
            $product->category->name ?? 'N/A',
        ]);
    }

    fclose($handle);

    return response()->download($filename)->deleteFileAfterSend(true);
}

// Add this method for PDF export
public function exportPdf()
{
    $products = Product::with('category')->get();
    $pdf = Pdf::loadView('exports.products', compact('products'));
    return $pdf->download('products.pdf');
}
}
