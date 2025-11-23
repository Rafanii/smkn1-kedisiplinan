<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Dashboard\WaliMuridDashboardController;

class OrtuDashboardController extends Controller
{
    // Deprecated shim: delegate to WaliMuridDashboardController to preserve behavior
    public function index(Request $request)
    {
        return app(WaliMuridDashboardController::class)->index($request);
    }
}