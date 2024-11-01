<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $service = Service::join('users','users.id','=','servicios.user_id')
        ->join('productos','productos.id','=','servicios.product_id')
        ->select(
            'servicios.id',
            'users.name as user_name',
            'productos.product_name as product_name',
            'productos.price as unit_price',
            Service::raw('servicios.quantity * productos.price as total_price'),
            'servicios.transaction_type as transaction_type',
            'servicios.created_at as created_at'
            )->get();
            // verificar si hay registros
            if($service->isEmpty()){
                return response()->json(['message'=>'No hay servicios registrados'], 404);
            }
            // devolver los resultados
            return response()->json(['Inventario:' => $service], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(),[
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:productos,id',
            'quantity' => 'required|integer|min:1',
            'transaction_type' => 'required|in:compra,venta'
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()], 400);
        }
        DB::beginTransaction();
        try{
            $product = Product::findOrFail($request->product_id);
            // actualizar cantidad en productos
            if($request->transaction_type === 'compra'){
                $product->quantity += $request->quantity;
            } elseif($request->transaction_type === 'venta'){
                // verificar que haya suficiente cantidad en productos
                if($product->quantity < $request->quantity){
                    return response()->json(['message'=> 'No hay suficiente cantidad para realizar la transaccion'],400);
                }
                $product->quantity -= $request->quantity;
            }
            $product->save();
            // crear el registro en servicios
            $service = Service::create([
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'transaction_type' => $request->transaction_type
            ]);
            DB::commit();
            return response()->json(['message'=> 'Servicio creado exitosamente','servicio' => $service], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message'=> 'Hubo un error al crear el servicio','error' => $e->getMessage()], 500);
        }
    }
}
