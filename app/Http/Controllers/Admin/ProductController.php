<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            'points' => 'required|integer|min:0',
        ], [
            'name.required' => 'Product name is required.',
            'demissions.required' => 'Demissions are required.',
            'image.required' => 'Product image is required.',
             'image.max' => 'Image size must not exceed 2MB.',
            'points.required' => 'Points per sale are required.',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('admin/assets/products'), $imageName);
            $imagePath = 'admin/assets/products/' . $imageName;
        }

        Product::create([
            'name' => $request->name,
            'demissions' => $request->demissions,
            'image' => $imagePath ?? null,
            'points_per_sale' => $request->points,
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
            'points' => 'required|integer|min:0',
        ], [
            'name.required' => 'Product name is required.',
            'demissions.required' => 'Demissions are required.',
            'image.image' => 'The image must be an image file.',
             'image.max' => 'Image size must not exceed 2MB.',
            'points.required' => 'Points per sale are required.',
        ]);

        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('admin/assets/products'), $imageName);
            $product->image = 'admin/assets/products/' . $imageName;
        }

        $product->name = $request->name;
        $product->demissions = $request->demissions;
        $product->points_per_sale = $request->points;
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
            'product_id' => 'required|exists:products,id'
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

