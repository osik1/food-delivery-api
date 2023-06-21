<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\MenuItemResource;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class MenuItemController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menuItem = QueryBuilder::for(MenuItem::class)
        ->allowedFilters('restaurant.name', 'food_name', 'price_per_bowl')
        ->get();
        return $this->sendResponse(MenuItemResource::collection($menuItem), 'Food Menus retrieved successfully');
    }



    /**
     * Count restaurants.
     */
    public function countMenus()
    {
        $menuItem = MenuItem::count();
        return $this->sendResponse($menuItem, 'Number of menus retrieved successfully');
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Make sure user has the right to add restaurant
        if (Auth::user()->role == 1)
        {
            $input = $request->all();
            $request->validate([
                'food_name' => 'required|string|max:55',
                'price_per_bowl' => 'required|numeric',
                'image' => 'required|image|mimes:jpg,png,jpeg,svg|max:2048',
            ]);

            // process the image
            $menuImage = time().'.'.$request->file('image')->getClientOriginalExtension(); 
            $request->image->move(public_path('Menu-pics'), $menuImage);
    
            // Get the user id.
            $restaurantOwner = Auth::user()->id;
            // Get the restaurant owner's restaurant id
            $restaurant = Restaurant::where('user_id', $restaurantOwner)->first();
            if (is_null($restaurant))
            {
                return $this->sendError('Error', 'You have no restaurant');
            }
            $restaurant_id = $restaurant->id;
    
            // Send data to database
            $menuItem = MenuItem::create($input);
            $menuItem->restaurant_id = $restaurant_id;
            $menuItem->image = $menuImage;
            $menuItem->save();
            return $this->sendResponse(new MenuItemResource($menuItem), 'Menu created successfully');
        }
        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);

    }




    /**
     * Display the specified resource.
     */
    public function show($id)
    {
       // Find menu by their id
       $menuItem = MenuItem::find($id);
       if (is_null($menuItem))
        {
           return $this->sendError('Error', 'Menu not found');
        }
       return $this->sendResponse(new MenuItemResource($menuItem), 'Menu retrieved successfully');
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find menu by their id
        $menuItem = MenuItem::find($id);
        if (is_null($menuItem))
        {
            return $this->sendError('Error', 'Menu not found');
        }
        $input = $request->all();
        $request->validate([
            'food_name' => 'required|string|max:55',
            'price_per_bowl' => 'required|numeric',
            'image' => 'required|image|mimes:jpg,png,jpeg,svg|max:2048',
        ]);

        // process the image
        $menuImage = time().'.'.$request->file('image')->getClientOriginalExtension(); 
        $request->image->move(public_path('Menu-pics'), $menuImage);

        // Get the user id.
        $restaurantOwner = Auth::user()->id;
        // Get the restaurant owner's restaurant id
        $restaurant = Restaurant::where('user_id', $restaurantOwner)->first();
        if (is_null($restaurant))
        {
            return $this->sendError('Error', 'You have no restaurant');
        }
        $restaurant_id = $restaurant->id;

        // Check if the owner of the restaurant owns the menu
        if ($menuItem->restaurant_id == $restaurant_id)
        {
            // update the data with new inputs
            $menuItem->food_name = $input['food_name'];
            $menuItem->price_per_bowl = $input['price_per_bowl'];
            $menuItem->image = $menuImage;
            $menuItem->save();
            return $this->sendResponse(new MenuItemResource($menuItem), 'Menu details updated successfully');
        } 
        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
       // Menu can be deleted by only Restaurant owners
       if (Auth::user()->role == 1)
       {
           // Find menu by their id
           $menuItem = MenuItem::find($id);
           if (is_null($menuItem))
           {
               return $this->sendError('Error', 'Menu not found');
           }

           // Get the user id.
            $restaurantOwner = Auth::user()->id;
            // Get the restaurant owner's restaurant id
            $restaurant = Restaurant::where('user_id', $restaurantOwner)->first();
            if (is_null($restaurant))
            {
                return $this->sendError('Error', 'You have no restaurant');
            }
            $restaurant_id = $restaurant->id;

            if ($menuItem->restaurant_id == $restaurant_id)
            {
                // delete if found
                $menuItem->delete();
                return $this->sendResponse([], 'Menu deleted successfully');
            }
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
         
       }
       return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    }


    
}
