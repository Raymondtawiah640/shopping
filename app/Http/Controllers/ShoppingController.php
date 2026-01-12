<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ShoppingUser;
use App\Http\Requests\Shopping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShoppingController extends Controller
{
    protected $shoppingUser;

    public function __construct(ShoppingUser $shoppingUser)
    {
        $this->shoppingUser = $shoppingUser;
    }

    public function login(Shopping $request)
    {
        Log::info('Login attempt in controller', ['email' => $request->email]);
        $credentials = $request->only('email', 'password');
        return $this->shoppingUser->login($credentials);
    }
}
