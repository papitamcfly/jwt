<?php

namespace App\Http\Controllers;

use App\Mail\AccountActivationMail;
use App\Models\Todo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = JWTAuth::parseToken()->authenticate();

        $task = User::where('id', $user->id)->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found for the authenticated user'], 404);
        }

        $task->name = $request->input('name');
        $task->email = $request->input('email');
        $task->save();

        return response()->json(['task' => $task], 200);
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
}
