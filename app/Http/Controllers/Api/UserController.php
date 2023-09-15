<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index() {
        return response()->json([
            'code' => 200,
            'message' => 'All users succesfully retrieved',
            'data' => UserResource::collection(User::all())
        ]);
    }
}
