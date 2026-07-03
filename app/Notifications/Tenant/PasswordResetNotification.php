<?php

namespace App\Notifications\Tenant;

use Illuminate\Support\Facades\Log;
use App\Models\Tenant\Company;
use Hyn\Tenancy\Contracts\CurrentHostname;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetNotification extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        $query = http_build_query([
            'email' => $notifiable->getEmailForPasswordReset(),
        ], '', '&', PHP_QUERY_RFC3986);

        $hostname = app(CurrentHostname::class);
        if ($hostname && app()->bound('request')) {
            $scheme = request()->getScheme();
            $url = sprintf('%s://%s/password/reset/%s?%s', $scheme, $hostname->fqdn, $this->token, $query);
        } else {
            $url = url('password/reset/' . $this->token) . '?' . $query;
        }

        if (!app()->environment('production')) {
            Log::info('Password reset URL (testing)', [
                'email' => $notifiable->getEmailForPasswordReset(),
                'url' => $url,
            ]);
        }

        $company = Company::first();

        $logoPath = $this->resolveLogoPath($company);
        $logoSrc = $this->resolveLogoSource($logoPath);

        return (new MailMessage)
            ->subject('Recuperar contraseña')
            ->view('tenant.mails.password_reset', [
                'url' => $url,
                'company' => $company,
                'logoPath' => $logoPath,
                'logoSrc' => $logoSrc,
            ]);
    }

    protected function resolveLogoPath(?Company $company): ?string
    {
        if ($company && !empty($company->logo)) {
            $logoPath = storage_path('app/public/uploads/logos/' . $company->logo);
            if (file_exists($logoPath)) {
                return $logoPath;
            }
        }

        $defaultLogoPath = public_path('logo/logo.png');
        if (file_exists($defaultLogoPath)) {
            return $defaultLogoPath;
        }

        return null;
    }

    protected function resolveLogoSource(?string $logoPath): ?string
    {
        if (empty($logoPath) || !file_exists($logoPath)) {
            return asset('logo/logo.png');
        }

        $mimeType = mime_content_type($logoPath) ?: 'image/png';

        return 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($logoPath));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
