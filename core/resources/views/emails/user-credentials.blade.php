<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account Credentials</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Hello {{ $user->name }},</p>

    <p>Your admin account has been created. Use the credentials below to sign in:</p>

    <table cellpadding="8" cellspacing="0" style="border-collapse: collapse; margin: 16px 0;">
        <tr>
            <td style="font-weight: bold;">Email</td>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Password</td>
            <td>{{ $plainPassword }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Role</td>
            <td>Admin</td>
        </tr>
    </table>

    <p>
        <a href="{{ url('/admin/login') }}" style="display: inline-block; padding: 10px 16px; background: #696cff; color: #fff; text-decoration: none; border-radius: 6px;">
            Login to Admin
        </a>
    </p>

    <p style="color: #666; font-size: 14px;">Please change your password after your first login.</p>

    <p>Regards,<br>{{ config('app.name') }}</p>
</body>
</html>
