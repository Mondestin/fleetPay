@component('mail::message')
<div style="text-align: center; margin-bottom: 20px;">
    <img src="https://fleet-pay-front.vercel.app/images/fleetpay-bgt.png" alt="FleetPay Logo" style="max-width: 150px;">
</div>

# Bienvenue {{ $user->first_name }} {{ $user->last_name }}!

Votre compte a été créé avec succès sur notre plateforme de gestion de flotte.

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <p style="color: #000000; margin: 0;"><strong>Vos identifiants de connexion :</strong></p>
    <p style="color: #000000; margin: 5px 0;">Email : {{ $user->email }}</p>
    <p style="color: #000000; margin: 5px 0;">Mot de passe : {{ $password }}</p>
</div>

Vous pouvez maintenant accéder à votre tableau de bord pour :
- Gérer vos chauffeurs
- Suivre vos revenus
- Consulter vos rapports
- Gérer vos paramètres
- Gérer votre abonnement
- et bien plus encore...

@component('mail::button', ['url' => 'https://fleet-pay-front.vercel.app/', 'color' => 'primary'])
Accéder au Tableau de Bord
@endcomponent

<div style="margin-top: 20px; color: #666;">
    <p>Si vous avez des questions, n'hésitez pas à nous contacter au <br> <a href="mailto:support@phoenone.com">support@phoenone.com</a></p>
</div>

<div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
    <p style="color: #000000; margin: 0;">Cordialement,</p>
    <p style="color: #01631b; margin: 5px 0; font-weight: bold;">L'équipe {{ config('app.name') }}</p>
</div>
@endcomponent
