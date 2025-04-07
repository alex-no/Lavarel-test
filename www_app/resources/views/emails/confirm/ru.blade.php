<!DOCTYPE html>
<html lang="ru">   
<head>
    <title>Подтверждение Email</title>
</head>
<body>
    <h1>Здравствуйте, {{ $name }}!</h1>
    <p>Спасибо за регистрацию. Пожалуйста, подтвердите ваш email, используя ссылку ниже:</p>
    <p><a href="{{ $verifyUrl }}">{{ $verifyUrl }}</a></p>
</body>
</html>