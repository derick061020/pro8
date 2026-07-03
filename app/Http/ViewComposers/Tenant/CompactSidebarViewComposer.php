<?php

namespace App\Http\ViewComposers\Tenant;

use App\Models\Tenant\Configuration;
use App\Models\System\User;
use Symfony\Component\Process\Process;

class CompactSidebarViewComposer
{
    public function compose($view)
    {
    	$configuration = Configuration::first();
        // $set = (new \App\Http\Controllers\Tenant\ConfigurationController)->getSystemPhone();

        // Obtener el número de WhatsApp del sistema
        $systemUser = User::first();
        $whatsappNumber = $systemUser ? $systemUser->whatsapp_number : null;
        
        $view->show_ws = $configuration->enable_whatsapp;
        $view->phone_whatsapp = $whatsappNumber ?: $configuration->phone_whatsapp; // Usar whatsapp_number del sistema, si no existe usar el de configuración
        $view->vc_compact_sidebar = $configuration;
        
        //variables para validar si se debe mostrar notificacion del cambio de contraseña
        $view->vc_check_last_password_update = (object)[
            'enabled_remember_change_password' => $configuration->enabled_remember_change_password,
            'quantity_month_remember_change_password' => $configuration->quantity_month_remember_change_password,
        ];

        $process = new Process([$this->resolveGitBinary(), 'describe', '--tags', '--abbrev=0'], base_path());
        $process->run();

        $version = $process->isSuccessful() ? trim($process->getOutput()) : null;

        $view->vc_version = $version;


    }

    private function resolveGitBinary(): string
    {
        $windowsGitPaths = [
            'C:\\Program Files\\Git\\cmd\\git.exe',
            'C:\\Program Files\\Git\\mingw64\\bin\\git.exe',
        ];

        foreach ($windowsGitPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return 'git';
    }
}