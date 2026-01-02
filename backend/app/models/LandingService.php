<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

class LandingService extends Model
{
    protected $table = 'landing_services';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'description',
        'icon',
        'price',
        'is_active',
        'created_at'
    ];

    public $timestamps = false;
}
