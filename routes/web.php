<?php

use App\Http\Controllers\AdquirirController;
use App\Http\Controllers\Admin\DistribucionLinkAdminController;
use App\Http\Controllers\Admin\SolicitudDistribuidorAdminController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EspecialidadesController;
use App\Http\Controllers\PagoDistribucionController;
use App\Http\Controllers\PagosController;
use App\Http\Controllers\PasaporteController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Portal\PortalAccesoController;
use App\Http\Controllers\Portal\PortalPasaporteController;
use App\Http\Controllers\Pats\DistribucionLinkController;
use App\Http\Controllers\Pats\FranquiciaLinkController;
use App\Http\Controllers\Pats\SolicitudDistribucionController;
use App\Http\Controllers\Pats\SolicitudFranquiciaController;
use App\Http\Controllers\Pats\SolicitudPatsController;
use App\Http\Controllers\Pats\StripeDistribucionController;
use App\Http\Controllers\Pats\StripeDistribucionLinkController;
use App\Http\Controllers\Pats\StripeFranquiciaController;
use App\Http\Controllers\Pats\StripePatsController;
use App\Http\Controllers\ServiciosController;
use Illuminate\Support\Facades\Route;

// ──────────────────────────────────────────────────────────────────────────────
//  PÚBLICA — landing
// ──────────────────────────────────────────────────────────────────────────────

Route::get('/', fn () => view('welcome'));

// ──────────────────────────────────────────────────────────────────────────────
//  AUTENTICACIÓN (guard: pasaporte)
// ──────────────────────────────────────────────────────────────────────────────

