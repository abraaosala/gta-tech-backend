<?php

namespace app\Http\controllers\admin;

use app\models\Sale;
use app\models\SaleItem;
use app\models\Product;
use app\classes\UUID;
use app\classes\Validator;
use app\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController
{
    public function index()
    {
        $sales = Sale::with(['items', 'seller'])->orderBy('created_at', 'desc')->get();
        // Transform user to match frontend expectation (sellerName) if needed, 
        // or frontend can adapt. Frontend uses sale.sellerName. 
        // Let's modify the response or ensure model serialization handles it.
        // Actually, frontend uses sale.sellerName. 
        // Let's map it.

        $data = $sales->map(function ($sale) {
            return [
                'id' => $sale->id,
                'sellerId' => $sale->seller_id,
                'sellerName' => $sale->seller ? $sale->seller->name : 'Unknown', // Flatten
                'total' => $sale->total_in_cents / 100, // Convert to float if frontend expects float, DB is cents
                'paymentMethod' => $sale->payment_method,
                'date' => $sale->created_at, // Timestamp string
                'items' => $sale->items->map(function ($item) {
                    return [
                        'id' => $item->product_id, // Item ID or Product ID? Frontend type SaleItem has id, quantity...
                        // Frontend SaleItem: { id: string; name: string; price: number; quantity: number; }
                        // Here Item has product_id, product_name...
                        'product_id' => $item->product_id,
                        'name' => $item->product_name,
                        'price' => $item->price_in_cents / 100,
                        'quantity' => $item->quantity,
                        'category' => $item->product && $item->product->category ? $item->product->category->name : 'Sem Categoria'
                    ];
                })
            ];
        });

        return response()->json($data);
    }

    public function store(Request $request, Response $response, Validator $validator)
    {
        $data = json_decode($request->getContent(), true);

        // Validation should be added here
        $rules = [
            'sellerId' => 'required',
            'total' => 'required',
            'paymentMethod' => 'required|string',
            'items' => 'required|array'
        ];
        $validator->validate($data, $rules);


        try {
            DB::beginTransaction();

            $saleId = (string) UUID::v4();
            $sale = new Sale();
            $sale->id = $saleId;
            $sale->seller_id = $data['sellerId'];
            $sale->total_in_cents = (int)round($data['total'] * 100);
            $sale->payment_method = $data['paymentMethod'];
            $sale->status = 'COMPLETED';
            $sale->created_at = date('Y-m-d H:i:s');
            $sale->save();

            foreach ($data['items'] as $item) {
                // Check and Update Stock
                $product = Product::find($item['id']);
                if (!$product) {
                    throw new \Exception("Produto não encontrado: " . $item['id']);
                }
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Estoque insuficiente para: " . $product->name);
                }

                $product->stock -= $item['quantity'];
                $product->save();

                $saleItem = new SaleItem();
                $saleItem->id = (string) UUID::v4();
                $saleItem->sale_id = $saleId;
                $saleItem->product_id = $product->id;
                $saleItem->product_name = $product->name;
                $saleItem->price_in_cents = (int)round($item['price'] * 100);
                $saleItem->quantity = (int) $item['quantity'];
                $saleItem->save();
            }

            DB::commit();

            // Return created sale formatted
            return response()->json([
                'id' => $sale->id,
                'sellerId' => $sale->seller_id,
                'total' => $sale->total_in_cents / 100,
                'date' => $sale->created_at,
                'items' => $data['items'] // Return what was sent essentially, or re-fetch
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
