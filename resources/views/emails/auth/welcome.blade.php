@component('mail::message')

Hola {{ $user->name }}

Su registro ha sido un éxito. Bienvenido a {{ config('app.name') }}.

Gracias,

Equipo {{ config('app.name') }}
@endcomponent