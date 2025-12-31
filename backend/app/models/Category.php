<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'created_at'
    ];

    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
