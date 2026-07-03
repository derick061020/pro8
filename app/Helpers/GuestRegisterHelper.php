<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use App\Mail\VerifyGuestRegisterMail;
use App\Models\System\Configuration;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Encryption\DecryptException;
use Exception;
use Throwable;

class GuestRegisterHelper
{

    /**
     *
     * @param  int $id
     * @param  string $email
     * @param  string $client_id
     * @return array
     */
    public function sendEmail($id, $email, $client_id, $payment_uuid = null)
    {
        try {
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Email inválido o vacío: " . var_export($email, true));
            }
            $signed_url = $this->generateSignedUrl($id, $email, $client_id);
            $payment_url = $payment_uuid ? route('payment.public.show', ['uuid' => $payment_uuid]) : null;

            $class = new VerifyGuestRegisterMail($signed_url, $payment_url);

            $config = Configuration::first();
            
            Config::set('mail.host', $config->mail_host);
            Config::set('mail.port', $config->mail_port);
            Config::set('mail.username', $config->mail_username);
            Config::set('mail.password', $config->mail_password);
            Config::set('mail.encryption', $config->mail_encryption);

            if (is_string($config->mail_username) && $config->mail_username !== '') {
                $class->setUsernmae($config->mail_username);
            }

            Mail::to($email)->send($class);

            return [
                'success' => true,
                'message' => 'Correo reenviado correctamente.'
            ];
        } catch(Throwable $e) {
            $this->writeErrorLog($e, 'Ocurrió un error al enviar el email de verificación (creación de cuenta invitado)');
            
            return [
                'success' => false,
                'message' => 'Ocurrió un error al enviar el correo.'
            ];
        }
    }

    /**
     *
     * @param  $exception
     * @param  string $message
     * @return void
     */
    public function writeErrorLog($exception, $message = null)
    {
        Log::error(($message ? "{$message} - " : '') . "Line: {$exception->getLine()} - Message: {$exception->getMessage()} - File: {$exception->getFile()}");
    }

    /**
     *
     * @param  string $id
     * @param  string $email
     * @param  string $client_id
     * @return string
     */
    public function generateSignedUrl($id, $email, $client_id)
    {
        return URL::temporarySignedRoute(
            'guest-register.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 120)),
            [
                'id' => $id,
                'hash' => sha1($email),
                'client_id' => $client_id
            ],
            true
        );
    }

    /**
     *
     * @param  string $value
     * @return string
     */
    public function encryptValue($value)
    {
        return Crypt::encryptString($value);
    }

    /**
     *
     * @param  string $value
     * @return string
     */
    public function decryptValue($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch(DecryptException $e) {
            $this->writeErrorLog($e, 'Ocurrió un error al descifrar valor.');
            return null;
        }
    }
}