<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;

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
}
