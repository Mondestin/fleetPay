<x-mail::message>
<div style="text-align: center; margin-bottom: 20px;">
    <img src="https://fleet-pay-front.vercel.app/images/fleetpay-bgt.png" alt="FleetPay Logo" style="max-width: 180px;">
</div>

# <span style="color: #22c55e;">Nouveau Message de Contact</span>

Vous avez reçu un nouveau message via le formulaire de contact.

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <p style="color: #000000; margin: 0;"><strong>Détails du message :</strong></p>
    <p style="color: #000000; margin: 5px 0;">Nom : {{ $data['name'] }}</p>
    <p style="color: #000000; margin: 5px 0;">Email : {{ $data['email'] }}</p>
    <p style="color: #000000; margin: 5px 0;">Téléphone : {{ $data['phone'] }}</p>
    <p style="color: #000000; margin: 5px 0;">Entreprise : {{ $data['company'] }}</p>
</div>

<div style="background-color: #ffffff; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #eee;">
    <p style="color: #000000; margin: 0;"><strong>Message :</strong></p>
    <p style="color: #000000; margin: 10px 0; line-height: 1.5;">{{ $data['message'] }}</p>
</div>

<x-mail::button :url="'https://fleet-pay-front.vercel.app/admin/contacts'" color="primary">
Voir dans l'Administration
</x-mail::button>

<div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
    <p style="color: #000000; margin: 0;">Cordialement,</p>
    <p style="color: #000000; margin: 5px 0; font-weight: bold;">L'équipe {{ config('app.name') }}</p>
</div>
</x-mail::message>
