<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\View\View;

class UserController extends Controller
{
    public function getAllUser(): Response
    {
        return response(User::get());
    }
    public function getUserById(string $id): Response
    {
        return response(User::findOrFail($id));
    }

    // public function login(Request $request): Response
    // {
    //     return null;
    // }
}
