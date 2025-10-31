<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('id', 'desc')->get();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

  public function store(Request $request)
{
	
    $request->validate([
        'name' => 'required|string|max:255',
        'demissions' => 'required|string',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'profit_margin' => 'required|numeric|min:0',
        'discount' => 'required|numeric|min:0|max:100',
        'points' => 'nullable|string',
    ], [
        'name.required' => 'Product name is required.',
        'demissions.required' => 'Specification is required.',
        'image.required' => 'Product image is required.',
        'image.image' => 'The image must be an image file.',
        'image.max' => 'Image size must not exceed 2MB.',
        'profit_margin.required' => 'Profit margin is required.',
        'discount.required' => 'Discount percentage is required.',
    ]);

    // ✅ Image Upload
    $imagePath = null;
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('admin/assets/products'), $imageName);
        $imagePath = 'admin/assets/products/' . $imageName;
    }

    // ✅ Clean points (remove "Points" text etc.)
    $cleanPoints = preg_replace('/[^0-9.]/', '', $request->points);
    // ✅ Create Product
    Product::create([
        'name' => $request->name,
        'demissions' => $request->demissions,
        'image' => $imagePath,
        'profit_margin' => $request->profit_margin,
        'discount' => $request->discount,
        'points_per_sale' => $cleanPoints,
    ]);

    return redirect()->route('product.index')->with('success', 'Product created successfully.');
}


    public function edit($id)
    {
        $product = Product::findOrFail($id);

        return view('admin.products.edit', compact('product'));
    }

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'demissions' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'profit_margin' => 'required|numeric|min:0',
        'discount' => 'required|numeric|min:0|max:100',
        // ✅ points can come with "100 Points" (string), so no numeric rule here
        'points' => 'nullable|string',
    ], [
        'name.required' => 'Product name is required.',
        'demissions.required' => 'Demissions are required.',
        'image.image' => 'The image must be an image file.',
        'image.max' => 'Image size must not exceed 2MB.',
        'points.required' => 'Points per sale are required.',
        'profit_margin.required' => 'Profit margin is required.',
        'discount.required' => 'Discount percentage is required.',
    ]);

    $product = Product::findOrFail($id);

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time().'_'.$image->getClientOriginalName();
        $image->move(public_path('admin/assets/products'), $imageName);
        $product->image = 'admin/assets/products/'.$imageName;
    }

    // ✅ Clean "Points" from input
    $cleanPoints = preg_replace('/[^0-9.]/', '', $request->points);

    $product->name = $request->name;
    $product->demissions = $request->demissions;
    $product->profit_margin = $request->profit_margin;
    $product->discount = $request->discount;
    $product->points_per_sale = $cleanPoints;

    $product->save();

    return redirect()->route('product.index')->with('success', 'Product updated successfully.');
}

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product deleted successfully.');
    }

    public function ProductDetails($id)
    {
        $details = ProductBatch::where('product_id', $id)->latest()->get();
        $product = Product::find($id);

        return view('admin.products.details', compact('details', 'id', 'product'));
    }

    public function CreateProductDetails($id)
    {
        $details = ProductBatch::where('product_id', $id)->latest()->get();
        $product = Product::find($id);

        return view('admin.products.createdetails', compact('details', 'id', 'product'));
    }

    public function ScanStore(Request $request)
    {
        $request->validate([
            'scan' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
        ]);

        ProductBatch::create([
            'product_id' => $request->product_id,
            'scan_code' => $request->scan,
        ]);

        return redirect()->route('product.index')->with('success', 'SN code created successfully.');
    }

    public function storeBatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'scan_code' => 'required|string|max:255|unique:product_batches,scan_code',
        ], [
            'scan_code.unique' => 'This SN code already exists.',
            'scan_code.required' => 'The SN code field is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ProductBatch::create([
            'product_id' => $request->product_id,
            'scan_code' => $request->scan_code,
        ]);

        return redirect()->route('product.index')->with('success', 'Product SN code added successfully.');
    }

    public function deleteBatch($id)
    {
        $batch = ProductBatch::findOrFail($id);
        $batch->delete();

        return redirect()->back()->with('success', 'SN code deleted successfully.');
    }
}
