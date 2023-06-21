<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'name',
        'location',
        'email',
        'phone',
        'image'
    ];

    /**
     * Referencing the primary key in users table
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Referencing the foreign key in Menu Items table
     */
    public function menuItem()
    {
        return $this->hasMany(MenuItem::class, 'restaurant_id');
    }
}
