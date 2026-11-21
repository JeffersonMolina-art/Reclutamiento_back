<?php

namespace App\Console\Commands;

use App\Traits\SMSTigoTrait;
use Illuminate\Console\Command;

class PruebasComando extends Command
{
    use SmsTigoTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pruebasComando';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $telefono = $this->ask('Ingrese el Numero de telefono');
        $confirma = $this->confirm('¿Desea enviar un mensaje de prueba a este número '.$telefono.'?');

        if (!$confirma) {
            $this->info('Operación cancelada');
            return 0;
        }

        try {
            $this->sendTelefonoSMS($telefono, 'Hola, este es un mensaje de prueba');
            $this->info('Mensaje enviado correctamente.');
        } catch (\Exception $e) {
            $this->error('Error al enviar el SMS: ' . $e->getMessage());
        }
    }
}
