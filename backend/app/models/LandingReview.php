<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

class LandingReview extends Model
{
    protected $table = 'landing_reviews';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_name',
        'customer_role',
        'testimonial',
        'rating',
        'created_at'
    ];

    public $timestamps = false;
}
