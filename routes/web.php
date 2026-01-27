<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

use App\Http\Middleware\NoStore;
use App\Http\Middleware\RequireMfaForEquipo;

use App\Http\Controllers\MfaChallengeController;
use App\Http\Controllers\Equipo\MfaController;

use App\Http\Controllers\Equipo\EmpresaController;
use App\Http\Controllers\Equipo\OperadorController;
use App\Http\Controllers\Equipo\CredentialController;
use App\Http\Controllers\Equipo\AssignmentController;
use App\Http\Controllers\Equipo\PortalAccountController;
use App\Http\Controllers\Equipo\JobResultController;

use App\Http\Controllers\Equipo\UsuarioController;
use App\Http\Controllers\Cliente\AssistantController;
use App\Http\Controllers\Equipo\ReporteController;
use App\Http\Controllers\Cliente\ReporteClienteController;
use App\Http\Controllers\Equipo\EquipoNewsController;
use App\Http\Controllers\Equipo\EquipoTutorialController;

use App\Http\Controllers\Cliente\ClienteNewsController;
use App\Http\Controllers\Cliente\ClienteTutorialController;
use App\Http\Controllers\Cliente\ContactoController;


// Feasy
use App\Http\Controllers\FeasyInvoiceController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Home → redirección por rol
|--------------------------------------------------------------------------
*/
Route::get('/home', function () {
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    return $user->rol === 'cliente'
        ? redirect()->route('cliente.panel')
        : redirect()->route('equipo.dashboard');

})->middleware(['auth', 'verified'])->name('home');

