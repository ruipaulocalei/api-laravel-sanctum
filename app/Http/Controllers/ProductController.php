<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = Product::all();
        return response()->json($product);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductRequest $request)
    {
        // $data = $request->all();
        // $data['image'] = $request->image->store('products-img', 'public');
        // $product->user_id = auth()->user()->id;
        // $product = Product::create($data);
        // return $product;
        $imagem = $request->image->store('product-images', 'public');
        $product = auth()->user()->products()->create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagem,
        ]);

        return response()->json($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response([
                'message' => 'Product not found'
            ], 404);
        }
        return $product;
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
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
        ]);
        $product = Product::find($id);
        $imagem = $request->image->store('product-images', 'public');;
        if (!$product) {
            return response([
                'message' => 'Product not found'
            ], 404);
        }

        $product->name = $data['name'];
        $product->price = $data['price'];
        $product->description = $data['description'];

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($product->image);
            $product->image = $imagem;
        }

        $product->save();
        return $product;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        // $this->authorize('delete', $id);
        if (!$product) {
            return response([
                'message' => 'Product not found'
            ], 404);
        }
        if (Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return response([
            'message' => 'Product deleted'
        ], 200);
    }

    public function search($name)
    {
        return Product::where('name', 'like', '%' . $name . '%')->paginate(5);
    }
}
