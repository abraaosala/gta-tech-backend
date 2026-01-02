<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

class LandingContact extends Model
{
    protected $table = 'landing_contacts';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'message',
        'created_at'
    ];

    public $timestamps = false;
}