Route::middleware('guest:pasaporte')->group(function () {
    Route::get( '/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth:pasaporte');

// ──────────────────────────────────────────────────────────────────────────────
//  ÁREA DE USUARIO (guard: pasaporte)
// ──────────────────────────────────────────────────────────────────────────────

Route::middleware('auth:pasaporte')->group(function () {

    // Dashboard / servicios
    Route::get('/servicios', function () {
        $user      = auth('pasaporte')->user();
        $pasaporte = null;
        if ($user->id_pasaporte) {
            $pasaporte = \Illuminate\Support\Facades\DB::table('pats_pasaportes')
                ->where('id_pasaporte', $user->id_pasaporte)
                ->first();
        }
        return view('servicios.index', compact('user', 'pasaporte'));
    })->name('servicios');

    Route::get('/pasaporte', [PasaporteController::class,  'index'])->name('pasaporte');
    Route::get('/pagos',     [PagosController::class,      'index'])->name('pagos');

    Route::get( '/perfil',          [PerfilController::class, 'show'])->name('perfil');
    Route::post('/perfil/campo',    [PerfilController::class, 'actualizarCampo'])->name('perfil.campo');
    Route::post('/perfil/foto',     [PerfilController::class, 'actualizarFoto'])->name('perfil.foto');
    Route::post('/perfil/password', [PerfilController::class, 'actualizarPassword'])->name('perfil.password');
});

// ──────────────────────────────────────────────────────────────────────────────
//  SERVICIOS / ESPECIALIDADES — públicos
// ──────────────────────────────────────────────────────────────────────────────


Route::controller(ServiciosController::class)->group(function () {
    Route::get('/atencion-medica',   'atencionMedica')->name('atencion.index');
    Route::get('/estudios-clinicos', 'estudiosСlinicos')->name('estudios.index');
    Route::get('/farmacia',          'farmacia')->name('farmacia.index');
    Route::get('/rayos',             'rayos')->name('rayos.index');
    Route::get('/hospitales',        'hospitales')->name('hospitales.index');
});

Route::get( '/especialidades',                   [EspecialidadesController::class, 'index'])->name('especialidades.index');
Route::get( '/especialidades/{idRecurso}/agenda',[EspecialidadesController::class, 'bloquesMedico'])->name('especialidades.agenda');
Route::post('/especialidades/cita',              [EspecialidadesController::class, 'guardarCita'])->name('especialidades.guardar');

Route::get( '/agenda',          [AgendaController::class, 'index'])->name('agenda.index');
Route::get( '/agenda/dia/{fecha}', [AgendaController::class, 'dia'])->name('agenda.dia');

// ──────────────────────────────────────────────────────────────────────────────
//  FORMULARIOS PÚBLICOS — Distribución
// ──────────────────────────────────────────────────────────────────────────────

Route::get( '/distribucion/solicitud',             [SolicitudDistribucionController::class, 'showPublico'])->name('dist.publico.show');
Route::post('/distribucion/solicitud',             [SolicitudDistribucionController::class, 'guardarPublico'])->name('dist.publico.guardar');
Route::post('/distribucion/solicitud/pre-validar', [SolicitudDistribucionController::class, 'preValidar'])->name('dist.publico.pre-validar');
Route::get( '/distribucion/solicitud/confirmacion',[SolicitudDistribucionController::class, 'confirmacion'])->name('dist.publico.confirmacion');
Route::post('/distribucion/stripe/intent',         [StripeDistribucionController::class,    'createIntent'])->name('dist.stripe.intent');

// Links de distribución (protegidos por contraseña)
Route::get( '/distribucion/link/{token}',              [DistribucionLinkController::class, 'show'])->name('dist.link.show');
Route::post('/distribucion/link/{token}/auth',          [DistribucionLinkController::class, 'auth'])->name('dist.link.auth');
Route::get( '/distribucion/link/{token}/formulario',    [DistribucionLinkController::class, 'formulario'])->name('dist.link.formulario');
Route::post('/distribucion/link/{token}/pre-validar',   [DistribucionLinkController::class, 'preValidar'])->name('dist.link.pre-validar');
Route::post('/distribucion/link/{token}/guardar',       [DistribucionLinkController::class, 'guardar'])->name('dist.link.guardar');
Route::post('/distribucion/link/{token}/stripe/intent', [StripeDistribucionLinkController::class, 'createIntent'])->name('dist.link.stripe.intent');

Route::get('/pats/distribucion',       [PagoDistribucionController::class, 'show'])->name('pats.pago-distribucion.show');
Route::post('/pats/distribucion/orden',[PagoDistribucionController::class, 'generarOrden'])->name('pats.pago-distribucion.generar-orden');
Route::get('/pats/distribucion/solicitudes', [SolicitudDistribucionController::class, 'listar'])->name('dist.solicitudes.listar');

// ──────────────────────────────────────────────────────────────────────────────
//  FORMULARIOS PÚBLICOS — Franquicia
// ──────────────────────────────────────────────────────────────────────────────

Route::get( '/franquicia/solicitud',                 [SolicitudFranquiciaController::class, 'showPublico'])->name('franq.publico.show');
Route::post('/franquicia/solicitud',                 [SolicitudFranquiciaController::class, 'guardarPublico'])->name('franq.publico.guardar');
Route::post('/franquicia/solicitud/pre-validar',     [SolicitudFranquiciaController::class, 'preValidar'])->name('franq.publico.pre-validar');
Route::get( '/franquicia/solicitud/confirmacion',    [SolicitudFranquiciaController::class, 'confirmacion'])->name('franq.publico.confirmacion');
Route::post('/franquicia/stripe/intent',             [StripeFranquiciaController::class,    'createIntent'])->name('franq.stripe.intent');

// Links de franquicia (protegidos por contraseña)
Route::get( '/franquicia/link/{token}',               [FranquiciaLinkController::class, 'show'])->name('franq.link.show');
Route::post('/franquicia/link/{token}/auth',           [FranquiciaLinkController::class, 'auth'])->name('franq.link.auth');
Route::get( '/franquicia/link/{token}/formulario',     [FranquiciaLinkController::class, 'formulario'])->name('franq.link.formulario');
Route::post('/franquicia/link/{token}/pre-validar',    [FranquiciaLinkController::class, 'preValidar'])->name('franq.link.pre-validar');
Route::post('/franquicia/link/{token}/guardar',        [FranquiciaLinkController::class, 'guardar'])->name('franq.link.guardar');
Route::post('/franquicia/link/{token}/contrato-preview',[FranquiciaLinkController::class, 'contratoPreview'])->name('franq.link.contrato-preview');

// ──────────────────────────────────────────────────────────────────────────────
//  FORMULARIOS PÚBLICOS — Registro PATS
// ──────────────────────────────────────────────────────────────────────────────

Route::get( '/pats/registro',                    [SolicitudPatsController::class, 'show'])->name('pats.registro.show');
Route::get( '/pats/registro/directo',            [SolicitudPatsController::class, 'showDirecto'])->name('pats.registro.directo');
Route::post('/pats/registro/orden',              [SolicitudPatsController::class, 'generarOrden'])->name('pats.registro.orden');
Route::post('/pats/registro/contrato',           [SolicitudPatsController::class, 'contratoPreview'])->name('pats.registro.contrato');
Route::post('/pats/registro/pasaporte-validar',  [SolicitudPatsController::class, 'validarPasaporte'])->name('pats.registro.pasaporte.validar');
Route::post('/pats/registro/stripe/intent',      [StripePatsController::class,    'createIntent'])->name('pats.registro.stripe.intent');

Route::get( '/adquirir',          [AdquirirController::class, 'show'])->name('adquirir');
Route::post('/adquirir/procesar', [AdquirirController::class, 'procesar'])->name('adquirir.procesar');

// ──────────────────────────────────────────────────────────────────────────────
//  ADMIN
// ──────────────────────────────────────────────────────────────────────────────

Route::prefix('admin/distribucion-links')->name('admin.dist-links.')->group(function () {
    Route::get('/',        [DistribucionLinkAdminController::class, 'index'])->name('index');
    Route::post('/',       [DistribucionLinkAdminController::class, 'store'])->name('store');
    Route::delete('/{id}', [DistribucionLinkAdminController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
});

Route::prefix('admin/solicitudes-distribuidor')->name('admin.dist.')->group(function () {
    Route::get('/',                    [SolicitudDistribuidorAdminController::class, 'index'])->name('index');
    Route::get('/{id}',                [SolicitudDistribuidorAdminController::class, 'show'])->name('show')->where('id', '[0-9]+');
    Route::post('/{id}/accion',        [SolicitudDistribuidorAdminController::class, 'accion'])->name('accion')->where('id', '[0-9]+');
    Route::get('/{id}/archivo/{tipo}', [SolicitudDistribuidorAdminController::class, 'archivo'])->name('archivo')->where('id', '[0-9]+');
});

// ──────────────────────────────────────────────────────────────────────────────
//  PORTAL PATS (/portal — guard: pasaporte)
// ──────────────────────────────────────────────────────────────────────────────

Route::prefix('portal')->name('portal.')->group(function () {

    Route::middleware('guest:pasaporte')->group(function () {
        Route::get( '/login', [PortalAccesoController::class, 'showLogin'])->name('login');
        Route::post('/login', [PortalAccesoController::class, 'login'])->name('login.post');
    });

    Route::post('/logout', [PortalAccesoController::class, 'logout'])->name('logout');

    Route::middleware('auth:pasaporte')->group(function () {
        Route::get('/', fn () => redirect()->route('servicios'))->name('dashboard');
        Route::get('/perfil',    [PortalPasaporteController::class, 'perfil'])->name('perfil');
        Route::get('/{seccion}', [PortalPasaporteController::class, 'proximamente'])->name('proximamente')
            ->where('seccion', 'pagos|beneficios|soporte');
    });
});

// ──────────────────────────────────────────────────────────────────────────────
//  CONTRATOS (públicos — usados por iframes)
// ──────────────────────────────────────────────────────────────────────────────

Route::get('/contrato/franquicia',     fn () => view('pats.contrato_franq'))->name('franq.contrato');
Route::get('/contrato/franquicia/en',  fn () => view('pats.contrato_dist_en'))->name('franq.contrato.en');
