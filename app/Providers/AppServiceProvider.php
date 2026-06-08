<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Memaksa Laravel menggunakan HTTPS
        if (config('app.env') !== 'local' || request()->server('HTTP_X_FORWARDED_PROTO') == 'https') {
            URL::forceScheme('https');
        }

        // Custom tampilan email verifikasi
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verifikasi Email — DriveEase')
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Klik tombol di bawah untuk memverifikasi alamat email Anda.')
                ->action('Verifikasi Email', $url)
                ->line('Jika Anda tidak mendaftar di DriveEase, abaikan email ini.')
                ->salutation('Salam, Tim DriveEase');
        });
    }
}