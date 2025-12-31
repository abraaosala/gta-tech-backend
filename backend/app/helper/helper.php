<?php

use app\Http\Response;

if (!function_exists('response')) {
    /**
     * Função helper para facilitar o uso da classe Response
     *
     * @return Response
     */
    function response()
    {
        return new Response();
    }
}


function id_regeneretor()
{
    return rand(1000, 9999);    
}