/*
|--------------------------------------------------------------------------
| Alias /dashboard (fallback)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| MFA Challenge (después del login para internos)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mfa/challenge', [MfaChallengeController::class, 'show'])->name('mfa.challenge');
    Route::post('/mfa/challenge', [MfaChallengeController::class, 'verify'])->name('mfa.challenge.verify');
});

/*
|--------------------------------------------------------------------------
| Zona EQUIPO (internos) + MFA obligatorio
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', NoStore::class, 'role:equipo', RequireMfaForEquipo::class])
    ->prefix('equipo')
    ->name('equipo.')
    ->group(function () {

        Route::get('/ping', fn() => 'OK EQUIPO')->name('ping');

        Route::get('/dashboard', function () {
            return view('equipo.dashboard');
        })->name('dashboard');

        Route::get('/perfil', function () {
            return view('equipo.perfil');
        })->name('perfil');

        // MFA setup / enable / disable
        Route::get('/mfa', [MfaController::class, 'setup'])->name('mfa.setup');
        Route::post('/mfa/enable', [MfaController::class, 'enable'])->name('mfa.enable');
        Route::post('/mfa/disable', [MfaController::class, 'disable'])->name('mfa.disable');

        // Confirmación MFA (re-confirm)
        Route::get('/mfa/confirm', [MfaChallengeController::class, 'confirmShow'])
            ->name('mfa.confirm.show');
        Route::post('/mfa/confirm', [MfaChallengeController::class, 'confirmVerify'])
            ->name('mfa.confirm.verify');

        /*
        |--------------------------------------------------------------------------
        | Usuarios del portal (admin)
        |--------------------------------------------------------------------------
        */
        Route::get('/usuarios', [UsuarioController::class, 'index'])
            ->middleware('recent_mfa:10')
            ->name('usuarios.index');

        Route::get('/usuarios/create', [UsuarioController::class, 'create'])
            ->middleware('recent_mfa:10')
            ->name('usuarios.create');

        Route::post('/usuarios', [UsuarioController::class, 'store'])
            ->middleware('recent_mfa:10')
            ->name('usuarios.store');

        Route::get('/usuarios/{user}/edit', [UsuarioController::class, 'edit'])
            ->middleware('recent_mfa:10')
            ->name('usuarios.edit');

        Route::put('/usuarios/{user}', [UsuarioController::class, 'update'])
            ->middleware('recent_mfa:10')
            ->name('usuarios.update');

        Route::delete('/usuarios/{user}', [UsuarioController::class, 'destroy'])
            ->middleware('recent_mfa:10')
            ->name('usuarios.destroy');

        /*
        |--------------------------------------------------------------------------
        | Empresas
        |--------------------------------------------------------------------------
        */
        Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas.index');
        Route::get('/empresas/crear', [EmpresaController::class, 'create'])->name('empresas.create');
        Route::post('/empresas', [EmpresaController::class, 'store'])->name('empresas.store');
        Route::get('/empresas/{company}', [EmpresaController::class, 'show'])->name('empresas.show');

        Route::get('/empresas/ruc/{ruc}', [EmpresaController::class, 'lookupRuc'])
            ->where('ruc', '[0-9]{11}')
            ->middleware('throttle:30,1')
            ->name('empresas.lookupRuc');

        Route::get('/empresas/{company}/edit', [EmpresaController::class, 'edit'])
            ->middleware('recent_mfa:10')
            ->name('empresas.edit');

        Route::get('/empresas/{company}/delete', [EmpresaController::class, 'deleteConfirm'])
            ->middleware('recent_mfa:10')
            ->name('empresas.delete');

        Route::put('/empresas/{company}', [EmpresaController::class, 'update'])
            ->middleware('recent_mfa:10')
            ->name('empresas.update');

        Route::delete('/empresas/{company}', [EmpresaController::class, 'destroy'])
            ->middleware('recent_mfa:10')
            ->name('empresas.destroy');

        /*
        |--------------------------------------------------------------------------
        | Operadores (App Users)
        |--------------------------------------------------------------------------
        */
        Route::get('/operadores', [OperadorController::class, 'index'])->name('operadores.index');

        Route::get('/operadores/create', [OperadorController::class, 'create'])
            ->middleware('recent_mfa:10')
            ->name('operadores.create');

        Route::post('/operadores', [OperadorController::class, 'store'])
            ->middleware('recent_mfa:10')
            ->name('operadores.store');

        Route::get('/operadores/{operador}/edit', [OperadorController::class, 'edit'])
            ->middleware('recent_mfa:10')
            ->name('operadores.edit');

        Route::put('/operadores/{operador}', [OperadorController::class, 'update'])
            ->middleware('recent_mfa:10')
            ->name('operadores.update');

        Route::delete('/operadores/{operador}', [OperadorController::class, 'destroy'])
            ->middleware('recent_mfa:10')
            ->name('operadores.destroy');

        /*
        |--------------------------------------------------------------------------
        | Credenciales (Portal Credentials)
        |--------------------------------------------------------------------------
        */
        Route::get('/credenciales', [CredentialController::class, 'index'])
            ->name('credenciales.index');

        Route::get('/credenciales/{portalAccount}/create', [CredentialController::class, 'create'])
            ->middleware('recent_mfa:10')
            ->name('credenciales.create');

        Route::post('/credenciales/{portalAccount}', [CredentialController::class, 'store'])
            ->middleware('recent_mfa:10')
            ->name('credenciales.store');

        Route::get('/credenciales/{portalAccount}/edit', [CredentialController::class, 'edit'])
            ->middleware('recent_mfa:10')
            ->name('credenciales.edit');

        Route::put('/credenciales/{portalAccount}', [CredentialController::class, 'update'])
            ->middleware('recent_mfa:10')
            ->name('credenciales.update');


        Route::post('/empresas/{company}/clientes/{user}/assign', [EmpresaController::class, 'assignCliente'])
        ->name('empresas.clientes.assign');

    Route::post('/empresas/{company}/clientes/{user}/unassign', [EmpresaController::class, 'unassignCliente'])
        ->name('empresas.clientes.unassign');



        /*
        |--------------------------------------------------------------------------
        | Asignaciones
        |--------------------------------------------------------------------------
        */
        Route::get('/asignaciones', [AssignmentController::class, 'index'])->name('asignaciones.index');

        Route::get('/asignaciones/create', [AssignmentController::class, 'create'])
            ->middleware('recent_mfa:10')
            ->name('asignaciones.create');

        Route::post('/asignaciones', [AssignmentController::class, 'store'])
            ->middleware('recent_mfa:10')
            ->name('asignaciones.store');

        Route::patch('/asignaciones/{assignment}/toggle', [AssignmentController::class, 'toggle'])
            ->middleware('recent_mfa:10')
            ->name('asignaciones.toggle');

        Route::delete('/asignaciones/{assignment}', [AssignmentController::class, 'destroy'])
            ->middleware('recent_mfa:10')
            ->name('asignaciones.destroy');

        /*
        |--------------------------------------------------------------------------
        | Portales por empresa (PortalAccount)
        |--------------------------------------------------------------------------
        */
        Route::get('/empresas/{company}/portales', [PortalAccountController::class, 'edit'])
            ->middleware('recent_mfa:10')
            ->name('empresas.portales.edit');

        Route::post('/empresas/{company}/portales', [PortalAccountController::class, 'update'])
            ->middleware('recent_mfa:10')
            ->name('empresas.portales.update');

        /*
        |--------------------------------------------------------------------------
        | Jobs / Resultados
        |--------------------------------------------------------------------------
        */
        Route::get('/jobs', [JobResultController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/{job}', [JobResultController::class, 'show'])->name('jobs.show');

        /*
        |--------------------------------------------------------------------------
        | FEASY Facturas
        |--------------------------------------------------------------------------
        */
        Route::get('/facturas', [FeasyInvoiceController::class, 'index'])->name('facturas.index');
        Route::get('/facturas/nueva', [FeasyInvoiceController::class, 'create'])->name('facturas.create');
        Route::post('/facturas', [FeasyInvoiceController::class, 'store'])->name('facturas.store');

        Route::get('/lookup/ruc/{ruc}', [FeasyInvoiceController::class, 'lookupRuc'])->name('lookup.ruc');
        Route::get('/lookup/dni/{dni}', [FeasyInvoiceController::class, 'lookupDni'])->name('lookup.dni');

        // Route::post('/facturas/consultar', [FeasyInvoiceController::class, 'consultarFactura'])
        //     ->middleware('throttle:30,1')
        //     ->name('facturas.consultar');

        Route::get('/facturas/descargar', [FeasyInvoiceController::class, 'descargar'])
            ->name('facturas.descargar');

        Route::post('/facturas/{invoice}/consultar', [FeasyInvoiceController::class, 'consultarYActualizar'])
            ->name('facturas.consultar');

        Route::post('/facturas/{invoice}/refresh', [FeasyInvoiceController::class, 'refreshFromFeasy'])
            ->name('facturas.refresh');

        /*
|---------------------------------------------------------------------- 
| Reportes (Power BI publish-to-web)
|---------------------------------------------------------------------- 
*/
Route::get('/reportes', [ReporteController::class, 'index'])
    ->name('reportes.index');

Route::get('/reportes/create', [ReporteController::class, 'create'])
    ->middleware('recent_mfa:10')
    ->name('reportes.create');

Route::post('/reportes', [ReporteController::class, 'store'])
    ->middleware('recent_mfa:10')
    ->name('reportes.store');

Route::get('/reportes/{reporte}/edit', [ReporteController::class, 'edit'])
    ->middleware('recent_mfa:10')
    ->name('reportes.edit');

Route::put('/reportes/{reporte}', [ReporteController::class, 'update'])
    ->middleware('recent_mfa:10')
    ->name('reportes.update');

Route::delete('/reportes/{reporte}', [ReporteController::class, 'destroy'])
    ->middleware('recent_mfa:10')
    ->name('reportes.destroy');

// ✅ SHOW al final (si lo quieres)
Route::get('/reportes/{reporte}', [ReporteController::class, 'show'])
    ->name('reportes.show');

                // En tu group('equipo') ...
        Route::post('/empresas/{company}/portal/toggle', [EmpresaController::class, 'togglePortal'])
        ->middleware('recent_mfa:10')
        ->name('empresas.portal.toggle');

        Route::post('/empresas/{company}/portal/crear-cliente', [EmpresaController::class, 'createPortalClient'])
        ->middleware('recent_mfa:10')
        ->name('empresas.portal.crearCliente');

        // (opcional dev) marcar verificado manual (para pruebas)
        Route::post('/empresas/{company}/portal/clientes/{user}/mark-verified', [EmpresaController::class, 'markClientVerified'])
        ->middleware('recent_mfa:10')
        ->name('empresas.portal.markVerified');

         // NOTICIAS
        Route::get('/noticias', [EquipoNewsController::class,'index'])->name('noticias.index');
        Route::get('/noticias/create', [EquipoNewsController::class,'create'])->name('noticias.create');
        Route::post('/noticias', [EquipoNewsController::class,'store'])->name('noticias.store');
        Route::get('/noticias/{news}/edit', [EquipoNewsController::class,'edit'])->name('noticias.edit');
        Route::put('/noticias/{news}', [EquipoNewsController::class,'update'])->name('noticias.update');
        Route::delete('/noticias/{news}', [EquipoNewsController::class,'destroy'])->name('noticias.destroy');


    // TUTORIALES
    Route::get('/tutoriales', [EquipoTutorialController::class,'index'])->name('tutorials.index');
    Route::get('/tutoriales/create', [EquipoTutorialController::class,'create'])->name('tutorials.create');
    Route::post('/tutoriales', [EquipoTutorialController::class,'store'])->name('tutorials.store');
    Route::get('/tutoriales/{tutorial}/edit', [EquipoTutorialController::class,'edit'])->name('tutorials.edit');
    Route::put('/tutoriales/{tutorial}', [EquipoTutorialController::class,'update'])->name('tutorials.update');
    Route::delete('/tutoriales/{tutorial}', [EquipoTutorialController::class,'destroy'])->name('tutorials.destroy');


        
    });

