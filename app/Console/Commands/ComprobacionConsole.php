<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Documento;

class ComprobacionConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comprobar:documento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprobando estado de recepcion y pago de documentos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $Documentos = Documento::whereNull('fecha_recepcion')
                            ->orWhereNull('fecha_pago')
                            ->get();

        foreach ($Documentos as $key => $documento) {
            $data=null;
            try {
                $data = json_decode(file_get_contents('http://190.116.184.195:9083/api/SeguimientoDocumentario/status?empresa='.$documento->empresa.'&ruc='.$documento->ruc.'&serie='.$documento->serie.'&numero='.$documento->numero.''), true);
                // dd($data);
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
            if ($data!=null) {
                $documento->fecha_recepcion=$data["fecha_recepcion"];
                $documento->fecha_pago=$data["fecha_pago"];
                $documento->save();
                echo '<br>Datos Actualizados';
                // dd($documento,$data);
            }
        }
        return 0;
    }
}
