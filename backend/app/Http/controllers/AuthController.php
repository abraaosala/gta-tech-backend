<?php

namespace app\Http\controllers;

use app\classes\Validator;
use App\Http\Controller;
use app\Http\Response;
use app\models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Request;

class AuthController
{
   public function login(Request $request, Validator $validator, Response $response)
{
    $request = $request->createFromGlobals();
    $data = json_decode($request->getContent(), true);

    $data = $validator->validate($data, [
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $data['email'])->first();
    if (!$user || !Hash::check($data['password'], $user->password_hash)) {
        return $response->error("Credenciais inválidas", [], 401);
    }

    unset($user->password_hash);

    // 🔑 ACCESS TOKEN (10 min)
    $accessPayload = [
        'iss' => 'http://localhost:8000',
        'aud' => 'http://localhost:5500',
        'type'=> 'access',
        'iat' => time(),
        'exp' => time() + 60 * 10,
        'data' => [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ]
    ];

    // 🔄 REFRESH TOKEN (7 dias)
    $refreshPayload = [
        'iss' => 'http://localhost:8000',
        'aud' => 'http://localhost:5500',
        'type'=> 'refresh',
        'iat' => time(),
        'exp' => time() + 60 * 60 * 24 * 7,
        'sub' => $user->id
    ];

    try {
        return $response->json([
            'access_token'  => JWT::encode($accessPayload, env('KEY'), env('ALG', 'HS256')),
            'refresh_token' => JWT::encode($refreshPayload, env('KEY'), env('ALG', 'HS256'))
        ]);
    } catch (\Throwable $e) {
        return $response->error("Erro ao gerar token", [], 500);
    }
}


   public function logout(Response $response)
   {
      return $response->json(['message' => 'Logout realizado com sucesso']);
   }


public function refresh(Request $request, Response $response)
{
    // $authHeader = $request->headers->get('Authorization');
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];

    
    if (!$authHeader || !preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
        return $response->error("Refresh token ausente", [], 401);
    }

    $refreshToken = $matches[1];

    try {

        $decoded = JWT::decode(
            $refreshToken,
            new Key(env('KEY'), env('ALG', 'HS256'))
        );

        // 🔐 garante que é refresh token
        if (($decoded->type ?? '') !== 'refresh') {
            return $response->error("Token inválido", [], 401);
        }

        $user= User::find($decoded->sub);

        unset($user->password_hash);
        
        // 🔄 novo access token (curto)
        $payload = [
            'iss' => 'http://localhost:8000',
            'aud' => 'http://localhost:5500',
            'type' => 'access',
            'iat' => time(),
            'exp' => time() + 60 * 10,
            'data' => $user
        ];

        $newAccessToken = JWT::encode(
            $payload,
            env('KEY'),
            env('ALG', 'HS256')
        );

        return $response->json([
            'access_token' => $newAccessToken,
            'expires_in' => 600,
        ]);

    } catch (\Throwable $e) {
        return $response->error("Refresh token inválido ou expirado", [], 401);
    }
}


   public function store() {}
}