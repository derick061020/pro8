<?php
namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\System\Configuration;
use App\Models\System\Skin as SystemSkin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\System\Client;
use App\Models\System\Plan;
use Hyn\Tenancy\Environment;
use Modules\Finance\Helpers\UploadFileHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;


class ConfigurationController extends Controller
{

    public function index()
    {
        $configuration = Configuration::first();
        $plans = Plan::where('id', '!=', 1)
            ->select('id', 'name', 'pricing', 'limit_documents', 'limit_users')
            ->get();
        return view('system.configuration.index', compact('configuration', 'plans'));
    }

    public function record()
    {

        $configuration = Configuration::first();

        return [
            'token_public_culqui' => $configuration->token_public_culqui,
            'token_private_culqui' => $configuration->token_private_culqui,
            'enabled_culqi' => $configuration->enabled_culqi,
            'enabled_izipay' => $configuration->enabled_izipay,
            'username_izipay' => $configuration->username_izipay,
            'password_izipay' => $configuration->password_izipay,
            'publickey_izipay' => $configuration->publickey_izipay,
            'sha256key_izipay' => $configuration->sha256key_izipay,
            'enabled_mp' => $configuration->enabled_mp,
            'access_token_mp' => $configuration->access_token_mp,
            'public_key_mp' => $configuration->public_key_mp,
        ];
    }


    public function store(Request $request)
    {
        $configuration = Configuration::first();


        if($request->token_public_culqui)
        {
            $configuration->token_public_culqui = $request->token_public_culqui;
        }

        if($request->token_private_culqui)
        {
            $configuration->token_private_culqui = $request->token_private_culqui;
        }

        if($request->url_apiruc)
        {
            $configuration->url_apiruc = $request->url_apiruc;
        }

        if($request->token_apiruc)
        {
            $configuration->token_apiruc = $request->token_apiruc;
        }

        if($request->apk_url)
        {
            $configuration->apk_url = $request->apk_url;
            $this->updateApkUrl($request->apk_url);
        }

        $configuration->fill($request->all());
        $configuration->save();

        return [
            'success' => true,
            'message' => 'Datos guardados con exito'
        ];
    }

    public function apiruc()
    {

        $configuration = Configuration::first();

        return [
            'url_apiruc' => $configuration->url_apiruc,
            'token_apiruc' => $configuration->token_apiruc,
        ];
    }

    public function storeLoginSettings()
    {
        request()->validate([
            'position_form' => 'required|in:left,right',
            'show_logo_in_form' => 'required|boolean',
            'position_logo' => 'required|in:on-form,top-left,top-right,bottom-left,bottom-right,none',
            'padding_in_form' => 'required|boolean',
            'show_socials' => 'required|boolean',
            'use_login_global' => 'required|boolean',
            'login_bg_color' => 'nullable|string|max:50',
        ]);

        $config = Configuration::first();
        $loginConfig = $config->login;
        
        // Campos que van en la configuración login (JSON)
        $loginFields = ['position_form', 'show_logo_in_form', 'position_logo', 'padding_in_form', 'show_socials', 'facebook', 'twitter', 'instagram', 'linkedin', 'tiktok'];
        
        foreach($loginFields as $field) {
            if (request()->has($field)) {
                $loginConfig->$field = request($field);
            }
        }

        $config->login = $loginConfig;
        $config->use_login_global = request('use_login_global');
        
        // El color de fondo se guarda directamente en la columna login_bg_color
        if (request()->has('login_bg_color')) {
            $config->login_bg_color = request('login_bg_color');
        }
        
        $config->save();

        return response()->json([
            'success' => true,
            'message' => 'Información actualizada.',
        ], 200);
    }

