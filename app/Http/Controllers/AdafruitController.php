<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\cuartos;

class AdafruitController extends Controller
{
    const key = 'aio_thPz49ENA7DJ5eWu1fEFRnYCtShX';
    public function getdatos()
    {
        $feed="humedad";
        $responses = []; // Aquí se guardarán todas las respuestas
        for ($i = 1; $i < 10; $i++) {
            switch($i)
            {
                case 1:
                    $feed="humedad";
                    $tipo ="normal";
                    break;
                case 2:
                    $feed="humo";
                    $tipo ="normal";
                    break;
                case 3:
                    $feed="polvo";
                    $tipo ="normal";
                    break;
                case 4:
                    $feed="sonido";
                    $tipo ="normal";
                    break;
                case 5:
                    $feed="temperatura";
                    $tipo ="normal";
                    break;
                case 6:
                    $feed="voltaje";
                    $tipo ="normal";
                    break;
                case 7:
                    $feed="alarma";
                    $tipo ="actuador";
                case 8:
                    $feed="leds";
                    $tipo ="actuador";
                    break;
                case 9:
                    $feed="acceso";
                    $tipo ="actuador";
                    break;
            }
            $response = Http::withHeaders([
                'X-AIO-Key' => self::key,
            ])->get("https://io.adafruit.com/api/v2/Anahi030702/feeds/".$feed."/data");
    
            if ($response->ok())
            {
                $data = $response->json();
                $value = $data[0]['value'];
                $feed_key = $data[0]['feed_key'];
                
                $responses[] = [
                    "feed_key" => $feed_key,
                    "value" => $value,
                    "tipo" => $tipo
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
            'X-AIO-Key' => self::key,
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
        
           return response()->json([
                "msg" => "No se ha podido apagar la alarma...",
                "data" => $response->body()
            ],400);
        }
    }
    public function LuzLed()
    {
        $response = Http::withHeaders([
            'X-AIO-Key' => self::key,
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
        
           return response()->json([
                "msg" => "No se ha podido apagar los leds...",
                "data" => $response->body()
            ], 400);
        }
    }
    public function Abrirpuerta()
    {
        $response = Http::withHeaders([
            'X-AIO-Key' => self::key
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
        
           return response()->json([
                "msg" => "No se ha podido abrir la puerta...",
                "data" => $response->body()
            ], 400);
        }
    }
    public function creargroup(String $name)
    {
       $response = Http::withHeaders([
            'X-AIO-Key' => self::key,
            'Content-Type' => 'application/json', //si no jala es por esta coma
        ])->post("https://io.adafruit.com/api/v2/Anahi030702/groups",[
            'name' => $name
        ]);
        if ($response->ok())
        {
            return response()->json([
                "msg"=>"Grupo creado"
            ], 200);
            
        }
        else{
        
            return response()->json([
                "msg" => "No se ha podido crear el grupo...",
                "data" => $response->body()
            ], 400);
        }
    }
    public function crearfeed($id)
    {
        $cuarto = cuartos::find($id);
        $response = Http::withHeaders([
            'X-AIO-Key' => self::key,
            'Content-Type' => 'application/json', //si no jala es por esta coma
        ])->post("$cuarto->ruta",[
            'name' => 'ejemplo'
        ]);
        if ($response->ok())
        {
            return response()->json([
                "msg"=>"feed creado"
            ], 200);
            
        }
        else{
        
            return  response()->json([
                "msg" => "No se ha podido crear el feed...",
                "data" => $response->body()
            ], 400);
        }
    }
}