/*
|--------------------------------------------------------------------------
| Zona CLIENTE (sin MFA)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', NoStore::class, 'role:cliente'])
    ->prefix('cliente')
    ->name('cliente.')
    ->group(function () {

        Route::get('/ping', fn() => 'OK CLIENTE')->name('ping');

        Route::get('/panel', function () {
            return view('cliente.panel');
        })->name('panel');

         // 👇 PERFIL CLIENTE
        Route::get('/perfil', function () {
            return view('cliente.perfil.perfil');
        })->name('perfil');

        Route::get('/asistente', [AssistantController::class, 'show'])->name('assistant.show');
        Route::post('/asistente/query', [AssistantController::class, 'query'])
        ->middleware('throttle:30,1') // 30 por minuto por usuario
        ->name('assistant.query');

        /*
        |----------------------------------------------------------------------
        | Mis reportes (cliente) - launcher seguro
        |----------------------------------------------------------------------
        */
        Route::get('/reportes', [ReporteClienteController::class, 'index'])
            ->name('reportes.index');

        Route::get('/reportes/{reporte}/ver', [ReporteClienteController::class, 'ver'])
            ->middleware('throttle:30,1')
            ->name('reportes.ver');

        // 1 SOLO LUGAR
    Route::get('/novedades', [\App\Http\Controllers\Cliente\NovedadesController::class, 'index'])
      ->name('novedades.index');

