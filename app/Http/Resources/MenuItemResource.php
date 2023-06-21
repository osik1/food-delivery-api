<?php

namespace App\Http\Resources;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
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
            'restaurant_id' => $this->restaurant_id,
            'restaurant_name' => with(new Restaurant())->find($this->restaurant_id)->name ?? 'Unknown',
            'food_name' => $this->food_name,
            'image' => 'public/Menu-pics/' .$this->image,
            'price_per_bowl' => $this->price_per_bowl,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
