<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactoController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'titulo' => ['required','string','max:120'],
            'categoria' => ['required','in:tributario,facturacion,laboral,otros'],
            'urgencia' => ['required','in:baja,media,alta'],
            'mensaje' => ['required','string','max:4000'],
        ]);

        $user = $request->user();

        $to = config('mail.support_to', 'soporte@mscontables.com');

        $subject = '['.strtoupper($data['urgencia']).'] '.$data['titulo'].' ('.$data['categoria'].')';

        Mail::send([], [], function ($m) use ($to, $subject, $data, $user) {
            $m->to($to)
              ->subject($subject)
              ->replyTo($user->email, $user->name)
              ->setBody(
                "Cliente: {$user->name}\nEmail: {$user->email}\n\n".
                "Categoría: {$data['categoria']}\nUrgencia: {$data['urgencia']}\n\n".
                "Mensaje:\n{$data['mensaje']}\n",
                'text/plain'
              );
        });

        return back()->with('ok', 'Mensaje enviado. Te responderemos por correo.');
    }
}
