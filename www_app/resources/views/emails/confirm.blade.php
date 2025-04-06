<!DOCTYPE html>
<html>
<head>
    <title>Email Confirmation</title>
</head>
<body>
    <h1>Hello, {{ $name }}!</h1>
    <p>Thank you for registering. Please confirm your email using the link below:</p>
    <p><a href="{{ $verifyUrl }}">{{ $verifyUrl }}</a></p>
</body>
</html>