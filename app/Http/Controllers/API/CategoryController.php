<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Category;
use Validator;
use App\Http\Resources\Category as CategoryResource;

class CategoryController extends BaseController
{
    public function index(Request $request)
    {
        $category = Category::where(function ($query) use ($request) { 
            if($request->search){
                $query->where('category_name','LIKE','%'.$request->search.'%');
            }})->where(['is_deleted' => 0])->orderBy('id', $request->sort ? 'ASC' : 'DESC')
            ->paginate($request->per_page);
            
        return $this->response(($category), 'Category retrieved successfully.');
          

    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'category_name' => 'required',
            'category_image' => 'required|image|mimes:jpg,png,jpeg', 
        
        ]);

        if($validator->fails()){
           return $this->sendError('Validation Error.', $validator->errors());
        }

        $path = $input['category_image']->store('public/images');
        $category = Category::create(['category_name' => $input['category_name'], 'category_image' => $path]);

        return $this->sendResponse(new CategoryResource($category), 'Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $validator = validator::make($input,[
            'category_name' => 'required',
            'category_image' => 'nullable|image|mimes:jpg,png,jpeg', 
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
       
        $category = Category::find($id);
        $category->category_name = $input['category_name'];
        if($request->category_image){
            $image_path = $request->file('category_image')->store('category_image','public');
            $category->category_image = $image_path;
        }
        
        $category->save();

        return $this->sendResponse(new CategoryResource($category), 'Category updated successfully.');
    }

    public function destroy($id)
    {
        Category::where('id', $id)->update(['is_deleted' => 1]);
        return $this->sendResponse([], 'Category deleted successfully.');
    }
}