    public function storeBgLogin()
    {
        request()->validate([
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        $config = Configuration::first();
        if (request()->hasFile('image') && request()->file('image')->isValid()) {
            $file = request()->file('image');
            $ext = $file->getClientOriginalExtension();
            $name = time() . '.' . $ext;
            $path = 'public/uploads/login';

            UploadFileHelper::checkIfValidFile($name, $file->getPathName(), true);

            $file->storeAs($path, $name);

            $loginConfig = $config->login;
            $basePathStorage = 'storage/uploads/login/';
			if (request('type') === 'bg') {
                $loginConfig->type = 'image';
				$loginConfig->image = asset($basePathStorage . $name);
			} else {
                $loginConfig->logo = asset($basePathStorage . $name);
            }
            $config->login = $loginConfig;
            $config->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Información actualizada.',
        ], 200);
    }

    public function InfoIndex(){
        $memory_limit = ini_get('memory_limit');
        $memory_in_byte = number_format(self::return_bytes($memory_limit),'2',',','.');
        $pcre_backtrack_limit = ini_get('pcre.backtrack_limit');
        $all_config = [
            'max_execution_time' =>ini_get('max_execution_time'),
            'max_input_time' =>ini_get('max_input_time'),
            'post_max_size' =>ini_get('post_max_size'),
            'upload_max_filesize' =>ini_get('upload_max_filesize'),
            'request_terminate_timeout' =>ini_get('request_terminate_timeout'),
            'date_timezone' =>ini_get('date.timezone'),
            'version_laravel' => app()->version(),
        ];

        return view('system.configuration.info', compact(
            'memory_limit',
            'memory_in_byte',
            'pcre_backtrack_limit',
            'all_config'
        ));

    }

    private  static function return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        $val = substr($val, 0, -1);
        switch($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }

    public function apkurl()
    {

        $configuration = Configuration::first();

        return [
            'apk_url' => $configuration->apk_url
        ];
    }

    public function updateApkUrl ($apk_url)
    {
        DB::connection('system')->transaction(function () use ($apk_url) {

            $records = Client::get();

            foreach ($records as $row) {

                $tenancy = app(Environment::class);
                $tenancy->tenant($row->hostname->website);

                DB::connection('tenant')->table('configurations')->where('id', 1)->update(['apk_url' => $apk_url]);

            }

        });
    }


    /**
     *
     * Actualizar el descuento global a 02 (Afecta la base) en todos los clientes
     *
     * @return array
     */
    public function updateTenantDiscountTypeBase()
    {

        DB::connection('system')->transaction(function (){

            $records = Client::get();

            foreach ($records as $row)
            {
                $tenancy = app(Environment::class);
                $tenancy->tenant($row->hostname->website);

                DB::connection('tenant')->table('configurations')->where('id', 1)->update(['global_discount_type_id' => '02']);
            }

        });

        return [
            'success' => true,
            'message' => 'El proceso se realizó correctamente'
        ];
    }


    /**
     *
     * @param  Request $request
     * @return array
     */
    public function storeOtherConfiguration(Request $request)
    {
        $record = Configuration::first();
        $record->regex_password_client = $request->regex_password_client;
        $record->tenant_show_ads = $request->tenant_show_ads;
        if ($request->has('enable_guest_register')) {
            $record->enable_guest_register = (bool) $request->enable_guest_register;
        }
        if ($request->has('guest_register_plan_id')) {
            $record->guest_register_plan_id = $request->guest_register_plan_id;
        }
        if ($request->has('validate_ruc_register')) {
            $record->validate_ruc_register = (bool) $request->validate_ruc_register;
        }
        $record->save();

        return [
            'success' => true,
            'message' => 'Configuración actualizada',
        ];
    }


    /**
     *
     * @return array
     */
    public function getOtherConfiguration()
    {
        return Configuration::select([
                                'regex_password_client',
                                'tenant_show_ads',
                                'tenant_image_ads',
                                'enable_guest_register',
                                'guest_register_plan_id',
                                'validate_ruc_register'
                            ])
                            ->firstOrFail();
    }


    /**
     *
     * @param  Request $request
     * @return array
     */
    public function uploadTenantAds(Request $request)
    {
        if ($request->hasFile('file'))
        {
            $configuration = Configuration::firstOrFail();

            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension();
            $name = 'tenant_image_ads_'.date('YmdHis').'.'.$ext;

            request()->validate(['file' => 'required|mimes:jpeg,png,jpg,gif,svg,webp|max:2048']);
            UploadFileHelper::checkIfValidFile($name, $file->getPathName(), true);
            $file->storeAs('public/uploads/system_ads', $name);

            $configuration->tenant_image_ads = $name;
            $configuration->save();

            return [
                'success' => true,
                'message' => __('app.actions.upload.success'),
                'name' => $name,
            ];
        }

        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'nullable|string',
        ]);

