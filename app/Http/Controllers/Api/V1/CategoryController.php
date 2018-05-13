<?php

namespace App\Http\Controllers\Api\V1;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        //Массив для проверки category_id.
        //Параметр должен быть либо id одной из существующих категорий либо нулем если у категории не должно быть родителя
        $validationArr = Category::all()->pluck('id')->toArray();
        array_push($validationArr, 0);
        $this->validate($request, [
            'title' => 'required|max:255',
            'category_id' => ['integer', Rule::in($validationArr)]
        ]);

        if ($request->isMethod('put')) {
            $category = Category::query()->findOrFail($request->category_id);
        } else {
            $category = new Category;
        }

        $category->title = $request->title;
        $category->save();
        if ($request->has('parent_id')) {
            if ($request->parent_id === '0') {
                $category->makeRoot();
            } else {
                $category->makeChildOf(Category::query()->find($request->parent_id));
            }
        }

        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        $category = Category::query()->findOrFail($id);
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $categories = Category::query()->findOrFail($id)->DescendantsAndSelf()->pluck('id')->toArray();
            //Делаем все связанные продукты сиротами
            Product::query()->whereIn('category_id', $categories)->update(['category_id' => null]);
            //Удаляем целевую категорию. Дочерние категории удалятся автоматически
            $category = Category::query()->find($id);
            $category->delete();

            DB::commit();

            return new CategoryResource($category);
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {

            DB::rollback();
            return response()->json([
                'errors' => [
                    'status' => 400,
                    'title' => 'Database error'
                ]
            ], 400);
        }

    }
}
