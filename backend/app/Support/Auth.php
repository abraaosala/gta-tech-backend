<?php

declare(strict_types=1);

namespace app\Support;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class Auth
{
    protected static ?object $user = null;

    /**
     * Retorna o usuário autenticado (ou null)
     */
    public static function user(): ?object
    {
        if (self::$user !== null) {
            return self::$user;
        }

        $token = self::getBearerToken();

        if (!$token) {
            return null;
        }

        try {
            $key = env('JWT_SECRET') ?: env('KEY');
            $decoded = JWT::decode(
                $token,
                new Key($key, env('ALG', 'HS256'))
            );

            return self::$user = $decoded->data ?? null;
        } catch (\Throwable $e) {
            return response()->error($e->getMessage(), [], 401);
        }
    }

    /**
     * Força autenticação (lança erro se não autenticado)
     */
    public static function check(): bool
    {
        if (!self::user()) {
            response()->error('Não autenticado', [], 401);
        }

        return true;
    }

    /**
     * Extrai o Bearer token do header
     */
    protected static function getBearerToken(): ?string
    {
        $request = Request::capture();

        $header = $request->headers->get('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return null;
        }

        return substr($header, 7);
    }
}
