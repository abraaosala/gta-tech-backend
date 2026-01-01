<?php

namespace app\Http\controllers\admin;

use app\classes\Validator;
use app\classes\UUID;
use app\Http\Response;
use app\models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController
{
    public function index(Request $request, Response $response)
    {

        $query = $_GET;
        $page = max(1, (int) ($query['page'] ?? 1));
        $perPage = min(50, (int) ($query['per_page'] ?? 10));

        $offset = ($page - 1) * $perPage;

        $total = User::count();

        $users = User::limit($perPage)
            ->offset($offset)
            ->get(['id', 'name', 'email', 'role', 'created_at']);
        if ($total === 0) {
            $response->notFound('Produto');
        }

        return $response->json([
            'success' => true,
            'data' => $users,
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

        $user = User::find($id, ['id', 'name', 'email', 'role', 'created_at']);

        if (!$user) {
            return $response->notFound('Produto');
        }

        $response->json([
            'success' => true,
            'data' => $user
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
            'email'           => 'required|email',
            'password'        => 'required|min:3',
            'role'           => 'required',
        ];

        $validator->validate($data, $rules);

        // Gera ID único
        $data['id'] = UUID::v4();
        // $data['id'] = id_regeneretor();


        $data['password_hash'] = Hash::make($data['password']);
        unset($data['password']);

        // Define created_at no servidor
        $data['created_at'] = date('Y-m-d H:i:s');

        // Salva no banco
        $userstore = User::create($data);

        // Resposta de sucesso
        return $response->success(
            'Usuario criado com sucesso',
            $userstore,
            201
        );
    }

    public function update($id, Request $request, Validator $validator, Response $response)
    {
        // Busca o produto
        $user = User::find($id);

        if (!$user) {
            return $response->json([
                'status' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        // Dados enviados
        $data = json_decode($request->getContent(), true);

        // Regras (tudo opcional no update)
        $rules = [
            'name'     => 'min:2',
            'email'    => 'email',
            'password' => 'min:3',
        ];

        // Validação
        $validator->validate($data, $rules);

        // Atualiza data no servidor
        // $data['updated_at'] = date('Y-m-d H:i:s');

        // Atualiza no banco
        $user->update($data);

        return $response->json([
            'status' => true,
            'message' => 'Produto atualizado com sucesso',
            'data' => $user
        ]);
    }

    public function destroy($id, Response $response)
    {
        // Busca o produto
        $user = User::find($id);

        if (!$user) {
            return $response->json([
                'status' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        // Remove
        $user->delete();

        return $response->json([
            'status' => true,
            'message' => 'Produto removido com sucesso'
        ], 200);
    }
}
