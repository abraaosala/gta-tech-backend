<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'nif',
        'email',
        'phone',
        'address'
    ];

    public $timestamps = true;
}
