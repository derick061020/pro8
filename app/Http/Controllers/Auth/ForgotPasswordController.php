<?php

namespace App\Http\Controllers\Auth;

use App\Models\Tenant\Company;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Contracts\Auth\PasswordBroker;
use App\Models\System\Configuration as SystemConfiguration;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails {
        sendResetLinkEmail as protected sendResetLinkEmailFromTrait;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        $config = SystemConfiguration::first();
        if (! $config->use_login_global) {
            $config = Configuration::first();
        }
        $useLoginGlobal = $config->use_login_global;
        $company = Company::first();
        $login = $config->login;
        $loginBgColor = $config->login_bg_color ?? '#ffffff';
        return view('tenant.auth.passwords.email', compact('company', 'login', 'useLoginGlobal', 'loginBgColor'));
    }

    protected function broker(): PasswordBroker
    {
        return Password::broker('users');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        Configuration::setConfigSmtpMail();
        Config::set('mail.driver', 'smtp');

        // Refrescar mailer para usar nuevos datos SMTP
        $this->refreshMailerTransport();

        if (empty(config('mail.host')) || empty(config('mail.port')) || empty(config('mail.username')) || empty(config('mail.password'))) {
            return back()
                ->withErrors(['email' => 'No se pudo enviar el correo de recuperación. Configure el SMTP en el panel administrador/reseller.'])
                ->withInput($request->only('email'));
        }

        // Evitar usar el remitente por defecto (hello@example.com) cuando ya hay SMTP configurado.
        if (config('mail.from.address') === 'hello@example.com' && !empty(config('mail.username'))) {
            Config::set('mail.from.address', config('mail.username'));
        }

        try {
            return $this->sendResetLinkEmailFromTrait($request);
        } catch (Throwable $e) {
            Log::error('Error enviando correo de recuperacion', [
                'email' => $request->email,
                'message' => $e->getMessage(),
            ]);
        
            return back()
                ->withErrors(['email' => 'No se pudo enviar el correo de recuperación. Verifique host, puerto, usuario y App Password SMTP.'])
                ->withInput($request->only('email'));

        }
    }

    protected function refreshMailerTransport(): void
    {
        try {
            $mailManager = app('mail.manager');
            if (method_exists($mailManager, 'forgetMailers')) {
                $mailManager->forgetMailers();
            }
            Mail::purge();
        } catch (Throwable $e) {
            Log::warning('No se pudo refrescar el transport SMTP en runtime', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
