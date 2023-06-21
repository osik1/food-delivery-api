<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class RestaurantController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $restaurant = QueryBuilder::for(Restaurant::class)
        ->allowedFilters('name', 'location')
        ->get();
        return $this->sendResponse(RestaurantResource::collection($restaurant), 'Restaurants retrieved successfully.');
    }



    /**
     * Count restaurants.
     */
    public function countRestaurants()
    {
        $restaurant = Restaurant::count();
        return $this->sendResponse($restaurant, 'Number of restaurants retrieved successfully');
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
                'name' => 'required|string|max:55|unique:restaurants',
                'location' => 'required|string|max:55',
                'email' => 'required|email|unique:restaurants',
                'phone' => 'required|numeric|min:10|unique:restaurants',
                'image' => 'required|image|mimes:jpg,png,jpeg,svg|max:2048',
            ]);
            // process the image
            $restaurantImage = time().'.'.$request->file('image')->getClientOriginalExtension(); 
            $request->image->move(public_path('Restaurant-pics'), $restaurantImage);
    
            // Get the user id.
            $restaurantOwner = Auth::user()->id;
    
            // Send data to database
            $restaurant = Restaurant::create($input);
            $restaurant->user_id = $restaurantOwner;
            $restaurant->image = $restaurantImage;
            $restaurant->save();
            return $this->sendResponse(new RestaurantResource($restaurant), 'Restaurant created successully');
        }
        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);

    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Find restaurant by their id
        $restaurant = Restaurant::find($id);
        if (is_null($restaurant))
        {
            return $this->sendError('Error', 'Restaurant not found');
        }
        return $this->sendResponse(new RestaurantResource($restaurant), 'Restaurant created successully');
    }

   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find restaurant by their id
        $restaurant = Restaurant::find($id);
        if (is_null($restaurant))
        {
            return $this->sendError('Error', 'Restaurant not found');
        }
        $input = $request->all();
        $request->validate([
            'name' => 'required|string|max:55|Unique:restaurants,name,'.$restaurant->id,
            'location' => 'required|string|max:55',
            'email' => 'required|email|Unique:restaurants,email,'.$restaurant->id,
            'phone' => 'required|numeric|min:10|Unique:restaurants,phone,'.$restaurant->id,
            'image' => 'required|image|mimes:jpg,png,jpeg,svg|max:2048',
        ]);

        // process the image
        $restaurantImage = time().'.'.$request->file('image')->getClientOriginalExtension(); 
        $request->image->move(public_path('Restaurant-pics'), $restaurantImage);

        // Check if the user is the owner of the restaurant
        $restaurantOwner = Auth::user()->id;
        if ($restaurant->user_id == $restaurantOwner)
        {
            // update the data with new inputs
            $restaurant->name = $input['name'];
            $restaurant->location = $input['location'];
            $restaurant->email = $input['email'];
            $restaurant->phone = $input['phone'];
            $restaurant->image = $restaurantImage;
            $restaurant->save();
            return $this->sendResponse(new RestaurantResource($restaurant), 'Restaurant details updated successully');
        } 
        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Restaurant can be deleted by only admin
        if (Auth::user()->role == 2)
        {
            // Find restaurant by their id
            $restaurant = Restaurant::find($id);
            if (is_null($restaurant))
            {
                return $this->sendError('Error', 'Restaurant not found');
            }
            // delete if found
            $restaurant->delete();
            return $this->sendResponse([], 'Restaurant deleted successfully');
        }
        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    }
}
