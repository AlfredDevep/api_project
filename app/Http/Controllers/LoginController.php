<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        //verifica si se cumplen o no las validaciones
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $email = $request->input('email');
        $password = $request->input('password');

        # validar que email y password existan en la base de datos
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }



        // definir el tiempo de expiracion en minutos
        $tokenExpiration = config('sanctum.token_expiration', 60); // 60 minutes

        // crear el token personal
        $token = $user->createToken('api-token')->plainTextToken;

        // retornar el token con informacion adicional
        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $tokenExpiration * 60, // 60 segundos
        ], 200);
    }
}
