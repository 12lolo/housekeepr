<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welkom bij HouseKeepr</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f7fa;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f7fa; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 600;">
                                Welkom bij HouseKeepr
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">

                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Er is een HouseKeepr account voor u aangemaakt!
                                @if($hotel)
                                    U kunt nu uw hotel <strong>{{ $hotel->name }}</strong> beheren via ons platform.
                                @else
                                    U kunt nu hotels beheren via ons platform.
                                @endif
                            </p>

                            <p style="margin: 0 0 10px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Uw inloggegevens:
                            </p>

                            <table width="100%" cellpadding="15" cellspacing="0" style="background-color: #f8f9fa; border-radius: 6px; margin: 20px 0;">
                                <tr>
                                    <td style="font-size: 14px; color: #666666;">
                                        <strong>Email:</strong>
                                    </td>
                                    <td style="font-size: 14px; color: #333333; text-align: right;">
                                        {{ $owner->email }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 14px; color: #666666; border-top: 1px solid #e0e0e0; padding-top: 15px;">
                                        <strong>Wachtwoord:</strong>
                                    </td>
                                    <td style="font-size: 14px; color: #333333; text-align: right; border-top: 1px solid #e0e0e0; padding-top: 15px;">
                                        <code style="background-color: #ffffff; padding: 5px 10px; border-radius: 4px; border: 1px solid #e0e0e0; font-family: monospace;">{{ $tempPassword }}</code>
                                    </td>
                                </tr>
                            </table>

                            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
                                <p style="margin: 0; font-size: 14px; color: #856404;">
                                    <strong>Belangrijk:</strong> We raden u sterk aan om dit wachtwoord te wijzigen na uw eerste login voor de veiligheid van uw account.
                                </p>
                            </div>

                            <p style="margin: 30px 0 20px; text-align: center;">
                                <a href="{{ url('/login') }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-weight: 600; font-size: 16px;">
                                    Inloggen op HouseKeepr
                                </a>
                            </p>

                            <p style="margin: 30px 0 0; font-size: 14px; color: #666666; line-height: 1.6;">
                                Met vriendelijke groet,<br>
                                <strong>Het HouseKeepr Team</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-radius: 0 0 8px 8px; border-top: 1px solid #e0e0e0;">
                            <p style="margin: 0; font-size: 12px; color: #999999;">
                                Dit is een geautomatiseerd bericht van HouseKeepr<br>
                                Heeft u vragen? Neem contact op met de beheerder.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