//       // Tutoriales (Cliente)
// Route::get('/tutoriales', [ClienteTutorialController::class, 'index'])
//   ->name('tutoriales.index');
//     // Noticias
//     Route::get('/noticias/{slug}', [ClienteNewsController::class,'show'])->name('news.show');

//     // Tutoriales
//     Route::get('/tutoriales/{slug}', [ClienteTutorialController::class,'show'])->name('tutorials.show');

//     // ✅ launcher (redirect) youtube
//     Route::get('/tutoriales/{tutorial}/ver', [ClienteTutorialController::class,'watch'])
//       ->name('tutorials.watch')
//       ->middleware('throttle:30,1');

//       Route::get('/tutoriales/{tutorial}/ver', [ClienteTutorialController::class,'watch'])
//   ->name('tutoriales.ver')
//   ->middleware('throttle:30,1');

// Tutoriales (Cliente) - listado
Route::get('/tutoriales', [ClienteTutorialController::class, 'index'])
  ->name('tutoriales.index');

   // Noticias
Route::get('/noticias/{slug}', [ClienteNewsController::class,'show'])->name('news.show');


// Tutoriales (Cliente) - detalle por slug
Route::get('/tutoriales/{slug}', [ClienteTutorialController::class, 'show'])
  ->name('tutoriales.show');

// ✅ Launcher (redirect a youtube) por ID (route model binding)
Route::get('/tutoriales/{tutorial}/ver', [ClienteTutorialController::class, 'watch'])
  ->name('tutoriales.ver')
  ->middleware('throttle:30,1');



    // Escríbenos (correo)
    Route::post('/contacto', [ContactoController::class,'send'])
      ->name('contacto.send')
      ->middleware('throttle:5,1');

    });

/*
|--------------------------------------------------------------------------
| Perfil de usuario (Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
