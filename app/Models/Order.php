<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'menu_id',
        'user_id',
        'restaurant_id',
        'bowl_qty',
        'total_amount',   
    ];

    // Securing these fields
    protected $guarded = [
        'receipt_status',
        'order_status',
        'delete_status'
    ];

    /**
    * Referencing the primary key in menu_items table
    */
    public function menu()
    {
        return $this->belongsTo(MenuItem::class, 'menu_id');
    }



    /**
    * Referencing the primary key in users table
    */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
    * Referencing the primary key in restaurant table
    */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }


}
