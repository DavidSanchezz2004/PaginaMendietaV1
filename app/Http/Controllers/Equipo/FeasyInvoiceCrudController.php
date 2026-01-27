<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use App\Models\FeasyInvoice;

class FeasyInvoiceCrudController extends Controller
{
    public function index()
    {
        $rows = FeasyInvoice::orderByDesc('id')->paginate(20);

        return view('equipo.facturas.index', compact('rows'));
    }
}
