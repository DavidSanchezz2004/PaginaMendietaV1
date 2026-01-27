<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\PortalTutorial;
use Illuminate\Http\Request;

class ClienteTutorialController extends Controller
{
    public function show(string $slug)
    {
        $tutorial = PortalTutorial::where('slug',$slug)->where('status','published')->firstOrFail();
        return view('cliente.tutoriales.show', compact('tutorial'));
    }

    // ✅ launcher interno: el cliente NO ve youtube_url en HTML
    public function watch(Request $request, PortalTutorial $tutorial)
    {
        abort_unless($tutorial->status === 'published', 404);
        return redirect()->away($tutorial->youtube_url);
    }
}
