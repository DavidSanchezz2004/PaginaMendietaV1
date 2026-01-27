<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use App\Models\PortalNews;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EquipoNewsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $rows = PortalNews::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('body', 'like', "%{$q}%")
                        ->orWhere('excerpt', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('equipo.noticias.index', compact('rows', 'q'));
    }

    public function create()
    {
        // Si luego quieres pasar categorías desde aquí, se puede.
        return view('equipo.noticias.create');
    }

    public function store(Request $request)
    {
        // ✅ Form en español (como tus blades)
        $form = $request->validate([
            'titulo' => ['required', 'string', 'max:190'],
            'categoria' => ['required', 'string', 'max:40'],
            'body' => ['required', 'string'],
            'estado' => ['required', 'in:borrador,publicado'],

            // opcionales
            'excerpt' => ['nullable', 'string', 'max:300'],
            'cover_image_url' => ['nullable', 'string', 'max:2000'],
        ]);

        // ✅ DB/model en inglés (como tu tabla: title/category/status)
        $data = [
            'title' => $form['titulo'],
            'category' => $form['categoria'],
            'body' => $form['body'],
            'excerpt' => $form['excerpt'] ?? null,
            'cover_image_url' => $form['cover_image_url'] ?? null,
            'status' => ($form['estado'] === 'publicado') ? 'published' : 'draft',
        ];

        // slug único
        $slug = Str::slug($data['title']);
        $base = $slug;
        $i = 2;
        while (PortalNews::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $data['slug'] = $slug;
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;
        $data['published_at'] = ($data['status'] === 'published') ? now() : null;

        PortalNews::create($data);

        // ✅ Tus rutas están como equipo.noticias.*
        return redirect()
            ->route('equipo.noticias.index')
            ->with('ok', 'Noticia creada.');
    }

    public function edit(PortalNews $news)
    {

        return view('equipo.noticias.edit', compact('news'));
    }

    public function update(Request $request, PortalNews $news)
    {
        $form = $request->validate([
            'titulo' => ['required', 'string', 'max:190'],
            'categoria' => ['required', 'string', 'max:40'],
            'body' => ['required', 'string'],
            'estado' => ['required', 'in:borrador,publicado'],

            'excerpt' => ['nullable', 'string', 'max:300'],
            'cover_image_url' => ['nullable', 'string', 'max:2000'],
        ]);

        $data = [
            'title' => $form['titulo'],
            'category' => $form['categoria'],
            'body' => $form['body'],
            'excerpt' => $form['excerpt'] ?? null,
            'cover_image_url' => $form['cover_image_url'] ?? null,
            'status' => ($form['estado'] === 'publicado') ? 'published' : 'draft',
        ];

        // si cambió el título, regenera slug (único)
        if ($data['title'] !== $news->title) {
            $slug = Str::slug($data['title']);
            $base = $slug;
            $i = 2;
            while (
                PortalNews::where('slug', $slug)
                    ->where('id', '!=', $news->id)
                    ->exists()
            ) {
                $slug = $base . '-' . $i++;
            }
            $data['slug'] = $slug;
        }

        $data['updated_by'] = $request->user()->id;

        // published_at según status
        if ($data['status'] === 'published' && !$news->published_at) {
            $data['published_at'] = now();
        }
        if ($data['status'] === 'draft') {
            $data['published_at'] = null;
        }

        $news->update($data);

        return redirect()
            ->route('equipo.noticias.index')
            ->with('ok', 'Noticia actualizada.');
    }

    public function destroy(PortalNews $news)
    {
        $news->delete();

        return redirect()
            ->route('equipo.noticias.index')
            ->with('ok', 'Noticia eliminada.');
    }
}
