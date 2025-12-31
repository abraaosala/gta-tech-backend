<?php

declare(strict_types=1);

namespace app\classes;

use Illuminate\Container\Container;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Symfony\Component\HttpFoundation\JsonResponse;

class Validator
{
    private Factory $factory;

    public function __construct()
    {
        $container = new Container();

        // Mensagens padrão (pt_BR)
        $loader = new ArrayLoader();
        $loader->addMessages('pt_BR', 'validation', self::messages());

        $translator = new Translator($loader, 'pt_BR');

        $this->factory = new Factory($translator, $container);
    }

    public function validate(
        array $data,
        array $rules,
        array $messages = [],
        array $attributes = []
    ): array {
        $validator = $this->factory->make(
            $data,
            $rules,
            $messages,
            $attributes
        );

        if ($validator->fails()) {
            response()->error(
                'Erros de validação',
                $validator->errors()->toArray()
            ,422);
        }

        return $validator->validated();
    }

    /**
     * Mensagens padrão (igual Laravel pt_BR)
     */
    private static function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'array' => 'O campo :attribute deve ser um array.',
            'email' => 'O campo :attribute deve ser um email válido.',
            'min' => [
                'numeric' => 'O campo :attribute deve ser no mínimo :min.',
                'string' => 'O campo :attribute deve ter no mínimo :min caracteres.',
                'array' => 'O campo :attribute deve ter no mínimo :min itens.'
            ],
            'max' => [
                'string' => 'O campo :attribute deve ter no máximo :max caracteres.'
            ],
            'url' => 'O campo :attribute deve ser uma URL válida.'
        ];
    }
}