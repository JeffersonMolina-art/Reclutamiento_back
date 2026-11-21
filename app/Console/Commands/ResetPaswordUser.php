<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Traits\ComandosTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetPaswordUser extends Command
{

    use ComandosTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinicia:contraseña {--todos} {--correo=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $correo = $this->options()['correo'] ?? null;
        $todos = $this->options()['todos'] ?? null;


        $this->inicio();

        if ($todos){
            /**
             * @var User $user
             */
            foreach (User::all() as $index => $user) {

                $contrasena = "Gy".Carbon::now()->year."00";

                $user->password = $contrasena;
                $user->nuevoPassword = 0;
                $user->password_changed_at = Carbon::now();
                $user->save();

            }

            $this->info("Contraseña reiniciada para todos los usuarios!");
            $this->info("Contraseña: ".$contrasena);

        }else{

            if(!$correo){
                $this->warn('No ingreso el parámetro --email');
                $this->warn('La forma correcta es: ');
                $this->warn('php artisan reinicia:contraseña --email=ejemplo@dominio.com');
            }


            /**
             * @var User $user
             */
            $user = User::where('email',$correo)->first();

            if (!$user){
                $this->warn('No se encontró al usuario con correo '.$correo);
            }

            $user->password = bcrypt($correo);
            $user->nuevoPassword = 0;
            $user->password_changed_at = Carbon::now();
            $user->save();

            $this->info("Contraseña reiniciada!");
            $this->info("Usuarios: ".$correo);
            $this->info("Contraseña: ".$correo);
        }




        $this->fin();
    }
}
