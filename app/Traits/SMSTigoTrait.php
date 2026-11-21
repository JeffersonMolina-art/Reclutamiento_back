<?php

namespace App\Traits;

use App\Models\Configuracion;
use App\Models\NotificacionBitacora;
use App\Models\SIGUA\AsociadoNotificacionBitacora;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

trait SMSTigoTrait
{
    public function getTokenValid()
    {
        try {
            $user = config('SMSTigo.SMSTigo_user');
            $pass = config('SMSTigo.SMSTigo_pass');

            $minutosRememberToken = 20;

            $segundos = $minutosRememberToken * 60;

            return Cache::tags(['SMSTigo'])->remember('access_token', $segundos, function() use ($user, $pass) {
                $client = new Client();
                $response = $client->post('https://prod.api.tigo.com/oauth/client_credential/accesstoken?grant_type=client_credentials', [
                    'auth' => [
                        $user,
                        $pass
                    ]
                ]);
                $dataJson = json_decode($response->getBody()->getContents(), true);
                return $dataJson['access_token'];
            });

        } catch (\Exception $e){
            Session::flash('swal-error', "Ha ocurrido un error con los servicios de Tigo SMS (Token Valid)");
        }
    }

    public function getContactos()
    {
        try {
            $apiKey = config('SMSTigo.SMSTigo_APIkey');
            $apiSecret = config('SMSTigo.SMSTigo_APIsecret');
            $idOrganizacion = config('SMSTigo.SMSTigo_IDorganizacion');
            $token = $this->getTokenValid();
            $url = 'https://prod.api.tigo.com/v1/tigo/b2b/gt/comcorp/contacts/organizations/'.$idOrganizacion;
            //dd($url);


            $client = new Client();
            $response = $client->get($url, [
                'headers' => [
                    'APIKey' => $apiKey,
                    'APISecret' => $apiSecret,
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);

        } catch (\Exception $e){
            Session::flash('swal-error', "Ha ocurrido un error con los servicios de Tigo SMS (Get Contacts)");
        }
    }

    public function getListSMS()
    {
        try {
            // PENDIENTE
            $apiKey = config('SMSTigo.SMSTigo_APIkey');
            $apiSecret = config('SMSTigo.SMSTigo_APIsecret');
            $idOrganizacion = config('SMSTigo.SMSTigo_IDorganizacion');
            $token = $this->getTokenValid();
            $url = 'https://prod.api.tigo.com/v1/tigo/b2b/gt/messages/broadcasts/organizations/'.$idOrganizacion;

            $client = new Client();
            $response = $client->get($url, [
                'headers' => [
                    'APIKey' => $apiKey,
                    'APISecret' => $apiSecret,
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);

        } catch (\Exception $e){
            Session::flash('swal-error', "Ha ocurrido un error con los servicios de Tigo SMS (Get Contacts)");
        }
    }

    public function sendTelefonoSMS($numero, $mensaje)
    {
        $this->envio($numero, $mensaje);
    }

    public function sendAsociadoSMS($asociado, $mensaje)
    {
        $this->envio($asociado, $mensaje);
    }

    public function envio($numero, $mensaje)
    {
        try {
            $apiKey = config('SMSTigo.SMSTigo_APIkey');
            $apiSecret = config('SMSTigo.SMSTigo_APIsecret');
            $idOrganizacion = config('SMSTigo.SMSTigo_IDorganizacion');
            $token = $this->getTokenValid();
            $url = 'https://prod.api.tigo.com/v1/tigo/b2b/gt/comcorp/messages/organizations/'.$idOrganizacion;

            $enviarNotificacion = Cache::tags(['Configuracion'])->remember('enviarNotificacion', 21600, function() {
                return Configuracion::where([['nombre', 'enviarNotificacion'],['grupo', 'Notificacion']])->pluck('valor')->first();
            });


            if(config('SMSTigo.SMSTigo_estado') && $enviarNotificacion){

                $client = new Client();
                $response = $client->post($url, [
                    'headers' => [
                        'APIKey' => $apiKey,
                        'APISecret' => $apiSecret,
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'json' => [
                        "protocol" => "sms",
                        "msisdn" => "502".$numero,
                        "priority" => 0,
                        "body" => $mensaje
                    ]
                ]);
                AsociadoNotificacionBitacora::create([
                    'asociado_id' => 1,
                    'asociado_cif' => '123456',
                    'destino' => $numero,
                    'tipo' => 'SMS',
                    'mensaje' => $mensaje,
                    'user_id' => 2
                ]);
                Session::flash('success', 'SMS Enviado con exito!');

            }

        } catch (\Exception $e){
           throw new \Exception($e);
        }
    }
}
