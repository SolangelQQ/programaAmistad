<?php
// 1. CREAR: resources/views/emails/reset-password.blade.php
?>
<x-mail::message>
# Recuperar Contraseña - Best Buddies Bolivia

Hola,

Recibiste este correo porque solicitaste recuperar tu contraseña para el **Programa Amistad**.

<x-mail::button :url="$url" color="primary">
Restablecer Contraseña
</x-mail::button>

Este enlace expirará en {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutos.

Si no solicitaste este cambio, puedes ignorar este correo.

Saludos,<br>
{{ config('app.name') }}<br>
**Programa Amistad**
</x-mail::message>