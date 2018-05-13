<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Product;
use App\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->category)) {
            $products = Product::query()
                ->where('category_id', '=', $request->category)
                ->paginate($request->perPage ?: 15);
        } else {
            $products = Product::query()->paginate($request->perPage ?: 15);
        }

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        //category_id должен существовать в таблице categories
        //или быть нулем если мы хотим убрать категорию товара
        $validationArray = Category::query()->pluck('id')->toArray();
        array_push($validationArray, 0);
        $this->validate($request, [
            'title' => 'required|max:255',
            'image' => 'image',
            'category_id' => ['integer', Rule::in($validationArray)],
            'description' => 'required|string'
        ]);

        //Определение действия
        if ($request->isMethod('put')) {
            $product = Product::query()->findOrfail($request->product_id);
        } else {
            $product = new Product();
        }
        $product->title = $request->input('title');
        $product->description = $request->input('description');
        //связь с категорией
        if ($request->has('category_id')) {
            if ($request->input('category_id') === '0') {
                $product->category_id = $request->input('category_id');
            } else {
                $product->category_id = null;
            }
        }
        //изображение
        if ($request->hasFile('image')) {
            $path = Storage::putFileAs(
                'products',
                $request->image,
                'img' . time() . random_int(0, 9) . random_int(0, 9) . '.' . $request->image->extension()
            );
            $product->image = $path;
        }

        $product->save();
        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        $product = Product::query()->findOrFail($id);
        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $product = Product::query()->findOrFail($id);
        $product->delete();
        return new ProductResource($product);

    }
}
