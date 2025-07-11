<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;  // ← AGREGAR ESTA LÍNEA
use Illuminate\Notifications\Messages\MailMessage;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
{
    // Personalizar SOLO el mensaje del email en español
    ResetPassword::toMailUsing(function (object $notifiable, string $token) {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('🔐 Recuperar Contraseña - Best Buddies Bolivia')
            ->greeting('¡Hola!')
            ->line('Recibiste este correo porque solicitaste recuperar tu contraseña para el **Programa Amistad**.')
            ->action('Restablecer Contraseña', $url)
            ->line('Este enlace expirará en ' . config('auth.passwords.users.expire') . ' minutos.')
            ->line('Si no solicitaste este cambio, puedes ignorar este correo.')
            // ->line('') // línea vacía
            ->line('Si tienes problemas haciendo clic en el botón "Restablecer Contraseña", copia y pega esta URL en tu navegador:')
            ->line($url)
            ->salutation('Saludos,  
                                    Best Buddies Bolivia  
                                    **Programa Amistad**');
    });
}
    
}