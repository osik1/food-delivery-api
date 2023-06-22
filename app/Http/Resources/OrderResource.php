<?php

namespace App\Http\Resources;

use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_code' => $this->order_code,
            'menu_id' => $this->menu_id,
            'menu' => with(new MenuItem())->find($this->menu_id)->food_name ?? 'Unknown',
            'restaurant_id' => $this->restaurant_id,
            'restaurant_name' => with(new Restaurant())->find($this->restaurant_id)->name ?? 'Unknown',
            'user_id' => $this->user_id,
            'customer_name' => with(new User())->find($this->user_id)->name ?? 'Unknown',
            'bowl_qty' => $this->bowl_qty,
            'total_amount' => $this->total_amount,
            'receipt_status' => $this->receipt_status == 2 ? 'Cancelled' : ($this->role == 1 ? 'Received' : 'Waiting for food'),
            'order_status' => $this->order_status == 3 ? 'Delivered' : ($this->order_status == 2 ? 'Delivering' : ($this->order_status == 1 ? 'Processing' : 'Received')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
