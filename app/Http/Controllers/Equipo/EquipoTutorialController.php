<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use App\Models\PortalTutorial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EquipoTutorialController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->query('q',''));

        $rows = PortalTutorial::query()
            ->when($q !== '', fn($qq) =>
                $qq->where('title','like',"%{$q}%")
                   ->orWhere('excerpt','like',"%{$q}%")
            )
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('equipo.tutoriales.index', compact('rows','q'));
    }

    public function create()
    {
        return view('equipo.tutoriales.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:190'],
            'category' => ['required','string','max:40'],
            'excerpt' => ['nullable','string','max:300'],
            'body' => ['nullable','string'],
            'cover_image_url' => ['nullable','string','max:2000'],
            'youtube_url' => ['required','string','max:2000'],
            'duration_label' => ['nullable','string','max:20'],
            'status' => ['required','in:draft,published'],
        ]);

        $slug = Str::slug($data['title']);
        $base = $slug; $i=2;
        while (PortalTutorial::where('slug',$slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        $data['slug'] = $slug;
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;
        $data['published_at'] = $data['status']==='published' ? now() : null;

        PortalTutorial::create($data);

        return redirect()->route('equipo.tutorials.index')->with('ok','Tutorial creado.');
    }

    public function edit(PortalTutorial $tutorial)
    {
        return view('equipo.tutoriales.edit', compact('tutorial'));
    }

    public function update(Request $request, PortalTutorial $tutorial)
    {
        $data = $request->validate([
            'title' => ['required','string','max:190'],
            'category' => ['required','string','max:40'],
            'excerpt' => ['nullable','string','max:300'],
            'body' => ['nullable','string'],
            'cover_image_url' => ['nullable','string','max:2000'],
            'youtube_url' => ['required','string','max:2000'],
            'duration_label' => ['nullable','string','max:20'],
            'status' => ['required','in:draft,published'],
        ]);

        if ($data['title'] !== $tutorial->title) {
            $slug = Str::slug($data['title']);
            $base=$slug; $i=2;
            while (PortalTutorial::where('slug',$slug)->where('id','!=',$tutorial->id)->exists()) {
                $slug = $base.'-'.$i++;
            }
            $data['slug'] = $slug;
        }

        $data['updated_by'] = $request->user()->id;

        if ($data['status']==='published' && !$tutorial->published_at) {
            $data['published_at'] = now();
        }
        if ($data['status']==='draft') {
            $data['published_at'] = null;
        }

        $tutorial->update($data);

        return redirect()->route('equipo.tutorials.index')->with('ok','Tutorial actualizado.');
    }

    public function destroy(PortalTutorial $tutorial)
    {
        $tutorial->delete();
        return redirect()->route('equipo.tutorials.index')->with('ok','Tutorial eliminado.');
    }
}
