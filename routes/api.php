<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
use App\Http\Controllers\UserController;
Route::resource('user',UserController::class);
Route::post('login',[UserController::class,'login']);
Route::get('rutas',[UserController::class,'rutas']);


use App\Http\Controllers\DocumentoController;
Route::resource('documento',DocumentoController::class);

use App\Model\Documento;
Route::get('prueba', function () {
    $Documentos = Documento::whereNull('fecha_recepcion')
                            ->orWhereNull('fecha_pago')
                            ->get();

    foreach ($Documentos as $key => $documento) {
        $data=null;
        try {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://190.116.184.195:9083/api/SeguimientoDocumentario/status?empresa='.$documento->empresa.'&ruc='.$documento->ruc.'&serie='.$documento->serie.'&numero='.$documento->numero.''); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_HEADER, 0); 
            $data = curl_exec($ch); 
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpcode==200) {
                if ($data!=null) {
                    $documento->fecha_recepcion=$data["fecha_recepcion"];
                    $documento->fecha_pago=$data["fecha_pago"];
                    $documento->save();
                    echo '<br>Datos Actualizados';
                    // dd($documento,$data);
                }
            } 
        } catch (\Exception $ex) {
            echo 'holas: '.$ex->getMessage();
        }
    }
});

