<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'seller_id',
        'total_in_cents',
        'payment_method',
        'status',
        'created_at',
        'customer_id'
    ];

    public $timestamps = false; // logic handles created_at manually or default

    public function items()
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