        $this->applyMailConfiguration($request->all());

        $recipient = Auth::user()->email ?? $request->user()->email;
        if (empty($recipient)) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró una dirección de correo válida para el usuario actual.',
            ], 422);
        }

        try {
            Mail::raw('Este es un correo de prueba para verificar la configuración SMTP.', function ($message) use ($recipient) {
                $message->to($recipient)->subject('Prueba de configuración SMTP');
            });

            return [
                'success' => true,
                'message' => 'Correo de prueba enviado a ' . $recipient,
            ];
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo de prueba: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function applyMailConfiguration(array $data)
    {
        Config::set('mail.host', $data['mail_host'] ?? null);
        Config::set('mail.port', $data['mail_port'] ?? null);
        Config::set('mail.username', $data['mail_username'] ?? null);
        Config::set('mail.password', $data['mail_password'] ?? null);
        Config::set('mail.encryption', $data['mail_encryption'] ?? null);
    }

    public function emails(Request $request)
    {
        $record = Configuration::first();
        $record->mail_host = $request->mail_host;  
        $record->mail_port = $request->mail_port;  
        $record->mail_username = $request->mail_username;  
        $record->mail_password = $request->mail_password;  
        $record->mail_encryption = $request->mail_encryption;
        $record->save();

        return [
            'success' => true,
            'message' => 'Configuración actualizada',
        ];
    }

    /**
     * Store visual theme configuration
     */
    public function storeVisualTheme(Request $request)
    {
        try {
            $configuration = Configuration::first();
            
            if (!$configuration) {
                $configuration = new Configuration();
            }

            // Obtener la configuración visual actual o crear una nueva
            $visual = $configuration->visual ? json_decode($configuration->visual, true) : [];
            
            // Actualizar solo el tema de color
            if ($request->has('theme_color')) {
                $visual['theme_color'] = $request->theme_color;
            }
            
            $configuration->visual = json_encode($visual);
            $configuration->save();

            return response()->json([
                'success' => true,
                'message' => 'Tema guardado correctamente',
                'theme' => $request->theme_color
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el tema: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get visual theme configuration
     */
    public function getVisualTheme()
    {
        try {
            $configuration = Configuration::first();
            
            if (!$configuration || !$configuration->visual) {
                return response()->json([
                    'success' => true,
                    'theme_color' => 'white' // tema por defecto
                ]);
            }

            $visual = json_decode($configuration->visual, true);
            
            return response()->json([
                'success' => true,
                'theme_color' => $visual['theme_color'] ?? 'white'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el tema: ' . $e->getMessage(),
                'theme_color' => 'white' // fallback
            ], 500);
        }
    }
    public function googleMaps(Request $request)
    {
        $record = Configuration::first();
        $record->google_maps_api_key = $request->google_maps_api_key;
        $record->save();

        return [
            'success' => true,
            'message' => 'Configuración actualizada',
        ];

    }

    /**
     * Configuración de la página pública de Términos y Condiciones.
     * Un único modo (disabled|content|url) garantiza que nunca se sirvan las dos fuentes a la vez.
     */
    public function getTerms()
    {
        return Configuration::select(['terms_mode', 'terms_content', 'terms_url'])->firstOrFail();
    }

    public function storeTerms(Request $request)
    {
        $request->validate([
            'terms_mode' => 'required|in:disabled,content,url',
            'terms_content' => 'nullable|string',
            'terms_url' => 'nullable|url|max:255',
        ]);

        if ($request->terms_mode === 'url' && empty($request->terms_url)) {
            return response()->json([
                'terms_url' => ['Debe indicar una URL para el modo "URL externa".'],
            ], 422);
        }

        $record = Configuration::first();
        $record->terms_mode = $request->terms_mode;

        // Se persiste solo la fuente del modo activo y se limpia la otra:
        // así nunca quedan ambas almacenadas a la vez.
        if ($request->terms_mode === 'content') {
            $record->terms_content = $request->terms_content;
            $record->terms_url = null;
        } elseif ($request->terms_mode === 'url') {
            $record->terms_url = $request->terms_url;
            $record->terms_content = null;
        }

        $record->save();

        return [
            'success' => true,
            'message' => 'Configuración actualizada',
        ];
    }

    public function evolutionServer(Request $request)
    {
        $request->validate([
            'evolution_server_url' => 'nullable|string|max:255',
            'evolution_server_apikey' => 'nullable|string|max:255',
        ]);

        $record = Configuration::first();
        $record->evolution_server_url = rtrim($request->evolution_server_url ?? '', '/') ?: null;
        $record->evolution_server_apikey = $request->evolution_server_apikey;
        $record->save();

        return [
            'success' => true,
            'message' => 'Configuración del servidor Evolution actualizada',
        ];
    }

    public function cron(Request $request)
    {
        $record = Configuration::first();
        $record->active_cron = $request->active_cron;
        $record->save();

        return [
            'success' => true,
            'message' => 'Configuración actualizada',
        ];

    }

    public function getSystemSkins()
    {
        $skins = SystemSkin::all()->map(fn($s) => $s->getCollectionData());

        return response()->json([
            'success' => true,
            'skins'   => $skins,
        ]);
    }

    public function checkSystemSkinName(Request $request)
    {
        $original = $request->query('filename', '');
        $baseName = pathinfo($original, PATHINFO_FILENAME);
        $filename = $baseName . '.css';
        $exists   = Storage::disk('public')->exists('skins' . DIRECTORY_SEPARATOR . $filename);

        $suggested = $filename;
        if ($exists) {
            $counter = 1;
            do {
                $suggested = $baseName . '_' . $counter . '.css';
                $counter++;
            } while (Storage::disk('public')->exists('skins' . DIRECTORY_SEPARATOR . $suggested));
        }

        return response()->json([
            'exists'    => $exists,
            'suggested' => pathinfo($suggested, PATHINFO_FILENAME),
        ]);
    }

    public function uploadSystemSkin(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => __('app.actions.upload.error')]);
        }

        $file = $request->file('file');

        if (strtolower($file->getClientOriginalExtension()) !== 'css') {
            return response()->json(['success' => false, 'message' => 'Solo se permiten archivos .css']);
        }

        // Usar nombre personalizado si el admin lo definió, o el nombre original
        $customName = trim($request->input('rename_to', ''));
        $baseName   = $customName !== '' ? $customName : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename   = $baseName . '.css';

        if (Storage::disk('public')->exists('skins' . DIRECTORY_SEPARATOR . $filename)) {
            return response()->json(['success' => false, 'message' => 'El archivo "' . $filename . '" ya existe. Elige otro nombre.']);
        }

        $name = $baseName;

        Storage::disk('public')->put('skins' . DIRECTORY_SEPARATOR . $filename, file_get_contents($file->getRealPath()));

        $skin = SystemSkin::create(['name' => $name, 'filename' => $filename, 'is_default' => false]);

        $clients = Client::with('hostname.website')->get();
        foreach ($clients as $client) {
            try {
                $tenancy = app(Environment::class);
                $tenancy->tenant($client->hostname->website);
                $exists = DB::connection('tenant')->table('skins')->where('filename', $filename)->exists();
                if (!$exists) {
                    DB::connection('tenant')->table('skins')->insert([
                        'name'      => $name,
                        'filename'  => $filename,
                        'status'    => 1,
                        'is_system' => true,
                    ]);
                }
            } catch (\Exception $e) {
                // Continuar con el siguiente tenant si hay error
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tema subido correctamente',
            'skins'   => SystemSkin::all()->map(fn($s) => $s->getCollectionData()),
        ]);
    }

    public function deleteSystemSkin(Request $request)
    {
        $skin = SystemSkin::find($request->id);

        if (!$skin) {
            return response()->json(['success' => false, 'message' => 'Tema no encontrado']);
        }

        if ($skin->is_default) {
            return response()->json(['success' => false, 'message' => 'No se pueden eliminar los temas por defecto']);
        }

        Storage::disk('public')->delete('skins' . DIRECTORY_SEPARATOR . $skin->filename);

        $clients = Client::with('hostname.website')->get();
        foreach ($clients as $client) {
            try {
                $tenancy = app(Environment::class);
                $tenancy->tenant($client->hostname->website);
                DB::connection('tenant')->table('skins')
                    ->where('filename', $skin->filename)
                    ->where('is_system', true)
                    ->delete();
            } catch (\Exception $e) {
                // Continuar con el siguiente tenant si hay error
            }
        }

        $skin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tema eliminado correctamente',
            'skins'   => SystemSkin::all()->map(fn($s) => $s->getCollectionData()),
        ]);
    }

    public function replaceSystemSkin(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => __('app.actions.upload.error')]);
        }

        $file = $request->file('file');

        if (strtolower($file->getClientOriginalExtension()) !== 'css') {
            return response()->json(['success' => false, 'message' => 'Solo se permiten archivos .css']);
        }

        $skin = SystemSkin::find($request->skin_id);

        if (!$skin || !$skin->is_default) {
            return response()->json(['success' => false, 'message' => 'Solo se pueden reemplazar los temas por defecto']);
        }

        // Nombre único para el override — el archivo original nunca se toca
        $baseName       = pathinfo($skin->filename, PATHINFO_FILENAME);
        $newFilename    = $baseName . '_override_' . date('YmdHis') . '.css';
        $oldActiveFile  = $skin->custom_filename; // puede ser null o un override anterior

        Storage::disk('public')->put('skins' . DIRECTORY_SEPARATOR . $newFilename, file_get_contents($file->getRealPath()));

        // Propagar a todos los tenants: actualizar el filename activo
        $oldTenantFilename = $oldActiveFile ?? $skin->filename;
        $clients = Client::with('hostname.website')->get();
        foreach ($clients as $client) {
            try {
                $tenancy = app(Environment::class);
                $tenancy->tenant($client->hostname->website);
                DB::connection('tenant')->table('skins')
                    ->where('filename', $oldTenantFilename)
                    ->where('is_system', true)
                    ->update(['filename' => $newFilename]);
            } catch (\Exception $e) {
                // Continuar con el siguiente tenant si hay error
            }
        }

        // Eliminar físicamente el override anterior si existía
        if ($oldActiveFile) {
            $oldPath = storage_path('app/public/skins/' . $oldActiveFile);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $skin->update(['custom_filename' => $newFilename]);

        return response()->json([
            'success' => true,
            'message' => 'Tema "' . $skin->name . '" reemplazado correctamente',
            'skins'   => SystemSkin::all()->map(fn($s) => $s->getCollectionData()),
        ]);
    }

    public function revertSystemSkin(Request $request)
    {
        $skin = SystemSkin::find($request->skin_id);

        if (!$skin || !$skin->is_default) {
            return response()->json(['success' => false, 'message' => 'Tema no válido']);
        }

        if (!$skin->custom_filename) {
            return response()->json(['success' => false, 'message' => 'Este tema no tiene un reemplazo activo']);
        }

        $overrideFilename = $skin->custom_filename;

        // Propagar a todos los tenants: volver al filename original
        $clients = Client::with('hostname.website')->get();
        foreach ($clients as $client) {
            try {
                $tenancy = app(Environment::class);
                $tenancy->tenant($client->hostname->website);
                DB::connection('tenant')->table('skins')
                    ->where('filename', $overrideFilename)
                    ->update(['filename' => $skin->filename]);
            } catch (\Exception $e) {
                // Continuar con el siguiente tenant si hay error
            }
        }

        // Eliminar físicamente el archivo override
        $fullPath = storage_path('app/public/skins/' . $overrideFilename);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        $skin->update(['custom_filename' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Tema "' . $skin->name . '" restaurado al original',
            'skins'   => SystemSkin::all()->map(fn($s) => $s->getCollectionData()),
        ]);
    }

    public function syncDefaultSkin(Request $request)
    {
        $skin = SystemSkin::find($request->skin_id);

        if (!$skin || !$skin->is_default) {
            return response()->json(['success' => false, 'message' => 'Solo se pueden sincronizar temas por defecto']);
        }

        $overridePath = $skin->custom_filename
            ? storage_path('app/public/skins/' . $skin->custom_filename)
            : null;

        // Forzar a todos los tenants a usar el archivo original
        $clients = Client::with('hostname.website')->get();
        foreach ($clients as $client) {
            try {
                $tenancy = app(Environment::class);
                $tenancy->tenant($client->hostname->website);
                DB::connection('tenant')->table('skins')
                    ->where('name', $skin->name)
                    ->update(['filename' => $skin->filename]);
            } catch (\Exception $e) {
                // Continuar con el siguiente tenant si hay error
            }
        }

        // Eliminar archivo override si existe
        if ($overridePath && file_exists($overridePath)) {
            @unlink($overridePath);
        }

        $skin->update(['custom_filename' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Tema "' . $skin->name . '" sincronizado al original en todos los tenants',
            'skins'   => SystemSkin::all()->map(fn($s) => $s->getCollectionData()),
        ]);
    }

    public function forceSystemSkin(Request $request)
    {
        $skin = SystemSkin::find($request->skin_id);

        if (!$skin) {
            return response()->json(['success' => false, 'message' => 'Tema no encontrado']);
        }

        $activeFilename = $skin->custom_filename ?? $skin->filename;

        $clients = Client::with('hostname.website')->get();
        $updated = 0;
        foreach ($clients as $client) {
            try {
                $tenancy = app(Environment::class);
                $tenancy->tenant($client->hostname->website);
                $tenantSkin = DB::connection('tenant')->table('skins')
                    ->where('filename', $activeFilename)
                    ->first();
                if ($tenantSkin) {
                    DB::connection('tenant')->table('configurations')
                        ->where('id', 1)
                        ->update(['skin_id' => $tenantSkin->id]);
                    $updated++;
                }
            } catch (\Exception $e) {
                // Continuar con el siguiente tenant si hay error
            }
        }

        SystemSkin::query()->update(['is_forced' => false]);
        $skin->update(['is_forced' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Tema "' . $skin->name . '" forzado en ' . $updated . ' empresa(s)',
            'skins'   => SystemSkin::all()->map(fn($s) => $s->getCollectionData()),
        ]);
    }

    public function setTenantDefaultSkin(Request $request)
    {
        $skin = SystemSkin::find($request->skin_id);

        if (!$skin) {
            return response()->json(['success' => false, 'message' => 'Tema no encontrado']);
        }

        SystemSkin::query()->update(['is_tenant_default' => false]);
        $skin->update(['is_tenant_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Tema por defecto actualizado a "' . $skin->name . '"',
            'skins'   => SystemSkin::all()->map(fn($s) => $s->getCollectionData()),
        ]);
    }

    public function toggleSkinVisibility(Request $request)
    {
        $skin = SystemSkin::find($request->skin_id);

        if (!$skin) {
            return response()->json(['success' => false, 'message' => 'Tema no encontrado']);
        }

        if ($skin->is_visible_to_clients && $skin->is_tenant_default) {
            return response()->json([
                'success' => false,
                'message' => "No puedes desactivar \"$skin->name\" porque es el tema predeterminado. Asigna otro tema como predeterminado antes de ocultarlo.",
            ]);
        }

        $skin->update(['is_visible_to_clients' => !$skin->is_visible_to_clients]);

        $status = $skin->is_visible_to_clients ? 'visible' : 'oculto';

        return response()->json([
            'success' => true,
            'message' => "Tema \"$skin->name\" ahora es $status para los clientes",
            'skins'   => SystemSkin::all()->map(fn($s) => $s->getCollectionData()),
        ]);
    }

    // ===== OpenAI provider =====
    // El reseller (superadmin) configura aqui la API key y el modelo. Los tenants
    // heredan estos valores via App\Models\System\Configuration en el bot.

    public function openAiConfig()
    {
        $c = Configuration::first();
        return [
            'openai_api_key' => $c->openai_api_key,
            'openai_model' => $c->openai_model ?: 'gpt-4o-mini',
        ];
    }

    public function storeOpenAiConfig(Request $request)
    {
        $request->validate([
            'openai_api_key' => 'nullable|string|max:255',
            'openai_model' => 'nullable|string|max:100',
        ]);
        $c = Configuration::first();
        $c->openai_api_key = $request->openai_api_key;
        $c->openai_model = $request->openai_model ?: 'gpt-4o-mini';
        $c->save();
        return ['success' => true, 'message' => 'Configuración de IA guardada'];
    }

    public function listOpenAiModels(Request $request)
    {
        $apiKey = $request->openai_api_key ?: Configuration::first()->openai_api_key;
        if (empty($apiKey)) {
            return ['success' => false, 'message' => 'Falta API Key.', 'models' => []];
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(15)
                ->get('https://api.openai.com/v1/models');

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'OpenAI respondio ' . $response->status() . ': ' . substr($response->body(), 0, 200),
                    'models' => [],
                ];
            }

            // Filtrar solo modelos de chat (gpt-*) y excluir variantes raras
            // (instruct/realtime/audio/embedding/tts/whisper/moderation/etc).
            $excludePatterns = ['instruct', 'realtime', 'audio', 'embedding', 'tts', 'whisper', 'moderation', 'transcribe', 'davinci', 'babbage'];
            $models = collect($response->json('data', []))
                ->pluck('id')
                ->filter(function ($id) use ($excludePatterns) {
                    if (!str_starts_with($id, 'gpt-')) return false;
                    foreach ($excludePatterns as $p) {
                        if (str_contains($id, $p)) return false;
                    }
                    return true;
                })
                ->sort()
                ->values()
                ->all();

            return ['success' => true, 'models' => $models];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage(), 'models' => []];
        }
    }

    public function testOpenAiConnection(Request $request)
    {
        $apiKey = $request->openai_api_key ?: Configuration::first()->openai_api_key;
        $model = $request->openai_model ?: (Configuration::first()->openai_model ?: 'gpt-4o-mini');

        if (empty($apiKey)) {
            return ['success' => false, 'message' => 'Falta API Key.'];
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(15)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [['role' => 'user', 'content' => 'ping']],
                    'max_tokens' => 5,
                ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'OpenAI respondió ' . $response->status() . ': ' . substr($response->body(), 0, 200),
                ];
            }

            return ['success' => true, 'message' => 'Conexión OK con modelo ' . $model];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
