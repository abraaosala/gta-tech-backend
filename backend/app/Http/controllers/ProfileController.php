<?php

namespace app\Http\controllers;

use app\Http\Response;
use app\Support\Auth;
use Illuminate\Http\Request;

class ProfileController 
{
     public function index(Request $request)
    {
        $user = Auth::user(); // aqui o usuário já está

        return response()->json(
             $user, 200
        );
    }

    public function show($id)
    {
        // Mostrar um item
    }
    
    public function store()
    {
        // Salvar item
    }
}