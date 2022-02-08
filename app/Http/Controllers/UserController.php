<?php

namespace App\Http\Controllers;

use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\UserValidate;
// use App\Http\Requests\UserEditar;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Visualiza todos los Useres
     */
    public function index(Request $request)
    {
        if ($request->search==null||$request->search=="null") {
            $request->search="";
        }
        if ($request->has('all')) {
            $Users=User::select('ruc','razon_social')->get();
        }else {
            $Users=User::where('','like','%'.$request->search.'%')->paginate(8);
        }
        return response()->json($Users);
    }

    /**
     * Registra un nuevo User
     */
    public function store(UserValidate $request)
    {
        $User=new User();
        $User->ruc=strtoupper($request->ruc);
        $User->razon_social=strtoupper($request->razon_social);
        $User->email=$request->email;
        $User->password=$request->password;
        // dd($User);
        $User->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "User Registrado"
        ]);
        
    }
        
        /**
         * Visualiza datos de un solo User
         */
    public function show($id)
    {
        $User=User::where('id',$id)->first();
        return response()->json($User);
    }
        
    // public function update(Request $request, $id)
    // {
    //     $User=User::where('id',$id)->first();

    //     $User->save();
    //     return response()->json([
    //         "status"=> "OK",
    //         "data"  => "User Actualizado"
    //     ]);
    // }

    /**
     * Desabilita al User
     */
    // public function estado($id)
    // {
    //     $User=User::where('id',$id)->first();
    //     $User->estado=($User->estado=='0') ? '1' : '0';
    //     $User->save();
    //     return response()->json([
    //         "status"=> "OK",
    //         "data"  => "Estado Actualizado"
    //     ]);
    // }

    public function rutas(Request $request){
        $ruc=$request->ruc;
        $User=User::where('ruc',$ruc)->first();
        $listaRutas=[];
        array_push($listaRutas,"/");
        
        switch ($User->tipo) {
            case 'P':
                array_push($listaRutas,"/documentos");
                break;
            case 'A':
                array_push($listaRutas,"/admin/documentos");
                break;

            default:
                break;
        }
        return response()->json($listaRutas);
    }

    public function login(Request $request){
        $usuario=$request->usuario;
        $password=$request->password;

        $User=User::where('email',$usuario)
                    ->where('password',$password)
                    ->select('ruc','razon_social','email')
                    ->first();
        if ($User==null) {
            return response()->json([
                "status"=> "ERROR",
                "data"  => "Usuario o Contraseña incorrecta."
            ]);
        }else{
            return response()->json([
                "status"=> "OK",
                "data"  => $User
            ]);
        }
    }
}
