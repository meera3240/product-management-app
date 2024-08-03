<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ProductHelper;
use App\Http\Requests\Admin\ImportProductsRequest;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use function Illuminate\Validation\message;

class ProductController extends Controller
{

    /**
     * List all products
     *
     * @Route("/products")
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $productList = Product::query();

        // Filter conditions
        if ($user->hasRole('sub-admin')) {
            $productList->where('user_id',$user->id);
        }
        if ($request->has('name') && !empty($request->input('name'))) {
            $productList->where('name', 'like', "%{$request->input('name')}%");
        }
        if ($request->has('status') && !empty($request->input('status'))) {
            $productList->where('status', $request->input('status'));
        }
        if ($request->has('start_date') && !empty($request->input('start_date'))) {
            $productList->whereDate('created_at', '>=', Carbon::parse($request->input('start_date')));
        }
        if ($request->has('end_date') && !empty($request->input('end_date'))) {
            $productList->whereDate('created_at', '<=', Carbon::parse($request->input('end_date')));
        }

        $products = $productList->orderBy('created_at', 'desc')->paginate(5);
        $products->transform(function ($product) {
            $product->encrypted_id = Crypt::encrypt($product->id);
            return $product;
        });
        return view('pages.admin.products.index', compact('products'));
    }

    /**
     * Create new produt page
     *
     * @Route("/admin/products")
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function create()
    {
        return view('pages.admin.products.create');
    }

    /**
     * Create a new product store instance
     *
     * @param ProductStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductStoreRequest $request)
    {
        $product = new Product();
        $product->name = $request->name;
        $product->code = ProductHelper::generateProductCode();
        $product->description = $request->description;
        $product->price = $request->price;
        $product->status = $request->status;
        $product->user_id = Auth::id();
        $product->save();

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products','public');
                $imagePaths[] = [
                    'product_id' => $product->id,
                    'path' => $imagePath,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            ProductImage::insert($imagePaths);
        }
        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Updates a product
     *
     * @param $encryptedId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse
     */
    public function edit($encryptedId)
    {
        try {
            // Decrypt the ID
            $productId = Crypt::decrypt($encryptedId);
            $product = Product::where('user_id',Auth::id())->where('id', $productId)->first();

            if (Auth::user()->hasRole('admin') || (Auth::user()->hasRole('sub-admin') && $product->user_id == Auth::id())) {
                if(is_null($product)){
                    return redirect()->route('products.index')->with('error', 'Invalid Request');
                }
                $product->images = ProductImage::where('product_id', $product->id)->get();
                return view('pages.admin.products.edit', compact('product'));
            } else {
                return redirect()->route('products.index')->with('error', 'Unauthorized access.');
            }
        } catch (DecryptException $e) {
            // Handle decryption error
            return redirect()->route('products.index')->with('error', 'Invalid Request.');
        }
    }

    /**
     * Update Product
     *
     * @param ProductUpdateRequest $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $product = Product::where('user_id',Auth::id())->where('id',$product->id)->first();
        if (Auth::user()->hasRole('admin') || (Auth::user()->hasRole('sub-admin') && $product->user_id == Auth::id())) {
            if(! is_null($product)){
                Product::where('id', $product->id)->update([
                    'name' => $request->name,
                    'price' => $request->price,
                    'status' => $request->status,
                    'description' => $request->description,
                ]);

                if ($request->hasFile('images')) {
                    $imagePaths = [];
                    foreach ($request->file('images') as $image) {
                        $imagePath = $image->store('products','public');
                        $imagePaths[] = [
                            'product_id' => $product->id,
                            'path' => $imagePath,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                    ProductImage::insert($imagePaths);
                }
                return redirect()->route('products.index')->with('success', 'Product updated successfully.');
            } else {
                return redirect()->route('products.index')->with('error', 'Invalid Request');
            }
        } else {
            return redirect()->route('products.index')->with('error', 'Unauthorized access.');
        }

    }

    /**
     * Return products view page
     * @param $encryptedId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse
     */
    public function show($encryptedId)
    {
        try {
            // Decrypt the ID
            $productId = Crypt::decrypt($encryptedId);
            $product = Product::where('user_id',Auth::id())->where('id', $productId)->first();
            if (is_null($product)) {
                return redirect()->route('products.index')->with('error', 'Invalid Request');
            }
            $product->images = ProductImage::where('product_id', $product->id)->get();
            return view('pages.admin.products.show', compact('product'));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Handle decryption error
            return redirect()->route('products.index')->with('error', 'Invalid Request');
        }
    }

    /**
     * Delete a product
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        $product = Product::where('user_id',Auth::id())->where('id',$product->id)->first();
        if (Auth::user()->hasRole('admin') || (Auth::user()->hasRole('sub-admin') && $product->user_id == Auth::id())) {
            if (is_null($product)) {
                return redirect()->route('products.index')->with('error', 'Invalid Request');
            } else {
                Product::where('id',$product->id)->delete();
                return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
            }
        } else {
            return redirect()->route('products.index')->with('error', 'Unauthorized access.');
        }
    }

    /**
     * Import Product
     *
     * @param ImportProductsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(ImportProductsRequest $request)
    {
        Excel::import(new ProductsImport, $request->file('file'));
        return redirect()->route('products.index')->with('success', 'Products imported successfully.');
    }

    /**
     * Bulk delete products based on selected products by user
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
        ]);
        $productIds = $request->input('product_ids');
        $images = ProductImage::whereIn('product_id', $productIds)->get();
        // Remove image from storage
        foreach ($images as $image) {
            Storage::delete($image->path);
        }
        // Delete product and its images from database
        ProductImage::whereIn('product_id', $productIds)->delete();
        Product::whereIn('id', $productIds)->delete();
        return redirect()->route('products.index')->with('success', 'Products deleted successfully.');
    }

}
