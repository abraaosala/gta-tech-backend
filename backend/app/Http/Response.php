<?php

declare(strict_types=1);

namespace app\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

class Response
{
    /**
     * Envia uma resposta JSON e encerra a execução.
     */
    public function json(null|array|object|string $payload, int $status = 200): void
    {
        $response = new JsonResponse($payload, $status);
        $response->send();
        exit;
    }

    /**
     * Resposta de sucesso padronizada.
     */
    public function success(string $message, $data = null, int $status = 200): void
    {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $payload['data'] = $data;
        }

        $this->json($payload, $status);
    }

    /**
     * Resposta de erro padronizada.
     */
    public function error(string $message, array $errors = [], int $status = 400): void
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }

        $this->json($payload, $status);
    }

    /**
     * Resposta 404 padronizada.
     */
    public function notFound(string $resource = 'Recurso'): void
    {
        $this->error("{$resource} não encontrado.", [], 404);
    }

    /**
     * Resposta 204 No Content.
     */
    public function noContent(): void
    {
        http_response_code(204);
        exit;
    }
}