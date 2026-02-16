<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Deskdiskannak</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Pakai CDN Tailwind supaya ringan --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: linear-gradient(135deg,#0f766e,#134e4a);
            /* background: linear-gradient(135deg,#14b8a6,#0f766e); */

        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center text-white">

    <div class="text-center max-w-3xl px-6">

        {{-- Judul --}}
        <h1 class="text-4xl md:text-6xl font-bold mb-6">
            Sistem Informasi Perikanan & Peternakan
        </h1>

        {{-- Deskripsi --}}
        <p class="text-lg text-teal-100 mb-10">
            Platform pengelolaan data pelaporan sektor perikanan dan peternakan.
            Mendukung monitoring, rekapitulasi, dan pengambilan keputusan berbasis data.
        </p>

        {{-- Button --}}
        <a href="/admin"
           class="inline-block bg-amber-400 hover:bg-amber-500 text-black font-semibold px-8 py-4 rounded-xl shadow-lg transition duration-300">
            Masuk ke Dashboard
        </a>

        {{-- Footer kecil --}}
        <div class="mt-16 text-sm text-teal-200 opacity-80">
            © {{ date('Y') }} Deskdiskannak
        </div>

    </div>

</body>
</html>
