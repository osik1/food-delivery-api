<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\OrderResource;
use App\Models\MenuItem;
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
            ->allowedFilters('menu_name', 'total_amount', 'user.name', 'order_code')
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
            ->allowedFilters('menu_name', 'total_amount', 'order_code')
            ->where('user_id', $user_id)
            ->where('delete_status', 1)
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
    public function store(Request $request, $menu_id)
    {
        //
        $input = $request->all();
            $request->validate([
                'bowl_qty' => 'required|numeric',
            ]);
        // Let's get the details of the menu from the menu id
        $menu = MenuItem::find($menu_id);
        if(is_null($menu))
        {
            return $this->sendError('Error', 'Menu not found!');
        }
        // Lets get the cost of the food
        $food_price = $menu->price_per_bowl;
        // Let's calculate the total amount the client will pay
        $total_amount = $food_price * $input['bowl_qty'];
        // Let's get the restaurant id
        $restaurant_id = $menu->restaurant_id;
        // Let's get the user
        $user_id = Auth::user()->id;

        // Now let's create the order
        $order = Order::create($input);
        $order->order_code = uniqid();
        $order->restaurant_id = $restaurant_id;
        $order->menu_id = $menu_id;
        $order->user_id = $user_id;
        $order->total_amount = $total_amount;
        $order->save();
        return $this->sendResponse(new OrderResource($order), 'Order created successfully');

    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Find order by the id
        $order = Order::find($id);
        if (is_null($order))
        {
            return $this->sendError('Error', 'Order not found');
        }
        return $this->sendResponse(new OrderResource($order), 'Order retrieved successfully');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find order by the id
        $order = Order::find($id);
        if (is_null($order))
        {
            return $this->sendError('Error', 'Order not found');
        }

        $input = $request->all();
            $request->validate([
                'bowl_qty' => 'required|numeric',
            ]);

        // Let's get the details of the menu from the menu id
        $menu_id = $order->menu_id;
        $menu = MenuItem::find($menu_id);
        if(is_null($menu))
        {
            return $this->sendError('Error', 'Menu not found!');
        }

        // Lets get the cost of the food
        $food_price = $menu->price_per_bowl;
        // Let's calculate the total amount the client will pay
        $total_amount = $food_price * $input['bowl_qty'];

        // Let's make sure the order belongs to the user before updating
        $user_id = Auth::user()->id;
        if ($order->user_id == $user_id)
        {
            $order->bowl_qty = $input['bowl_qty'];
            $order->total_amount = $total_amount;
            $order->save();
            return $this->sendResponse(new OrderResource($order), 'Order updated successfully');
        }
        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    }



    /**
     * Restaurant Owner can Let users know they are processing the order 
     */
    public function processingOrder($order_id)
    {
        // Only Restaurant owners can process orders
        if (Auth::user()->role == 1)
        {
            $order = Order::find($order_id);
            if (is_null($order))
            {
                return $this->sendError('Error', 'Order not found');
            }
            $order->order_status = 1;
            $order->save();
            return $this->sendResponse(new OrderResource($order), 'Order set to proccessing successfully');
        }
        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    }



    /**
     * Restaurant Owner can Let users know they are delivering the order 
     */
    public function deliveringOrder($order_id)
    {
        // Only Restaurant owners can process orders
        if (Auth::user()->role == 1)
        {
            $order = Order::find($order_id);
            if (is_null($order))
            {
                return $this->sendError('Error', 'Order not found');
            }
            $order->order_status = 2;
            $order->save();
            return $this->sendResponse(new OrderResource($order), 'Order set to delivering successfully');
        }
        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    }


     /**
     * Restaurant Owner can Let users know they are delivered the order 
     */
    public function deliveredOrder($order_id)
    {
        // Only Restaurant owners can process orders
        if (Auth::user()->role == 1)
        {
            $order = Order::find($order_id);
            if (is_null($order))
            {
                return $this->sendError('Error', 'Order not found');
            }
            $order->order_status = 3;
            $order->save();
            return $this->sendResponse(new OrderResource($order), 'Order set to delivered successfully');
        }
        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    }


    /**
     * Users can let restaurant owners know they have received the order
     */
    public function receivedOrder($order_id)
    {
        $order = Order::find($order_id);
        if (is_null($order))
        {
            return $this->sendError('Error', 'Order not found');
        }
        // Check if user owns the order
        $user_id = Auth::user()->id;
        if ($order->user_id == $user_id)
        {
            $order->receipt_status = 1;
            $order->save();
            return $this->sendResponse(new OrderResource($order), 'Order set to received successfully');
        }
    
    }


     /**
     * Users can let restaurant owners know they have cancelled the order
     */
    public function cancelOrder($order_id)
    {
        $order = Order::find($order_id);
        if (is_null($order))
        {
            return $this->sendError('Error', 'Order not found');
        }
        // Check if user owns the order
        $user_id = Auth::user()->id;
        if ($order->user_id == $user_id)
        {
            $order->receipt_status = 2;
            $order->save();
            return $this->sendResponse(new OrderResource($order), 'Order set to cancelled successfully');
        }
    
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find order by the id
        $order = Order::find($id);
        if (is_null($order))
        {
            return $this->sendError('Error', 'Order not found');
        }
        // Check if user owns or created that order
        $user_id = Auth::user()->id;
        if ($order->user_id == $user_id);
        {
           // Users might want to delete their orders, so we can hide it for them.
            $order->delete_status = 0;
            $order->save();
            return $this->sendResponse([], 'Order deleted successfully');

        } 

    }
}
