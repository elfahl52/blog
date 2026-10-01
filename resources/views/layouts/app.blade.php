<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kian Blog')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { color-scheme: light; --ink: #202421; --muted: #6a716d; --paper: #f4f5f1; --line: #dfe3dd; --accent: #b64c3b; --bg: #f3f1ee; --panel: rgba(255,255,255,.88); --panel-strong: #fff; --text: #202421; --soft: #ecefeb; --border: #dfe3dd; --shadow: rgba(0,0,0,.07); }
        body.dark-mode { color-scheme: dark; --ink: #f3f3f3; --muted: #b6b6b6; --paper: #121212; --line: rgba(255,255,255,.12); --bg: #121212; --panel: #191919; --panel-strong: #1b1b1b; --text: #f3f3f3; --soft: #2a2a2a; --border: rgba(255,255,255,.12); --shadow: rgba(0,0,0,.24); }
        body { min-height: 100vh; background: var(--bg); color: var(--text); font-family: "Segoe UI", sans-serif; transition: background .2s ease, color .2s ease; }
        .site-nav { background: var(--panel-strong); border-bottom: 1px solid var(--border); }
        .site-brand { color: var(--text); font-size: 1.1rem; font-weight: 750; text-decoration: none; white-space: nowrap; }
        .site-brand i { color: var(--accent); }
        .site-nav a:not(.site-brand) { color: var(--muted); text-decoration: none; font-weight: 600; white-space: nowrap; }
        .site-links, .site-account { display: flex; align-items: center; gap: 16px; }
        .site-account { gap: 12px; }
        .site-account form { margin: 0; }
        .theme-toggle { display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--border); border-radius: 999px; padding: 7px 11px; background: var(--panel-strong); color: var(--text); font-size: .82rem; font-weight: 600; white-space: nowrap; }
        .flash-toast {
            position: fixed;
            top: 18px;
            right: 18px;
            z-index: 1100;
            width: min(420px, calc(100vw - 30px));
            padding: 0.9rem 1rem;
            border-radius: 12px;
            box-shadow: 0 16px 28px rgba(0,0,0,.12);
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-weight: 600;
        }
        .flash-toast.success {
            background: #eafaf1;
            color: #0f5132;
            border-color: #a7f3d0;
        }
        .flash-toast.error {
            background: #fff1f2;
            color: #842029;
            border-color: #f5c2c7;
        }
        .page-wrap { width: min(1100px, calc(100% - 32px)); margin: 36px auto; }
        .surface { background: var(--panel-strong); color: var(--text); border: 1px solid var(--border); border-radius: 8px; box-shadow: 0 12px 24px var(--shadow); }
        .text-muted { color: var(--muted) !important; }
        .btn-dark { background: #202421; border-color: #202421; }
        body.dark-mode .btn-dark { background: #e5e9e5; border-color: #e5e9e5; color: #202421; }
        .form-control, .form-select, .input-group-text { border-color: var(--border); background-color: var(--panel-strong); color: var(--text); }
        .form-control::placeholder { color: var(--muted); }
        .form-control:focus, .form-select:focus { border-color: #87918a; box-shadow: 0 0 0 .2rem rgba(91, 112, 98, .14); }
        body.dark-mode .table { --bs-table-bg: var(--panel-strong); --bs-table-color: var(--text); --bs-table-border-color: var(--border); }
        body.dark-mode .table thead { color: var(--muted); }
        body.dark-mode .badge.text-bg-light { background: var(--soft) !important; color: var(--text) !important; }
        @media (max-width: 767.98px) { .site-links { display: none; } .page-wrap { margin-top: 24px; } }
        @media (max-width: 575.98px) { .theme-toggle #themeLabel { display: none; } .site-account { gap: 8px; } }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="site-nav">
        <div class="container d-flex align-items-center justify-content-between py-3 gap-3">
            <a class="site-brand" href="{{ route('posts.index') }}"><i class="bi bi-journal-richtext me-2" aria-hidden="true"></i>Kian Blog</a>
            <div class="site-links">
                <a href="{{ route('posts.index') }}"><i class="bi bi-house-door me-1" aria-hidden="true"></i>Home</a>
                <a href="{{ route('posts.index') }}#categories"><i class="bi bi-grid me-1" aria-hidden="true"></i>Categories</a>
                <a href="{{ route('posts.index') }}#tags"><i class="bi bi-tags me-1" aria-hidden="true"></i>Tags</a>
            </div>
            <div class="site-account">
                @auth
                    @if (auth()->user()->isAdmin() || auth()->user()->role === \App\Role::Author)
                        <a href="{{ route('dashboard.posts.index') }}">Dashboard</a>
                    @endif
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('dashboard.categories.index') }}">Categories</a>
                        <a href="{{ route('dashboard.tags.index') }}">Tags</a>
                    @endif
                    <span class="small text-muted d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary" type="submit">Sign out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Sign in</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-dark">Create account</a>
                @endauth
                <button type="button" class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">
                    <i class="bi bi-moon-stars-fill" id="themeIcon" aria-hidden="true"></i>
                    <span id="themeLabel">Dark mode</span>
                </button>
            </div>
        </div>
    </nav>

    <main class="page-wrap">
        @if (session('success'))
            <div class="flash-toast success alert fade show" role="status">
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="flash-toast error alert fade show" role="alert">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @yield('content')
    </main>
    <script>
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const themeLabel = document.getElementById('themeLabel');
        const savedTheme = localStorage.getItem('theme');

        const applyTheme = (isDark) => {
            document.body.classList.toggle('dark-mode', isDark);
            themeIcon.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
            themeLabel.textContent = isDark ? 'Light mode' : 'Dark mode';
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        };

        applyTheme(savedTheme === 'dark');
        themeToggle.addEventListener('click', () => applyTheme(!document.body.classList.contains('dark-mode')));

    </script>
    @vite('resources/js/app.js')
    @stack('scripts')
</body>
</html>
