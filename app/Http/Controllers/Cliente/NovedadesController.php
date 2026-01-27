<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\PortalNews;
use App\Models\PortalTutorial;
use Illuminate\Http\Request;

class NovedadesController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->query('q',''));

        $news = PortalNews::query()
            ->where('status','published')
            ->when($q !== '', fn($qq) =>
                $qq->where('title','like',"%{$q}%")
                   ->orWhere('excerpt','like',"%{$q}%")
            )
            ->orderByDesc('published_at')
            ->limit(12)
            ->get();

        $tutorials = PortalTutorial::query()
            ->where('status','published')
            ->when($q !== '', fn($qq) =>
                $qq->where('title','like',"%{$q}%")
                   ->orWhere('excerpt','like',"%{$q}%")
            )
            ->orderByDesc('published_at')
            ->limit(12)
            ->get();

        return view('cliente.novedades.index', compact('news','tutorials','q'));
    }
}
