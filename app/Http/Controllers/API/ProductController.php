<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Product;
use Validator;
use App\Http\Resources\Product as ProductResource;
use App\Models\Image;

class ProductController extends BaseController
{
    public function index()
    {  
        $products = Product::leftJoin('categories', 'categories.id', '=', 'products.category_id')
        ->select('products.unique_id', 'products.product_name', 'products.price', 'products.quantity', 'categories.category_name')
        ->get();
        
        return $this->response(($products), 'Products retrieved successfully.');
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required',
            'price' => 'required',
            'quantity' => 'required',
        
        ]);

        if($validator->fails()){
           // return $validator->errors();
           return $this->sendError('Validation Error.', $validator->errors());
        }

        $uid = 'PRODUCT';
        $input['unique_id'] = $uid.'-'.rand(100000,999999); 

        $product = Product::create($input);

        foreach($input['images'] as $mediaFiles)
        {
            $path = $mediaFiles->store('public/images');
            
            Image::create(['product_id' => $product->id, 'image' => $path]);
        }
        return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
    }
    public function show($id)
    {
        $product = Product::find($id);
  
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
   
        return $this->response(new ProductResource($product), 'Product retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
  
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
        
        $input = $request->all();
       
        $validator = validator::make($input,[
            'category_id' => 'required',
            'unique_id' => 'required',
            'product_name' => 'required',
            'price' => 'required',
            'quantity' => 'required',
            'image' => 'required|image|mimes:jpg,png,jpeg',
        ]);
        

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
       
        $product = Product::find($id);
        $product->category_id = $input['category_id'];
        $product->unique_id = $input['unique_id'];
        $product->product_name = $input['product_name'];
        $product->price = $input['price'];
        $product->quantity = $input['quantity'];
        if($request->image){
            $image_path = $request->file('image')->store('image','public');
            $product->image = $image_path;
        }    
        $product->save();
        
        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }
    public function delete($id)
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
        $product->delete();

        return $this->sendResponse([], 'Product deleted successfully.');
    }
}