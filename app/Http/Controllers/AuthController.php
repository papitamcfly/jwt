<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Http;
use App\Mail\AccountActivationMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\dispositivos;
use App\Models\tipo_dispositivo;
use App\Models\cuartos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\AdafruitController;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'lastname' => 'required',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
    if($validator->fails()){
        return response()->json($validator->errors()->toJson(),400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password)]
        ));
        $token = JWTAuth::fromUser($user);

        $url = URL::temporarySignedRoute(
            'activate', now()->addMinutes(30), ['token' => $token]
        );

        Mail::to($user->email)->send(new AccountActivationMail($url));
        return response()->json([
            'message' => 'usuario registrado correctamente. verifica tu correo para activar tu cuenta ', 'user'=>$user
        ],201);
    }

    public function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user'=>auth()->user()
        ]);
    }


   public function activate($token)
   {
       $user = JWTAuth::parseToken()->authenticate();

       if ($user->tipo_usuario  == 2) {
           $user->tipo_usuario = 1;
           $user->save();

           return response()->json([
               'success' => true,
               'message' => 'Account activated successfully.',
           ]);
       }

       return response()->json([
           'success' => false,
           'message' => 'Account is already activated.',
       ]);
   }


   public function refresh(){
    $token = JWTAuth::getToken();


try {
    $newToken = JWTAuth::refresh($token);
} catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
 
    return response()->json(['error' => 'Token no válido'], 401);
}


return response()->json(['new_token' => $newToken]);
   }


    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=> 'required|email',
            'password'=> 'required|string|min:6',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
            }
        if(!$token=auth()->attempt($validator->validated())){
            return response()->json(['error' => 'Unauthorized',401]) ;
        }   
        return $this->createNewToken($token);
        
    }

    public function update(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'lasname'=>'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $task = User::where('id', $user->id)->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found for the authenticated user'], 404);
        }

        $task->name = $request->input('name');
        $task->email = $request->input('email');
        $task->save();

        return response()->json(['task' => $task], 200);
    }
    public function regcuarto(Request $request){
        $Ada = new AdafruitController();
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
        $validator = Validator::make($request->all(),[
            'nombre' => 'required|string'
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()],400);
        }
        $userid = $user->id;
        $cuarto = new cuartos();
        $cuarto->nombre = $request->nombre;
        $cuarto->propietario = $userid;
        $cuarto->ruta = 'https://io.adafruit.com/api/v2/Anahi030702/groups/'.$request->nombre;
        $Ada->creargroup($request->nombre);
        $Ada->crearfeed($user->id);
        $cuarto->save();
        $cuartoid = $cuarto->id;
        $dispositivos = [
            ['nombre' => "DT" . $cuartoid, 'tipo_dispositivo' => 1, 'cuarto' => $cuartoid],
            ['nombre' => "DH" . $cuartoid, 'tipo_dispositivo' => 2, 'cuarto' => $cuartoid],
            ['nombre' => "SO" . $cuartoid, 'tipo_dispositivo' => 3, 'cuarto' => $cuartoid],
            ['nombre' => "VO" . $cuartoid, 'tipo_dispositivo' => 4, 'cuarto' => $cuartoid],
            ['nombre' => "PO" . $cuartoid, 'tipo_dispositivo' => 5, 'cuarto' => $cuartoid],
            ['nombre' => "HU" . $cuartoid, 'tipo_dispositivo' => 6, 'cuarto' => $cuartoid],
            ['nombre' => "NF" . $cuartoid, 'tipo_dispositivo' => 7, 'cuarto' => $cuartoid],
        ];
    
        DB::table('dispositivos')->insert($dispositivos);
        return response()->json(['msg'=>'el cuarto ha sido creado exitosamente con el identificador']);
    }  
    public function profile(){
        return response()->json(auth()->user());
    }

    public function logout(){
        auth()->logout();
        return response()->json([
            'message' => 'User logged out'
        ]);
    }
    public function obtenerDatos()
    {
        try{
        $user = JWTAuth::parseToken()->authenticate();
        $cuartos = $user->cuartos;
        return response()->json($cuartos,200);
        }
        catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los cuartos'], 500);
        }
    }
    public function cuartoesp($idcuarto)
    {
        try{
        JWTAuth::parseToken()->authenticate();
        $cuarto = cuartos::find($idcuarto);
        $dispositivos = $cuarto->dispositivos;
        return response()->json($cuarto,200);
        }
        catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener la informacion del cuarto'], 500);
        }
    }
    public function borrarcuarto($idcuarto)
    {
        try
        {
            JWTAuth::parseToken()->authenticate();
            $cuarto = cuartos::find($idcuarto);
            if( $cuarto){
              
                $cuarto->delete();
              return response()->json([
                "msg" => "cuarto eliminado",
                "data" =>  $cuarto
              ],200);
            }
            return response()->json(["error" =>"cuarto no encontrado",404]);
        }
        catch(\Exception $e){
            return response()->json(['error'=>'Error al obtener la informacion del cuarto'],500);
        }
    }
    public function editarcuarto(Request $request ,$idcuarto)
    {
        
        if (!JWTAuth::parseToken()->authenticate()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
        $validate = Validator::make($request->all(),
        [
            'nombre' => 'required|string'
        ]);
        if ($validate->fails())
        {
          return response()->json(['errors'=>$validate->errors(),
            "msg"=>"Errores de validacion"
           
          ]);
        }
        $cuarto = cuartos::find($idcuarto);
        if($cuarto){
            $cuarto->nombre = $request->nombre;
            $cuarto->save();
            return response()->json(['cuarto editado correctamente', $cuarto],200);
        }
        return response()->json('cuarto no encontrado',404);
    }
}
