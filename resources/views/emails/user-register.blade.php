<x-mail::message>
<div style="text-align: center; margin-bottom: 20px;">
    <img src="https://fleet-pay-front.vercel.app/images/fleetpay-bgt.png" alt="FleetPay Logo" style="max-width: 180px;">
</div>

# <span style="color: #22c55e;">Nouvelle Inscription</span>

Un nouvel utilisateur s'est inscrit sur votre plateforme.

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <p style="color: #000000; margin: 0;"><strong>Informations de l'utilisateur :</strong></p>
    <p style="color: #000000; margin: 5px 0;">Nom : {{ $user->first_name }} {{ $user->last_name }}</p>
    <p style="color: #000000; margin: 5px 0;">Email : {{ $user->email }}</p>
    <p style="color: #000000; margin: 5px 0;">Téléphone : {{ $user->phone_number }}</p>
    <p style="color: #000000; margin: 5px 0;">Date d'inscription : {{ $user->created_at->format('d/m/Y H:i') }}</p>
</div>

<x-mail::button :url="'https://fleet-pay-front.vercel.app/admin/users'" color="primary">
Voir le Profil
</x-mail::button>

<div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
    <p style="color: #000000; margin: 0;">Cordialement,</p>
    <p style="color: #22c55e; margin: 5px 0; font-weight: bold;">L'équipe {{ config('app.name') }}</p>
</div>
</x-mail::message>
