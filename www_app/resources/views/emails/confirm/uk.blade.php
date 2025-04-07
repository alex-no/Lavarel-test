<!DOCTYPE html>
<html lang="uk">
<head>
    <title>Підтвердження електронної пошти</title>
</head>
<body>
    <h1>Привіт, {{ $name }}!</h1>
    <p>Дякуємо за реєстрацію. Будь ласка, підтвердіть свою електронну пошту за посиланням нижче:</p>
    <p><a href="{{ $verifyUrl }}">{{ $verifyUrl }}</a></p>
</body>
</html>