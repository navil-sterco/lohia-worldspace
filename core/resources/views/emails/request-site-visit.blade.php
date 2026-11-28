<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Site Visit Request</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Hello {{ $siteVisit->first_name }},</p>

    <p>Thank you for requesting a site visit. Our representative will get back to you shortly.</p>

    <p>Your preferred visit date is {{ $siteVisit->udf_6->format('d M Y') }}.</p>

    <p>Please find the Lohia Worldspace brochure attached to this email.</p>

    <p>Regards,<br>{{ config('app.name') }}</p>
</body>
</html>
