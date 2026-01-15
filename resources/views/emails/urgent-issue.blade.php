<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>URGENT - Probleem met Kamer</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f7fa;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f7fa; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-top: 4px solid #dc3545;">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #dc3545; padding: 40px 30px; text-align: center; border-radius: 4px 4px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 600;">
                                ⚠️ URGENT: Probleem Gemeld
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <div style="background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 0 0 30px; border-radius: 4px;">
                                <p style="margin: 0; font-size: 16px; color: #721c24; font-weight: 600;">
                                    Er is een urgent probleem gemeld voor kamer {{ $issue->room->room_number }}
                                </p>
                            </div>

                            <h2 style="margin: 0 0 20px; font-size: 20px; color: #333333;">
                                Probleem Details
                            </h2>

                            <table width="100%" cellpadding="12" cellspacing="0" style="background-color: #f8f9fa; border-radius: 6px; margin: 20px 0;">
                                <tr>
                                    <td style="font-size: 14px; color: #666666; width: 40%;">
                                        <strong>Kamer:</strong>
                                    </td>
                                    <td style="font-size: 16px; color: #333333; font-weight: 600;">
                                        {{ $issue->room->room_number }}
                                        @if($issue->room->room_type)
                                            <span style="font-size: 14px; color: #666666; font-weight: normal;">({{ $issue->room->room_type }})</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 14px; color: #666666; border-top: 1px solid #e0e0e0; padding-top: 12px;">
                                        <strong>Impact:</strong>
                                    </td>
                                    <td style="font-size: 14px; border-top: 1px solid #e0e0e0; padding-top: 12px;">
                                        @if($issue->impact === 'kan_niet_gebruikt')
                                            <span style="background-color: #dc3545; color: #ffffff; padding: 4px 12px; border-radius: 12px; font-weight: 600; font-size: 12px;">
                                                KAMER GEBLOKKEERD
                                            </span>
                                        @elseif($issue->impact === 'graag_snel')
                                            <span style="background-color: #ffc107; color: #000000; padding: 4px 12px; border-radius: 12px; font-weight: 600; font-size: 12px;">
                                                GRAAG SNEL
                                            </span>
                                        @else
                                            <span style="background-color: #6c757d; color: #ffffff; padding: 4px 12px; border-radius: 12px; font-weight: 600; font-size: 12px;">
                                                GEEN HAAST
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 14px; color: #666666; border-top: 1px solid #e0e0e0; padding-top: 12px;">
                                        <strong>Gemeld door:</strong>
                                    </td>
                                    <td style="font-size: 14px; color: #333333; border-top: 1px solid #e0e0e0; padding-top: 12px;">
                                        {{ $issue->reportedBy?->name ?? 'Systeem' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 14px; color: #666666; border-top: 1px solid #e0e0e0; padding-top: 12px;">
                                        <strong>Tijdstip:</strong>
                                    </td>
                                    <td style="font-size: 14px; color: #333333; border-top: 1px solid #e0e0e0; padding-top: 12px;">
                                        {{ $issue->created_at->format('d-m-Y H:i') }}
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin: 30px 0 15px; font-size: 18px; color: #333333;">
                                Beschrijving
                            </h3>

                            <div style="background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 6px; padding: 20px; margin: 0 0 30px;">
                                <p style="margin: 0; font-size: 15px; color: #333333; line-height: 1.6; white-space: pre-wrap;">{{ $issue->note }}</p>
                            </div>

                            @if($booking)
                            <div style="background-color: #d1ecf1; border-left: 4px solid #0c5460; padding: 15px; margin: 20px 0; border-radius: 4px;">
                                <p style="margin: 0 0 10px; font-size: 14px; color: #0c5460; font-weight: 600;">
                                    ⚠️ Gerelateerde Boeking
                                </p>
                                <p style="margin: 0; font-size: 14px; color: #0c5460;">
                                    Check-in: <strong>{{ $booking->check_in_datetime->format('d-m-Y H:i') }}</strong>
                                </p>
                            </div>
                            @endif

                            @if($issue->impact === 'kan_niet_gebruikt')
                            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
                                <p style="margin: 0; font-size: 14px; color: #856404;">
                                    <strong>Let op:</strong> Deze kamer is gemarkeerd als "Kan Niet Gebruikt Worden". De automatische planning is bijgewerkt en deze kamer wordt geblokkeerd tot het probleem is opgelost.
                                </p>
                            </div>
                            @endif

                            <p style="margin: 30px 0 20px; text-align: center;">
                                <a href="{{ url('/owner/issues/' . $issue->id) }}" style="display: inline-block; background-color: #dc3545; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-weight: 600; font-size: 16px;">
                                    Bekijk Probleem in HouseKeepr
                                </a>
                            </p>

                            <p style="margin: 30px 0 0; font-size: 14px; color: #666666; line-height: 1.6;">
                                Dit probleem vereist uw directe aandacht.<br>
                                <strong>Het HouseKeepr Team</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-radius: 0 0 8px 8px; border-top: 1px solid #e0e0e0;">
                            <p style="margin: 0; font-size: 12px; color: #999999;">
                                Dit is een geautomatiseerd urgent bericht van HouseKeepr<br>
                                Log in op het platform om actie te ondernemen.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
