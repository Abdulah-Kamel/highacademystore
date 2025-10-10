<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبك جاهز للاستلام - High Academy</title>
    <style>
        /* Base styles - supported by all email clients */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        /* Email container - responsive but compatible */
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        /* Info sections - use tables for better compatibility */
        .info-section {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }

        .order-info-cell {
            padding: 10px;
            vertical-align: top;
        }

        .info-card {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            text-align: center;
            margin-bottom: 10px;
        }

        /* Modern email clients get better responsive features */
        @media screen and (max-width: 600px) {
            .email-container {
                border-radius: 0;
                box-shadow: none;
            }

            .header {
                padding: 15px;
            }

            .content {
                padding: 15px;
            }

            .header h1 {
                font-size: 20px !important;
            }

            .info-card {
                margin-bottom: 15px;
            }
        }

        /* Gmail-specific fixes */
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            .info-card {
                -webkit-box-decoration-break: clone;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🎓 High Academy Store</h1>
            <p>طلبك جاهز للاستلام من الفرع!</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>مرحباً {{ $order->name ?? 'عزيزي العميل' }}</h2>

            <!-- Order Information - Hybrid table/div approach -->
            <table class="info-section"
                style="background-color: #f8f9fa; border-radius: 8px; margin: 20px 0; border-right: 4px solid #28a745;">
                <tr>
                    <td style="padding: 15px;">
                        <h3 style="color: #28a745; margin: 0 0 15px 0; font-size: 18px;">📦 معلومات الطلب</h3>

                        <table width="100%" style="margin-bottom: 15px;">
                            <tr>
                                <td width="48%" style="padding: 5px;">
                                    <div class="info-card">
                                        <strong>رقم الطلب</strong><br>
                                        <span
                                            style="color: #28a745; font-size: 18px; font-weight: bold;">#{{ $order->id }}</span>
                                    </div>
                                </td>
                                @if ($order->barcode)
                                    <td width="4%">&nbsp;</td>
                                    <td width="48%" style="padding: 5px;">
                                        <div class="info-card">
                                            <strong>الباركود</strong><br>
                                            <span
                                                style="color: #28a745; font-size: 18px; font-weight: bold;">{{ $order->barcode ?: 'سيتم إضافته قريباً' }}</span>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Custom Message -->
            @if (!empty($customMessage))
                <table class="info-section"
                    style="background-color: #e7f3ff; border: 1px solid #b8daff; border-radius: 8px; margin: 20px 0;">
                    <tr>
                        <td style="padding: 20px;">
                            <h3 style="color: #004085; margin: 0 0 15px 0; font-size: 18px;"> رسالة من High Academy Store</h3>
                            <p style="font-size: 16px; line-height: 1.6; color: #004085; margin: 0;">
                                {{ $customMessage }}</p>
                        </td>
                    </tr>
                </table>
            @endif

            <!-- Branch Information -->
            <table class="info-section"
                style="background-color: #e7f3ff; border: 1px solid #b8daff; border-radius: 8px; margin: 20px 0;">
                <tr>
                    <td style="padding: 20px;">
                        <h3 style="color: #004085; margin: 0 0 15px 0; font-size: 18px;"> معلومات الاستلام من الفرع
                        </h3>
                        <p style="margin: 5px 0;"><strong>📍 العنوان:</strong>
                            {{ $order->shipping->address ?? 'سيتم إرسال العنوان قريباً' }}</p>
                        <p style="margin: 5px 0;"><strong>📞 الهاتف:</strong>
                            {{ is_array($order->shipping->phones) ? implode(' - ', $order->shipping->phones) : $order->shipping->phones ?? 'سيتم إرسال رقم الهاتف قريباً' }}
                        </p>
                        <p style="margin: 5px 0;"><strong>⏰ ساعات العمل:</strong> من السبت إلى الخميس، 9 صباحاً - 6
                            مساءً</p>
                    </td>
                </tr>
            </table>

            <!-- Ready Status -->
            <table class="info-section"
                style="background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; margin: 20px 0;">
                <tr>
                    <td style="padding: 20px; text-align: center;">
                        <h3 style="color: #155724; margin: 0 0 10px 0; font-size: 20px;">✅ طلبك جاهز للاستلام!</h3>
                        <p style="color: #155724; line-height: 1.6; margin: 0; font-size: 16px;">
                            يمكنك زيارة الفرع في أي وقت خلال ساعات العمل لاستلام طلبك.
                        </p>
                    </td>
                </tr>
            </table>

            <p style="color: #6c757d; font-size: 14px; line-height: 1.6; margin-top: 20px;">
                يرجى إحضار هذا الإيميل أو رقم الطلب عند الاستلام من الفرع.<br>
                في حالة وجود أي استفسارات، لا تتردد في التواصل معنا.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #343a40; color: white; padding: 20px; text-align: center; font-size: 14px;">
            <p style="margin: 5px 0;"><strong>High Academy Store</strong></p>
            <p style="margin: 5px 0;">شكراً لثقتكم بنا 💚</p>
            <p style="margin: 5px 0;">© {{ date('Y') }} High Academy. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>

</html>
