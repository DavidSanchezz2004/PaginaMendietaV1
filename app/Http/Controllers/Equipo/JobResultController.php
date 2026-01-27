<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use App\Models\PortalJob;
use Illuminate\Http\Request;

class JobResultController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $portal = $request->query('portal', '');   // sunat|sunafil|afp
        $status = $request->query('status', '');  // pending|running|done|failed

        $jobs = PortalJob::query()
            ->with([
                'company:id,ruc,razon_social',
                'appUser:id,username',
                'latestResult',
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('job_uid', 'like', "%{$q}%")
                      ->orWhere('device_id', 'like', "%{$q}%")
                      ->orWhere('action', 'like', "%{$q}%")
                      ->orWhereHas('company', function ($c) use ($q) {
                          $c->where('ruc', 'like', "%{$q}%")
                            ->orWhere('razon_social', 'like', "%{$q}%");
                      })
                      ->orWhereHas('appUser', function ($u) use ($q) {
                          $u->where('username', 'like', "%{$q}%");
                      });
            })
            ->when(in_array($portal, ['sunat','sunafil','afp'], true), fn($qq) => $qq->where('portal', $portal))
            ->when($status !== '', fn($qq) => $qq->where('status', $status))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('equipo.jobs.index', compact('jobs','q','portal','status'));
    }

    public function show(PortalJob $job)
    {
        $job->load([
            'company:id,ruc,razon_social',
            'appUser:id,username',
            'results' => function ($q) {
                $q->orderByDesc('id');
            },
        ]);

        return view('equipo.jobs.show', compact('job'));
    }
}
