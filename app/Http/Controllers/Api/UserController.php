<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends BaseController
{

       /**
     * Register/Signup function
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $input = $request->all();
        $request->validate([
            'name' => 'bail|required|string|max:55|unique:users',
            'email' => 'required|email|unique:users',
            'phone' => 'required|numeric|min:10|unique:users',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password', 
        ]);
       
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        if ($user->id == 1)
        {
            $user->role = 2;
            $user->save();
        }
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['username'] = $user->name;
        $success['id'] = $user->id;
        return $this->sendResponse($success, 'User registered successfully.'); 

    }



    /**
     * Login function
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
       
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name'] = $user->name;
            $success['email'] = $user->email;
            $success['phone'] = $user->phone;
            $success['location'] = $user->location;
            $success['gps'] = $user->gps;
            $success['avatar'] = $user->avatar;
            $success['role'] = $user->role == 2 ? 'Admin' : ($user->role == 1 ? 'Restaurant Owner' : 'user');
            $success['created_at'] = $user->created_at;
            $success['updated_at'] = $user->updated_at;
            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }



    /**
     * Logout function
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse([], 'User logout successfully.');
    }



    /**
     * forgot password function that sends email to users
     *
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status  = Password::sendResetLink(
            $request->only('email')
        );

        if($status === Password::RESET_LINK_SENT){
            return $this->sendResponse([], __($status));
        }else{
            return $this->sendError('Error', __($status)); 
        }

    }



     /**
     * Reset password when the user click on the link in the email
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );
        if($status === Password::PASSWORD_RESET){
            return $this->sendResponse([], __($status));
        }else{
            return $this->sendError('Error', __($status));
        }


    }



     /**
     * Function to change the password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
        ]);

        $user = Auth::user();
        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();
            return $this->sendResponse($user, 'Password changed successfully');
        } else {
            return $this->sendError('Password change failed', 'Old password entered is incorrect');
            
        }
    }



     /**
     * Function to show the user's profile
     */
    public function profile()
    {
        //Fetch logged in user data
        $user = Auth::user();
        return $this->sendResponse(new UserResource($user), 'User profile retrieved successfully.');
        
    }




    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $users = QueryBuilder::for(User::class)
        ->allowedFilters('name', 'location')
        ->get();
        return $this->sendResponse(UserResource::collection($users), 'Users retrieved successfully.');
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
