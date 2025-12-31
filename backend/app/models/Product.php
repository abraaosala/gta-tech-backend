<?php
namespace app\models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [  
        'id',
        'name',
        'description',
        'price_in_cents',
        'stock',
        'image_url',
        'category_id'
      ];

      
    
        public $timestamps = false;

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }
    
}