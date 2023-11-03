<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class BackendController extends Controller
{
    public function index(): View
    {
        //Added By IT20179076 - Ensure that only authenticated users can access this view.
        $this->middleware('auth');
        return view('backend.index');
    }
}
