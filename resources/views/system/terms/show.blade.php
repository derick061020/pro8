<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Términos y Condiciones</title>
    <style>
        :root { color-scheme: light; }
        body {
            margin: 0;
            background: #f4f5f7;
            color: #2c2c2c;
            font-family: "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
        }
        .terms-wrapper {
            max-width: 820px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
            padding: 48px 56px;
        }
        .terms-wrapper h1 {
            font-size: 1.6rem;
            margin: 0 0 24px;
        }
        .terms-content img { max-width: 100%; height: auto; }
        .terms-content table { max-width: 100%; }
        @media (max-width: 640px) {
            .terms-wrapper { margin: 0; border-radius: 0; padding: 28px 20px; }
        }
    </style>
</head>
<body>
    <main class="terms-wrapper">
        <h1>Términos y Condiciones</h1>
        <div class="terms-content">
            {!! $content !!}
        </div>
    </main>
</body>
</html>
