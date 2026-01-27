<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\PortalNews;

class ClienteNewsController extends Controller
{
    public function show(string $slug)
    {
        $news = PortalNews::where('slug',$slug)->where('status','published')->firstOrFail();
        return view('cliente.noticias.show', compact('news'));
    }
}
