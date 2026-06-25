<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="page">
    <header class="topbar">
        <div>
            <h1>{{ $title ?? 'APK Apps' }}</h1>
            <p class="muted">Android APK sürüm ve kanal yönetimi</p>
        </div>
        @auth
            <nav class="nav">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.applications.index') }}">Uygulamalar</a>
                <a href="{{ route('admin.agents.index') }}">Agentlar</a>
                <form method="post" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="secondary" type="submit">Çıkış</button>
                </form>
            </nav>
        @endauth
    </header>

    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{ $slot }}
</main>
</body>
</html>
