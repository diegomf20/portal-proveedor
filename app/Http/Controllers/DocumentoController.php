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
        // if ($request->search==null||$request->search=="null") {
        //     $request->search="";
        // }
        // if ( $request->ruc!=null) {
        //     $documentos=Documento::select('*',DB::raw("DATE_FORMAT(created_at,'%e/%c/%Y %H:%i') fecha_registro"))
        //                             ->where('ruc',$request->ruc)
        //                             ->orderBy('created_at','DESC')
        //                             ->get();
        // }else{
        $documentos=Documento::select('documento.*','users.razon_social',DB::raw("DATE_FORMAT(documento.created_at,'%e/%c/%Y %H:%i') fecha_registro"))
                            ->join('users','users.ruc','=','documento.ruc')
                            ->where('users.ruc','like',$request->ruc.'%')
                            ->orderBy('documento.created_at','DESC')
                            ->get();
        // }
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
}
