<?php

namespace app\Http\controllers\admin;

use app\classes\Validator;
use app\classes\UUID;
use app\Http\Response;
use app\models\Customer;
use Illuminate\Http\Request;

class CustomerController
{
    public function index(Request $request, Response $response)
    {
        $query = $request->input('q');

        $customers = Customer::query();

        if ($query) {
            $customers->where('name', 'like', "%{$query}%")
                ->orWhere('nif', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%");
        }

        return $response->json($customers->limit(20)->get());
    }

    public function store(Request $request, Validator $validator, Response $response)
    {
        $data = json_decode($request->getContent(), true);

        $rules = [
            'name' => 'required|min:2',
            'nif'  => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ];

        $validator->validate($data, $rules);

        $customer = new Customer();
        $customer->id = (string) UUID::v4();
        $customer->name = $data['name'];
        $customer->nif = $data['nif'] ?? null;
        $customer->email = $data['email'] ?? null;
        $customer->phone = $data['phone'] ?? null;
        $customer->address = $data['address'] ?? null;
        $customer->save();

        return $response->json($customer, 201);
    }
}
