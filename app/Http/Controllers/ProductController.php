<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $product = Product::all();
        // validamos si hay registros
        if ($product->isEmpty()) {
            return response()->json([
                'message' => 'No products found'
            ], 404);
        }
        // si hay registros, retornamos los productos
        return response()->json(['products' => $product], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => ['required', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
        ]);
        //verificamos si se cumplen o no las validaciones
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error: ',
                'errors' => $validator->errors()
            ], 400);
        }
        //si se cumplen las validaciones, creamos el usuario
        $product = Product::create($request->all());
        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $product = Product::find($id);
        //validamos que el producto exista
        if ($product != null) {
            return response()->json($product, 200);
        }
        # si el registro no existe retornamos una respuesta de no encontrado
        return response()->json(['message' => 'Not Found: Producto no encontrado'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $product = Product::find($id);
        // validamos que el producto exista
        if (!$product) {
            return response()->json(['message' => 'Not Found: Producto no encontrado'], 404);
        }
        // validamos si se cumplen o no las validaciones
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => ['required', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
        ]);
        //verificamos si se cumplen si no las validaciones
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error: ',
                'errors' => $validator->errors()
            ], 400);
        }
        // actualizamos los campos solo si estan presentes en el request
        
            $product->product_name = $request->product_name;
            $product->quantity = $request->quantity;
            $product->price = $request->price;
        // guardamos los cambios
        $product->save();
        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $product = Product::find($id);
        // validamos que el producto exista
        if (!$product) {
            return response()->json(['message' => 'Not Found: Producto no encontrado'], 404);
        }
        // eliminamos el producto
        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully'
        ], 204);
    }

    public function searchProductByNameLike(string $name)
    {
        //
        $product = Product::where('product_name', 'LIKE', '%' . $name . '%')->get();

        if (count($product) > 0) {
            // mandar todos los productos con status 200
            return response()->json($product, 200);
        }
        // si no hay productos que coincidan con el nombre mandar una respuesta de no encontrado
        return response()->json(['message' => 'Not Found: Producto no encontrado'], 404);
    }

    public function searchProductByPriceRange(float $min, float $max)
    {
        //
        $product = Product::whereBetween('price', [$min, $max])->get();

        if (count($product) > 0) {
            // mandar todos los productos con status 200
            return response()->json($product, 200);
        }
        // si no hay productos que coincidan con el rango de precios mandar una respuesta de no encontrado
        return response()->json(['message' => 'Not Found: Producto no encontrado'], 404);
    }
}
