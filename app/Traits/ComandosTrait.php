<?php


namespace App\Traits;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

trait ComandosTrait
{

    private $barraProceso;

    public function inicio($titulo = "Inicio script...")
    {
//        $this->info($titulo);
        $this->output->title($titulo);

        DB::enableQueryLog();

        $this->tiempoIni = microtime(true);

    }

    public function fin($mostrarQuerys=false)
    {

        $tiempoFin = microtime(true);
        $duration = $tiempoFin - $this->tiempoIni;
        $hours = (int)($duration/60/60);
        $minutes = (int)($duration/60)-$hours*60;
        $seconds = (int)$duration-$hours*60*60-$minutes*60;

        $this->info("\n### Fin ####");
        $this->info("Tiempo empleado: ". $hours . ' hrs ' . $minutes.' min '. $seconds." Segundos");
        $this->info("Cantidad querys: ".count(DB::getQueryLog()));

        $querys = collect(DB::getQueryLog())->pluck('query');

        if ($mostrarQuerys){
            dump($querys);
        }


    }

    public function guardarLog($contenido='contenido')
    {

        $nombreClase = str_replace('App\\Console\\Commands\\','',get_class($this));

        if (!file_exists(storage_path('logs/comandos/'))){
            mkdir(storage_path('logs/comandos/'));
        }

        $file =storage_path('logs/comandos/'.'Log'.$nombreClase.'.txt');


        File::put($file,$contenido);

    }


    public function barraProcesoIniciar($cantidadRegistros)
    {

        $this->barraProceso = $this->output->createProgressBar($cantidadRegistros);
        $this->barraProceso->start();
    }

    public function barraProcesoAvanzar()
    {
        $this->barraProceso->advance();
    }

    public function barraProcesoFin()
    {
        $this->barraProceso->finish();
    }

}



