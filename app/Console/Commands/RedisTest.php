<?php

namespace App\Console\Commands;

use App\Traits\ComandosTrait;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Predis\Response\Status;

class RedisTest extends Command
{

    use ComandosTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->inicio();

        /**
         * @var Redis $redis
         */
        $redis = Redis::connection();

        try{
            /**
             * @var Status $respuesta
             */
            $this->line("Haciendo ping con redis...");
            $respuesta = $redis->ping();
            $this->info("CONEXIÓN EXITOSA CON REDIS!!! ");

        } catch (Exception $e){
            dd($e->getMessage());
        }

        $this->fin();
    }
}
