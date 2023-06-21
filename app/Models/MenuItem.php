<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'restaurant_id',
        'food_name',
        'image',
        'price_per_bowl',
    ];

    /**
     * Referencing the primary key in restaurants table
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }


    
    /**
     * Referencing the foreign key in orders table
     */
    public function order()
    {
        return $this->hasMany(Order::class, 'menu_id');
    }
}
