<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::query()->select('id', 'title', 'description', 'image')->get();

        return response()->json(
            [
                'success' => true,
                'data'    => [
                    'products' => $products
                ]
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::query()->select('id','title');

        return response()->json(
            [
                'success' => true,
                'data'    => [
                    'categories' => $categories
                ]
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $categories = Category::query()->pluck('id')->toArray();
        $this->validate($request, [
            'title' => 'required|max:255',
            'image' => 'image',
            'category_id' => ['integer', 'required', Rule::in($categories)]
        ]);

        $product = (new Product)->fill([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'description' => $request->description,
        ]);

        if ($request->hasFile('image')) {
            $path = Storage::putFileAs(
                'products',
                $request->image,
                'img' . time() . random_int(0, 9) . random_int(0, 9) . '.' . $request->image->extension()
            );
            $product->image = $path;
        }

        $product->save();

        return response()->json(
            [
                'success' => true,
                'data'    => [
                    'product' => $product
                ]
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::query()->findOrFail($id);
        $categories = Category::query()->select('id','title');

        return response()->json(
            [
                'success' => true,
                'data'    => [
                    'product' => $product,
                    'categories' => $categories
                ]
            ]
        );
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $categories = Category::query()->pluck('id')->toArray();
        $this->validate($request, [
            'title' => 'required|max:255',
            'image' => 'image',
            'category_id' => ['integer', 'required', Rule::in($categories)]
        ]);

        $product = Product::query()->find($id);
        if ($product === null) {
            return response()->json(['success' => false]);
        }

        $product->fill([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'description' => $request->description,
        ]);

        if ($request->hasFile('image')) {
            $path = Storage::putFileAs(
                'products',
                $request->image,
                'img' . time() . random_int(0, 9) . random_int(0, 9) . '.' . $request->image->extension()
            );
            $product->image = $path;
        }

        $product->save();

        return response()->json(
            [
                'success' => true,
                'data'    => [
                    'product' => $product
                ]
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::query()->find($id);
        if ($product === null) {
            return response()->json(['success' => false]);
        }
        $product->delete();
        return response()->json(['success' => true]);
    }
}
