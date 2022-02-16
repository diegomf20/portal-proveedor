<?php

namespace App\Http\Controllers;

use App\Model\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\DocumentoValidate;
use Carbon\Carbon;

class DocumentoController extends Controller
{
    /**
     * Visualiza todos los Documentos
     */
    public function index(Request $request)
    {
        $documentos=Documento::select('documento.*',
                                            'users.razon_social',
                                            DB::raw("DATE_FORMAT(documento.fecha_emision,'%e/%c/%Y') fecha_emision"),
                                            DB::raw("DATE_FORMAT(documento.created_at,'%e/%c/%Y %H:%i') fecha_registro"),
                                            DB::raw("DATE_FORMAT(documento.fecha_recepcion,'%e/%c/%Y') fecha_recepcion"),
                                            DB::raw("DATE_FORMAT(documento.fecha_pago,'%e/%c/%Y') fecha_pago")
                                )
                                ->join('users','users.ruc','=','documento.ruc')
                                ->where('users.ruc','like',$request->ruc.'%')
                                ->orderBy('documento.created_at','DESC')
                                ->get();
        return response()->json($documentos);
    }

    /**
     * Registra un nuevo User
     */
    public function store(DocumentoValidate $request)
    {   
        $documento=Documento::where('ruc',$request->ruc)
                    ->where('serie',$request->serie)
                    ->where('numero',(int)$request->numero)
                    // ->where('empresa',$request->empresa)
                    ->first();
        if ($documento!=null) {
            return response()->json([
                "status"=> "VALIDATION",
                "data"  => ["numero"=>"Documento ya registrado"]
            ]);
        }

        $documento=new Documento();
        $documento->ruc=strtoupper($request->ruc);
        $documento->serie=strtoupper($request->serie);
        $documento->numero=$request->numero;
        $documento->empresa=$request->empresa;
        $documento->fecha_emision=$request->fecha_emision;
        $documento->monto=$request->monto;
        $documento->moneda=$request->moneda;
        
        if ($request->file!=null) {
            $fileName = $request->ruc.' '.$request->serie.'-'.$request->numero.'.'.$request->file->getClientOriginalExtension();
            $request->file->move(public_path('files'), $fileName);
            $documento->ruta_archivo=$fileName;
        }else{
            return response()->json([
                "status"=> "VALIDATION",
                "data"  => ["file"=>"Subir archivo."]
            ]);
        }

        $documento->save();

        return response()->json([
            "status"=> "OK",
            "data"  => "Documento Registrado"
        ]);
        
    }

    public function show($id)
    {
        $User=User::where('id',$id)->first();
        return response()->json($User);
    }

    public function pendientes(Request $request){
        $documentos = Documento::whereNull('fecha_recepcion')
                            ->orWhereNull('fecha_pago')
                            ->get();
        return response()->json($documentos);
    }

    public function update(Request $request,$id){
        $documento = Documento::where('id',$id)->first();
        $documento->fecha_pago=$request->fecha_pago;
        $documento->fecha_recepcion=$request->fecha_recepcion;
        $documento->save();
    }
}
