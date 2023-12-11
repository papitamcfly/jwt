<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;


class AdafruitController extends Controller
{
    public function getdatos()
    {
        $feed="humedad";
        $responses = []; // Aquí se guardarán todas las respuestas
        for ($i = 1; $i < 8; $i++) {
            switch($i)
            {
                case 1:
                    $feed="humedad";
                    break;
                case 2:
                    $feed="humo";
                    break;
                case 3:
                    $feed="polvo";
                    break;
                case 4:
                    $feed="sonido";
                    break;
                case 5:
                    $feed="temperatura";
                    break;
                case 6:
                    $feed="voltaje";
                    break;
                case 7:
                    $feed="alarma";
                    break;
            }
            $response = Http::withHeaders([
                'X-AIO-Key' => 'aio_eyYN44L64sgDk79gkq61PqOFSe65',
            ])->get("https://io.adafruit.com/api/v2/Anahi030702/feeds/".$feed."/data");
    
            if ($response->ok())
            {
                $data = $response->json();
                $value = $data[0]['value'];
                $feed_key = $data[0]['feed_key'];
                
                $responses[] = [
                    "feed_key" => $feed_key,
                    "value" => $value
                ];
            }
            else{
            
                $responses[] = [
                    "msg" => "No quema kuh :C",
                    "data" => $response->body()
                ];
            }
        }
    
        // Devuelves todas las respuestas en un solo JSON
        return response()->json($responses, 200);
    }
    public function ApagarAlarma()
    {
        $response = Http::withHeaders([
            'X-AIO-Key' => 'aio_eyYN44L64sgDk79gkq61PqOFSe65',
            'Content-Type' => 'application/json', //si no jala es por esta coma
        ])->post("https://io.adafruit.com/api/v2/Anahi030702/feeds/alarma/data",[
            'value' => 0
        ]);

        if ($response->ok())
        {
            return response()->json([
                "msg"=>"Alarma apagada"
            ],200);
            
        }
        else{
        
            response()->json([
                "msg" => "No se ha podido apagar la alarma...",
                "data" => $response->body()
            ],400);
        }
    }
    public function LuzLed()
    {
        $response = Http::withHeaders([
            'X-AIO-Key' => 'aio_eyYN44L64sgDk79gkq61PqOFSe65',
            'Content-Type' => 'application/json', //si no jala es por esta coma
        ])->post("https://io.adafruit.com/api/v2/Anahi030702/feeds/leds/data",[
            'value' => 0,
        ]);

        if ($response->ok())
        {
            return response()->json([
                "msg"=>"Leds apagados"
            ],200);
            
        }
        else{
        
            response()->json([
                "msg" => "No se ha podido apagar los leds...",
                "data" => $response->body()
            ], 400);
        }
    }
    public function Abrirpuerta()
    {
        $response = Http::withHeaders([
            'X-AIO-Key' => 'aio_eyYN44L64sgDk79gkq61PqOFSe65'
            ,
            'Content-Type' => 'application/json', //si no jala es por esta coma
        ])->post("https://io.adafruit.com/api/v2/Anahi030702/feeds/acceso/data",[
            'value' => 1,
        ]);

        if ($response->ok())
        {
            return response()->json([
                "msg"=>"Puerta abierta"
            ], 200);
            
        }
        else{
        
            response()->json([
                "msg" => "No se ha podido abrir la puerta...",
                "data" => $response->body()
            ], 400);
        }
    }

}
