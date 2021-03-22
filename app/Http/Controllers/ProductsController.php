<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Product;


class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::where("user_id",auth()->user()->id)->paginate(15);
        return response()->json(["products"=>$products]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data["user_id"] = auth()->user()->id;

        $uniqueUserCodeRule =  Rule::unique('products')->where(function ($query) use 
                ($data){
                    return $query->where('user_id', $data['user_id']??'')
                    ->where('code', $data['code']??'');
                });

        $validator = Validator::make($data, [
            'code'          => 'required|alpha_num|max:20|'.$uniqueUserCodeRule,
            'name'          => 'required|string|max:250',
            'description'   => 'required|string|min:10',
            'price'         => 'required|numeric|min:0',
            'stock'         => 'required|numeric|min:0',
            'image'         => "required|url"
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        
        $product = Product::create($data);

        return response()->json(compact('product'),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $search = [
            "id" => $id,
            "user_id" => auth()->user()->id
        ];
        $product = Product::where($search)->first();
        if (!isset($product->id)) {
            return response()->json(["message"=>"Not Found"],404);
        }
        return response()->json(["product"=>$product]);
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

        $product = Product::where([
            "user_id" => auth()->user()->id,
            "id"=>$id
        ])->first();

        if (!isset($product->id)) {
            return response()->json(["message"=>"Not Found"],404);
        }

        $data = $request->all();
        $data["user_id"] = auth()->user()->id;

        $uniqueUserCodeRule =  Rule::unique('products')->where(function ($query) use 
                ($data){
                    return $query->where('user_id', $data['user_id']??'')
                    ->where('code', $data['code']??'');
                });

        $validator = Validator::make($data, [
            'code'          => 'alpha_num|max:20|'.$uniqueUserCodeRule,
            'name'          => 'string|max:250',
            'description'   => 'string|min:10',
            'price'         => 'numeric|min:0',
            'stock'         => 'numeric|min:0',
            'image'         => "url"
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        $product->update($data);

        return response()->json(compact('product'),201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $search = [
            "id" => $id,
            "user_id" => auth()->user()->id
        ];
        $product = Product::where($search)->first();
        if (!isset($product->id)) {
            return response()->json(["message"=>"Not Found"],404);
        }
        $product->delete();
        return response()->json(["message"=>"deleted successfully"]);
    }
}
