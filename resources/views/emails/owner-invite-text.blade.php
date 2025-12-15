Welkom bij HouseKeepr
=====================
@if($owner->name)

Beste {{ $owner->name }},
@endif

Er is een HouseKeepr account voor u aangemaakt!
@if($hotel)
U kunt nu uw hotel {{ $hotel->name }} beheren via ons platform.
@else
U kunt nu hotels beheren via ons platform.
@endif

Uw inloggegevens:

Email: {{ $owner->email }}
Wachtwoord: {{ $tempPassword }}

BELANGRIJK: We raden u sterk aan om dit wachtwoord te wijzigen na uw eerste login voor de veiligheid van uw account.

Inloggen op HouseKeepr:
{{ url('/login') }}

Met vriendelijke groet,
Het HouseKeepr Team

---
Dit is een geautomatiseerd bericht van HouseKeepr
Heeft u vragen? Neem contact op met de beheerder.

