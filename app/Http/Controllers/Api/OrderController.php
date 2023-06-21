<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // if the one checking orders is a restaurant owner
        if(Auth::user()->role == 1)
        {
            //Get user id
            $user_id = Auth::user()->id;
            // Get the restaurant id
            $restaurant = Restaurant::where('user_id', $user_id)->first();
            $restaurant_id = $restaurant->id; 
            // get all orders with the restaurant id
            $order = QueryBuilder::for(Order::class)
            ->allowedFilters('menu_name', 'total_amount', 'user.name')
            ->where('restaurant_id', $restaurant_id)
            ->get();
            return $this->sendResponse(OrderResource::collection($order), 'Orders retrieved successfully.');
        }

        // if the one checking orders is a user
        if(Auth::user()->role == 0)
        {
            //Get user id
            $user_id = Auth::user()->id;
            $order = QueryBuilder::for(Order::class)
            ->allowedFilters('menu_name', 'total_amount')
            ->where('user_id', $user_id)
            ->get();
            return $this->sendResponse(OrderResource::collection($order), 'Your 0rders retrieved successfully.');
        }
        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function countOrders()
    {
        // if the one checking orders is a restaurant owner
        if(Auth::user()->role == 1)
        {
            //Get user id
            $user_id = Auth::user()->id;
            // Get the restaurant id
            $restaurant = Restaurant::where('user_id', $user_id)->first();
            $restaurant_id = $restaurant->id; 
            // count all orders with the restaurant id
            $order = Order::where('restaurant_id', $restaurant_id)->count();
            return $this->sendResponse($order, 'Number of orders retrieved successfully');
        }

        // if the one checking orders is a user
        if(Auth::user()->role == 0)
        {
            //Get user id
            $user_id = Auth::user()->id;
            // count all orders with the user id
            $order = Order::where('user_id', $user_id)->count();
            return $this->sendResponse($order, 'Number of orders retrieved successfully');
        }
        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }



    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
