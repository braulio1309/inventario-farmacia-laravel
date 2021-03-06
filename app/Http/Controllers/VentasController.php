<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\VentasExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Models\Ventas;
use App\Models\Precios_productos;
use App\Models\Lotes;
use App\Models\Productos;
use App\Models\Clientes;
use App\Models\Detalle_ventas;



class VentasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function export($filtro = null, $fecha_inicio = null, $fecha_final= null, $id= null){
        return Excel::download(new VentasExport($filtro = null, $fecha_inicio = null, $fecha_final= null, $id= null), 'ventas.xlsx');
    }

    public function imprimirFactura(){
        $pdf = \PDF::loadView('ejemplo');
        return $pdf->download('ejemplo.pdf');
   }

   public function fechaVista(){
       return view('Ventas/fecha');
   }

   public function getVentas(Request $request, $filtro = null, $fecha_inicio = null, $fecha_final= null, $id= null){
        
        if($filtro == null){//Todas
            $ventas = Ventas::join('clientes', 'clientes.id', '=', 'ventas.cliente_id')
            ->join('users', 'users.id', '=', 'ventas.user_id')
            ->select('ventas.id', 'clientes.nombre AS cliente', 'ventas.monto', 'users.name AS Vendedor', 'ventas.created_at AS Fecha')
            ->get();
        }else
            if($filtro == 1){//Por cliente
                $ventas = Ventas::join('clientes', 'clientes.id', '=', 'ventas.cliente_id')
                ->join('users', 'users.id', '=', 'ventas.user_id')
                ->select('ventas.id', 'clientes.nombre AS cliente', 'ventas.monto', 'users.name AS Vendedor', 'ventas.created_at AS Fecha')
                            ->where('ventas.cliente_id', $id)
                            ->get();
            }else
                if($filtro == 2){//Por fecha
                    $ventas = Ventas::join('clientes', 'clientes.id', '=', 'ventas.cliente_id')
                    ->join('users', 'users.id', '=', 'ventas.user_id')
                    ->select('ventas.id', 'clientes.nombre AS cliente', 'ventas.monto', 'users.name AS Vendedor', 'ventas.created_at AS Fecha')
                        ->whereBetween('ventas.created_at', [$fecha_inicio, $fecha_final])
                        ->get();
                }else
                    if($filtro == 3){//Por tipo de cliente
                        $ventas = Ventas::join('clientes', 'clientes.id', '=', 'ventas.cliente_id')
                        ->join('users', 'users.id', '=', 'ventas.user_id')
                        ->select('ventas.id', 'clientes.nombre AS cliente', 'ventas.monto', 'users.name AS Vendedor', 'ventas.created_at AS Fecha')
                            ->where('clientes.tipo', $id)
                            ->get();
                    }else
                        if($filtro == 4){ //M??s recientes
                            $ventas = Ventas::join('clientes', 'clientes.id', '=', 'ventas.cliente_id')
                            ->join('users', 'users.id', '=', 'ventas.user_id')
                            ->select('ventas.id', 'clientes.nombre AS cliente', 'ventas.monto', 'users.name AS Vendedor', 'ventas.created_at AS Fecha')
                            ->orderby('ventas.created_at', 'asc')
                                ->get();
                        }else
                            if($filtro == 5){// M??s antiguos
                                $ventas = Ventas::join('clientes', 'clientes.id', '=', 'ventas.cliente_id')
                                ->join('users', 'users.id', '=', 'ventas.user_id')
                                ->select('ventas.id', 'clientes.nombre AS cliente', 'ventas.monto', 'users.name AS Vendedor', 'ventas.created_at AS Fecha')
                                ->orderby('ventas.created_at', 'desc')
                                ->get();
                            }else
                                if($filtro == 6){
                                    $hoy=Ventas::whereDate('created_at', '=', Carbon::now()->format('Y-m-d'))->get();

                                }


        return view('Ventas/mostrar', [
            'ventas' => $ventas,
            'filtro' => $filtro,
            'fecha_inicial' => $fecha_inicio,
            'fecha_final' => $fecha_final,
            'id' => $id
        ]);
    }

    public function create(){
        $lotes = Lotes::join('precios_productos', 'precios_productos.id','=', 'lotes.precio_id')
        ->select('lotes.*', 'precios_productos.precio as precio')
        ->get();
       
        $clientes = Clientes::all();

        return view('Ventas/create', [
            'lotes'     => $lotes,
            'clientes'  => $clientes
        ]);


    }

    public function createVenta(Request $request){
       $unidades = $request->input('unidades');
       $producto_id = $request->input('lote_id');

       $venta = new Ventas();
       $venta->cliente_id = $request->input('cliente_id');
       $venta->monto = 0;
       $venta->impuesto = 0.13;
       $venta->descuento = 0;
       $venta->user_id = Auth::user()->id;
       $venta->save(); //se crea la venta

       $i = 0;
       $monto_final = 0;
       foreach($producto_id as $lote){
            $lotes = Lotes::where('lotes.id', $lote)
            ->join('precios_productos', 'precios_productos.id','=', 'lotes.precio_id')
            ->select('lotes.*', 'precios_productos.precio as precio', 'lotes.id as id')
            ->first();

            if($unidades[$i] > $lotes->unidades){

                $request->session()->flash('alert-danger', "No hay suficientes $lotes->nombre, quedan solo $lotes->unidades ");
                return redirect()->back();
            }
           
            $lotes->unidades = $lotes->unidades - $unidades[$i];
            $lotes->save();

            $detalle = new Detalle_ventas();
            $detalle->producto_id = $lotes->producto_id;
            $detalle->venta_id = $venta->id;
            $detalle->lote_id = $lotes->id;
            
            $detalle->unidades = $unidades[$i];
            $detalle->save();
            
            $monto_final += $lotes->precio * $unidades[$i];
       }

       $monto_impuesto = $monto_final *0.13;
       $monto_final = $monto_final + $monto_impuesto;

       $venta->monto = $monto_final;
       $venta->save();

       $request->session()->flash('alert-success', 'Venta realizada con exito!');
       return redirect()->route('ventas.lista', ['filtro' => 4]);

       
    }


}
