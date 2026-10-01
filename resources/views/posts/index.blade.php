<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #f3f1ee;
            --panel: rgba(255,255,255,0.78);
            --panel-strong: #ffffff;
            --text: #111111;
            --muted: #666666;
            --soft: #f0ece7;
            --border: rgba(17,17,17,0.08);
            --shadow: rgba(0,0,0,0.07);
        }

        body.dark-mode {
            --bg: #121212;
            --panel: rgba(25,25,25,0.92);
            --panel-strong: #1b1b1b;
            --text: #f3f3f3;
            --muted: #b6b6b6;
            --soft: #2a2a2a;
            --border: rgba(255,255,255,0.08);
            --shadow: rgba(0,0,0,0.24);
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: "Segoe UI", sans-serif;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .blog-shell {
            max-width: 1180px;
            margin: 36px auto;
            padding: 0 18px;
        }

        .topbar {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 14px 22px;
            margin-bottom: 28px;
            box-shadow: 0 12px 24px var(--shadow);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            color: var(--text);
        }

        .brand .bi {
            color: #bf4b3b;
            font-size: 1.1rem;
        }

        .primary-nav {
            gap: 22px;
            margin-left: auto;
            margin-right: 24px;
        }

        .primary-nav a {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--muted);
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.18s ease;
        }

        .primary-nav a:hover,
        .primary-nav a:focus-visible {
            color: var(--text);
        }

        .account-nav {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-right: 16px;
        }

        .account-nav a {
            color: var(--text);
            font-size: 0.84rem;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
        }

        .account-nav .account-name {
            color: var(--muted);
            font-size: 0.82rem;
        }

        .account-nav .nav-register {
            background: var(--text);
            border-radius: 999px;
            color: var(--bg);
            padding: 8px 12px;
        }

        .account-nav button {
            border: 0;
            background: transparent;
            color: var(--text);
            font-size: 0.84rem;
            font-weight: 600;
            padding: 0;
            white-space: nowrap;
        }

        .theme-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid var(--border);
            background: var(--panel-strong);
            color: var(--text);
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: background 0.18s ease, border-color 0.18s ease;
        }

        .theme-toggle:hover {
            border-color: var(--text);
        }

        .hero {
            background: linear-gradient(135deg, var(--panel-strong) 0%, rgba(255,255,255,0.2) 100%);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 34px 30px;
            margin-bottom: 28px;
            box-shadow: 0 12px 30px var(--shadow);
        }

        .hero h1 {
            font-size: clamp(2.1rem, 3vw, 3.6rem);
            font-weight: 800;
            letter-spacing: -0.06em;
            margin: 0 0 12px;
            color: var(--text);
        }

        .hero p {
            margin: 0;
            color: var(--muted);
            max-width: 720px;
            font-size: 1.05rem;
            line-height: 1.7;
        }

        .sidebar-card,
        .post-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: 0 12px 24px var(--shadow);
        }

        .post-card {
            padding: 24px 24px 18px;
            margin-bottom: 18px;
        }

        .post-image {
            display: block;
            width: 100%;
            max-width: 560px;
            max-height: 315px;
            aspect-ratio: 16 / 9;
            object-fit: cover;
            border-radius: 12px;
            margin-inline: auto;
            margin-bottom: 18px;
        }

        .post-card h2 {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            margin-bottom: 10px;
        }

        .post-card h2 a,
        .sidebar-link,
        .meta a {
            color: var(--text);
            text-decoration: none;
        }

        .meta {
            color: var(--muted);
            font-size: 0.82rem;
            margin-bottom: 12px;
        }

        .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .meta-item .bi,
        .sidebar-title .bi {
            color: #bf4b3b;
        }

        .post-excerpt {
            color: var(--text);
            line-height: 1.7;
            margin-bottom: 16px;
        }

        .tag {
            display: inline-block;
            background: var(--soft);
            color: var(--text);
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 8px;
            margin-bottom: 8px;
        }

        .sidebar-card {
            padding: 20px 18px;
            margin-bottom: 20px;
        }

        .sidebar-title {
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 16px;
            font-weight: 700;
        }

        .sidebar-link {
            display: block;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-link:last-child {
            border-bottom: none;
        }

        .count-badge {
            background: var(--soft);
            color: var(--text);
            border-radius: 999px;
            padding: 3px 8px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .empty-box {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 28px;
            text-align: center;
            color: var(--muted);
        }

        .pagination {
            margin: 0;
            gap: 6px;
        }

        .pagination .page-link {
            min-width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: 1px solid var(--border);
            color: var(--text);
            background: var(--panel-strong);
            padding: 8px 13px;
            transition: color 0.18s ease, background 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
        }

        .pagination a.page-link:hover {
            border-color: var(--text);
            color: var(--text);
            background: var(--soft);
        }

        .pagination a.page-link[rel="prev"]:hover {
            transform: translateX(-2px);
        }

        .pagination a.page-link[rel="next"]:hover {
            transform: translateX(2px);
        }

        .pagination .page-item.active .page-link {
            background: var(--text);
            border-color: var(--text);
            color: var(--bg);
        }

        .pagination .page-item.disabled .page-link {
            color: var(--muted);
            background: var(--soft);
            opacity: 0.58;
        }

        @media (max-width: 575.98px) {
            .topbar {
                padding: 12px 14px;
            }

            .account-nav {
                gap: 8px;
                margin-right: 8px;
            }

            .theme-toggle #themeLabel {
                display: none;
            }

            .pagination .page-link {
                min-width: 38px;
                height: 38px;
                padding: 6px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="blog-shell">
        <nav class="topbar d-flex align-items-center" aria-label="Main navigation">
            <a href="{{ route('posts.index') }}" class="brand text-decoration-none">
                <i class="bi bi-journal-richtext" aria-hidden="true"></i>
                <span>My Blog</span>
            </a>
            <div class="primary-nav d-none d-md-flex align-items-center">
                <a href="{{ route('posts.index') }}"><i class="bi bi-house-door" aria-hidden="true"></i>Home</a>
                <a href="#categories"><i class="bi bi-grid" aria-hidden="true"></i>Categories</a>
                <a href="#tags"><i class="bi bi-tags" aria-hidden="true"></i>Tags</a>
            </div>
            <div class="account-nav">
                @auth
                    @if (auth()->user()->isAdmin() || auth()->user()->role === \App\Role::Author)
                        <a href="{{ route('dashboard.posts.index') }}">Dashboard</a>
                    @endif
                    <span class="account-name d-none d-lg-inline">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit">Sign out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Sign in</a>
                    <a href="{{ route('register') }}" class="nav-register">Register</a>
                @endauth
            </div>
            <button type="button" class="theme-toggle" id="themeToggle">
                <i class="bi bi-moon-stars-fill" id="themeIcon" aria-hidden="true"></i>
                <span id="themeLabel">Dark mode</span>
            </button>
        </nav>

        <header class="hero">
            <h1>
                @isset($category)
                    {{ $category->name }}
                @elseif (isset($tag))
                    #{{ $tag->name }}
                @else
                    Ideas, notes, and stories worth reading.
                @endisset
            </h1>
            <p>
                @if (isset($category) || isset($tag))
                    Browse published articles in this collection.
                @else
                    A quiet, modern blog for sharing practical lessons, product thinking, and the small wins that make work better.
                @endif
            </p>
        </header>

        <div class="row g-4">
            <main class="col-lg-8">
                <form action="{{ route('posts.index') }}" method="GET" class="input-group mb-4">
                    <span class="input-group-text bg-white"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input type="search" name="q" class="form-control" value="{{ request('q') }}" placeholder="Search articles" aria-label="Search articles">
                    <button class="btn btn-dark" type="submit">Search</button>
                </form>

                @forelse ($posts as $post)
                    <article class="post-card">
                        @if ($post->images->isNotEmpty())
                            <a href="{{ route('posts.show', $post) }}" aria-label="Read {{ $post->title }}">
                                <img class="post-image" src="{{ url('/storage/' . $post->images->first()->path) }}" alt="{{ $post->images->first()->alt_text ?? $post->title }}" loading="lazy">
                            </a>
                        @endif
                        <h2>
                            <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                        </h2>

                        <div class="meta">
                            {{ $post->author?->name ?? 'Unknown author' }}
                            @if ($post->category)
                                <span>•</span>
                                <a href="{{ route('posts.category', $post->category) }}">{{ $post->category->name }}</a>
                            @endif
                            <span>•</span>
                            {{ $post->published_at?->diffForHumans() ?? 'Not published yet' }}
                            <span>•</span>
                            <span class="meta-item"><i class="bi bi-eye" aria-hidden="true"></i>{{ $post->views ?? 0 }} views</span>
                            <span>•</span>
                            <span class="meta-item"><i class="bi bi-chat-left-text" aria-hidden="true"></i>{{ $post->comments_count ?? 0 }} comments</span>
                        </div>

                        <p class="post-excerpt">
                            {{ $post->excerpt ?? Str::limit(strip_tags($post->body), 160) }}
                        </p>

                        @foreach ($post->tags as $tag)
                            <span class="tag">{{ $tag->name }}</span>
                        @endforeach
                    </article>
                @empty
                    <div class="empty-box">
                        لا توجد منشورات حالياً.
                    </div>
                @endforelse

                <div class="d-flex justify-content-center">
                    {{ $posts->links('pagination::bootstrap-5') }}
                </div>
            </main>

            <aside class="col-lg-4">
                <div class="sidebar-card" id="categories">
                    <div class="sidebar-title"><i class="bi bi-grid" aria-hidden="true"></i> Categories</div>
                    @foreach ($categories as $category)
                        <a href="{{ route('posts.category', $category) }}" class="sidebar-link d-flex justify-content-between align-items-center">
                            <span>{{ $category->name }}</span>
                            <span class="count-badge">{{ $category->posts_count }}</span>
                        </a>
                    @endforeach
                </div>

                <div class="sidebar-card" id="tags">
                    <div class="sidebar-title"><i class="bi bi-tags" aria-hidden="true"></i> Tags</div>
                    @foreach ($tags as $tag)
                        <a href="{{ route('posts.tag', $tag) }}" class="sidebar-link d-flex justify-content-between align-items-center">
                            <span>#{{ $tag->name }}</span>
                            <span class="count-badge">{{ $tag->posts_count }}</span>
                        </a>
                    @endforeach
                </div>
            </aside>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const body = document.body;
        const toggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const themeLabel = document.getElementById('themeLabel');
        const savedTheme = localStorage.getItem('theme');

        const setTheme = (isDark) => {
            body.classList.toggle('dark-mode', isDark);
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            themeIcon.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
            themeLabel.textContent = isDark ? 'Light mode' : 'Dark mode';
        };

        setTheme(savedTheme === 'dark');

        toggle.addEventListener('click', () => {
            setTheme(!body.classList.contains('dark-mode'));
        });
    </script>
</body>
</html>
