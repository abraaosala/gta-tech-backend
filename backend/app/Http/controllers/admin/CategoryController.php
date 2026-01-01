<?php

namespace app\Http\controllers\admin;

use app\Http\Response;
use app\models\Category;
use app\classes\UUID;
use Illuminate\Http\Request;

class CategoryController
{
    public function index(Response $response)
    {
        $categories = Category::all();

        return $response->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function show($id, Response $response)
    {
        $category = Category::find($id);

        if (!$category) {
            return $response->notFound('Categoria');
        }

        return $response->json([
            'success' => true,
            'data' => $category
        ]);
    }

    public function store(Request $request, Response $response)
    {
        $data = json_decode($request->getContent(), true);

        $data['id'] = (string) UUID::v4();
        $data['created_at'] = date('Y-m-d H:i:s');

        $category = Category::create($data);

        return $response->success(
            'Categoria criada com sucesso',
            $category,
            201
        );
    }

    public function update($id, Request $request, Response $response)
    {
        $category = Category::find($id);

        if (!$category) {
            return $response->json([
                'status' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);
        $category->update($data);

        return $response->json([
            'status' => true,
            'message' => 'Categoria atualizada com sucesso',
            'data' => $category
        ]);
    }

    public function destroy($id, Response $response)
    {
        $category = Category::find($id);

        if (!$category) {
            return $response->json([
                'status' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        $category->delete();

        return $response->json([
            'status' => true,
            'message' => 'Categoria removida com sucesso'
        ], 200);
    }
}
