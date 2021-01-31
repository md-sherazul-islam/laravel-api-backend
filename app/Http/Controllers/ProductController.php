<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function products(){
        $products = Product::get()->all();
        return response()->json($products);
    }
    public function addProduct(Request $request){
        $this->validate($request,[
            'title'         => 'required',
            'description'   => 'required',
            'price'         => 'required',
            'image'         => 'required'
        ]);
        
        $image_64 = $request['image']; //your base64 encoded data

        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
      
        $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
      
      // find substring fro replace here eg: data:image/png;base64,
      
       $image = str_replace($replace, '', $image_64); 
      
       $image = str_replace(' ', '+', $image); 
      
       $imageName = Str::random(10).'.'.$extension;
      
       Storage::disk('public')->put($imageName, base64_decode($image));

        $added = Product::create([
            'Title'         => $request->title,
            'Description'   => $request->description,
            'Price'         => $request->price,
            'Image'         => $imageName
        ]);
        if($added){
            return response()->json(['success' => 'Product Added'],200);
        }
    }
    public function editProduct(Request $request){
        $this->validate($request,[
            'title'         => 'required',
            'description'   => 'required',
            'price'         => 'required',
            'image'         => 'required'
        ]);

        $image_64 = $request['image']; //your base64 encoded data

        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
      
        $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
      
      // find substring fro replace here eg: data:image/png;base64,
      
       $image = str_replace($replace, '', $image_64); 
      
       $image = str_replace(' ', '+', $image); 
      
       $imageName = Str::random(10).'.'.$extension;
      
       Storage::disk('public')->put($imageName, base64_decode($image));

        $find = Product::where('id',$request->id)->first();
        $updated = $find->update([
            'Title'         => $request->title,
            'Description'   => $request->description,
            'Price'         => $request->price,
            'Image'         => $imageName
        ]);
        if($updated){
            return response()->json(['success'=>'Successfully Updated','data'=>$updated],200);
        }
    }
    public function deleteProduct($id){
        $find = Product::where('id',$id)->first();
        if($find->delete()){
            return response()->json(['success'=>'Product Deleted'],200);
        }
    }
}
