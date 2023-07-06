<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ActionLogger;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     $this->authorizeResource(User::class, 'users');
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        
        $users = User::latest('updated_at')->get();

        return $users;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create',User::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->authorize('create',User::class);

        $validator = Validator::make($request->all(),
            ['name' => 'required|string|max:255',
                'email' => 'required|email|unique:users|max:255',
                'password' => 'required|min:10',
                'role' => 'required']);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['error' => $errors], 400);
        }
        if ($validator->passes()) {
            $user = User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password),'role' => $request->role,'status' => $request->status,]);
            $user->assignRole($request->role);
            $token = $user->createToken('auth_token')->plainTextToken;
            // sending this event to logs in database
            ActionLogger::log($user, 'UserController', 'store');
            // end of sending event
            return response()->json(['access_token' => $token, 'token_type' => 'Bearer',], 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('view',$user);
        
        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $this->authorize('update',$user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validator = Validator::make($request->all(),
            ['name' => 'string|max:255',
                'email' => 'email|max:255',
                'password' => 'min:10',
                'role' => '']);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['error' => $errors], 400);
        }
        if ($validator->passes()) {
            $user->update(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password),'role' => $request->role,'status' => $request->status,]);
            $user->syncRoles([$request->role]);
            $token = $user->createToken('auth_token')->plainTextToken;
            // sending this event to logs in database
            ActionLogger::log($user, 'UserController', 'update');
            // end of sending event
            return response()->json(['access_token' => $token, 'token_type' => 'Bearer',]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();

        // sending this event to logs in database
        ActionLogger::log($user, 'UserController', 'destroy');
        // end of sending event

        return response()->noContent();
    }
}
