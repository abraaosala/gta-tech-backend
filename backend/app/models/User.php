<?php
namespace app\models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    public $timestamps = false;
    
     public $incrementing = false; // 🔥 essencial

    protected $keyType = 'string'; // 🔥 essencial

    protected $fillable = [
        'id',
        'name',
        'email',
        'password_hash',
        'role'
    ];

    
}