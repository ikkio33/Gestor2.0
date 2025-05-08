<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        
        return view('funcionario.dashboard');
    }
}
