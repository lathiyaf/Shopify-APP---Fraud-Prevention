<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class AuthController extends Controller
{
    public function index(Request $request){
        return View::make(
            'auth.index',
            ['shopDomain' => $request->query('shop')]
        );
    }
}
