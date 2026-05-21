<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Support\Str;

class DistribucionLinkAdminController extends Controller
{
    private const ESTADOS = [
        'AGS'  => 'Aguascalientes',      'BCN'  => 'Baja California',
        'BCS'  => 'Baja California Sur', 'CAM'  => 'Campeche',
        'CHP'  => 'Chiapas',             'CHH'  => 'Chihuahua',
        'CDMX' => 'Ciudad de México',    'COA'  => 'Coahuila',
        'COL'  => 'Colima',              'DUR'  => 'Durango',
        'MEX'  => 'Estado de México',    'GTO'  => 'Guanajuato',
        'GRO'  => 'Guerrero',            'HGO'  => 'Hidalgo',
        'JAL'  => 'Jalisco',             'MIC'  => 'Michoacán',
        'MOR'  => 'Morelos',             'NAY'  => 'Nayarit',
        'NLE'  => 'Nuevo León',          'OAX'  => 'Oaxaca',
        'PUE'  => 'Puebla',              'QRO'  => 'Querétaro',
        'ROO'  => 'Quintana Roo',        'SLP'  => 'San Luis Potosí',
        'SIN'  => 'Sinaloa',             'SON'  => 'Sonora',
        'TAB'  => 'Tabasco',             'TAM'  => 'Tamaulipas',
        'TLAX' => 'Tlaxcala',            'VER'  => 'Veracruz',
        'YUC'  => 'Yucatán',             'ZAC'  => 'Zacatecas',
    ];

    public function index(): \Illuminate\View\View
    {
        $links = DB::table('distribuidor_links')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return view('admin.distribucion_links', [
            'links'   => $links,
            'estados' => self::ESTADOS,
            'appUrl'  => rtrim(config('app.url'), '/'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $password = trim((string) $request->input('password', ''));
        if ($password === '') {
            return back()->withInput()->withErrors(['password' => 'La contraseña es obligatoria.']);
        }

        $amount  = (float) str_replace(',', '', (string) $request->input('amount', '0'));
        $typePay = $request->input('type_pay', 'card') === 'free' ? 'free' : 'card';

        // Prefill — solo guardar campos que tengan valor
        $prefillFields = [
            'nombre', 'apellido_paterno', 'apellido_materno',
            'correo', 'telefono',
            'pais', 'region', 'municipio', 'ciudad',
            'calle', 'num_ext', 'num_int', 'cp', 'colonia',
            'fecha_nacimiento', 'pais_nacimiento', 'nacionalidad', 'ocupacion',
            'tipo_identificacion', 'identificacion_emitida_por', 'numero_identificacion',
            'rfc', 'tipo_persona',
        ];

        $prefill = [];
        foreach ($prefillFields as $field) {
            $val = trim((string) $request->input("prefill_{$field}", ''));
            if ($val !== '') $prefill[$field] = $val;
        }

        $token = Str::uuid()->toString();

        DB::table('distribuidor_links')->insert([
            'token'         => $token,
            'id_esquema'    => (int) $request->input('id_esquema', 0),
            'id_franquicia' => (int) $request->input('id_franquicia', 0),
            'id_solicitud'  => 0,
            'password'      => Hash::make($password),
            'active'        => 1,
            'amount'        => $amount,
            'type_pay'      => $typePay,
            'prefill_json'  => empty($prefill) ? null : json_encode($prefill, JSON_UNESCAPED_UNICODE),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->route('admin.dist-links.index')
            ->with('success', "Link creado correctamente.")
            ->with('new_token', $token);
    }

    public function destroy(int $id): RedirectResponse
    {
        $link = DB::table('distribuidor_links')->where('id', $id)->first();

        if ($link && $link->id_solicitud == 0) {
            DB::table('distribuidor_links')->where('id', $id)->delete();
        }

        return redirect()->route('admin.dist-links.index')
            ->with('success', 'Link eliminado.');
    }
}
