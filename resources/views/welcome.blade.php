<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Baju</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex items-center justify-center">

    <div class="text-center px-4">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Ruang Baju</h1>
        <p class="text-gray-500 text-lg mb-8">
            Fashion casual & minimalist untuk semua usia.
        </p>

        <a href="{{ route('home') }}"
           class="inline-block bg-brand-600 hover:bg-brand-700 text-white font-semibold text-lg px-10 py-3 rounded-full transition-colors duration-200 shadow-lg hover:shadow-xl">
            SHOP NOW
        </a>
    </div>

</body>
</html>
