<?php

namespace app\Http\controllers\admin;

use app\classes\Validator;
use app\classes\UUID;
use app\Http\Response;
use app\models\Product;
use Illuminate\Http\Request;


class ProductController
{
    public function index(Request $request, Response $response)
    {

        $query = $_GET;
        $page = max(1, (int) ($query['page'] ?? 1));
        $perPage = min(50, (int) ($query['per_page'] ?? 10));

        $offset = ($page - 1) * $perPage;

        $total = Product::count();

        $products = Product::with('category')
            ->limit($perPage)
            ->offset($offset)
            ->get();

        return $response->json([
            'success' => true,
            'data' => $products,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage)
            ]
        ]);
    }




    public function show($id, Response $response)
    {

        $product = Product::find($id);

        if (!$product) {
            return $response->notFound('Produto');
        }

        $response->json([
            'success' => true,
            'data' => $product
        ]);

        // Mostrar um item
    }

    public function store(Request $request, Validator $validator, Response $response)
    {
        // Pega os dados do body (JSON)
        $data = json_decode($request->getContent(), true);

        // Regras de validação
        $rules = [
            'name'            => 'required|min:2',
            'price_in_cents'  => 'required|numeric',
            'stock'           => 'required|integer',
            'category_id'     => 'required',
        ];

        $validator->validate($data, $rules);

        // Gera ID único
        $data['id'] = (string) UUID::v4();

        // Conversão explícita para evitar erros de tipagem no PostgreSQL
        $data['price_in_cents'] = (int) $data['price_in_cents'];
        $data['stock'] = (int) $data['stock'];

        // Define created_at no servidor
        $data['created_at'] = date('Y-m-d H:i:s');

        // Salva no banco
        $productStore = Product::create($data);

        // Resposta de sucesso
        return $response->success(
            'Produto criado com sucesso',
            $productStore,
            201
        );
    }

    public function update($id, Request $request, Validator $validator, Response $response)
    {

        // Busca o produto
        $product = Product::find($id);
        if (!$product) {
            return $response->json([
                'status' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        // Dados enviados
        $data = json_decode($request->getContent(), true);

        // Regras (tudo opcional no update)
        $rules = [
            'name'            => 'min:2',
            'price_in_cents'  => 'numeric',
            'stock'           => 'integer',
        ];

        // Validação
        $validator->validate($data, $rules);

        // Conversão explícita para evitar erros de tipagem no PostgreSQL
        if (isset($data['price_in_cents'])) {
            $data['price_in_cents'] = (int) $data['price_in_cents'];
        }
        if (isset($data['stock'])) {
            $data['stock'] = (int) $data['stock'];
        }

        // Atualiza data no servidor
        // $data['updated_at'] = date('Y-m-d H:i:s');

        // Atualiza no banco
        $product->update($data);

        return $response->json([
            'status' => true,
            'message' => 'Produto atualizado com sucesso',
            'data' => $product
        ]);
    }

    public function destroy($id, Response $response)
    {
        // Busca o produto
        $product = Product::find($id);

        if (!$product) {
            return $response->json([
                'status' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        // Remove
        $product->delete();

        return $response->json([
            'status' => true,
            'message' => 'Produto removido com sucesso'
        ], 200);
    }
}
