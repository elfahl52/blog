@extends('layouts.app')

@section('title', 'Manage articles | Kian Blog')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-bold text-muted mb-1">Publishing</p>
            <h1 class="h2 mb-0">Articles</h1>
        </div>
        <a class="btn btn-dark" href="{{ route('dashboard.posts.create') }}"><i class="bi bi-plus-lg me-2" aria-hidden="true"></i>New article</a>
    </div>

    <div class="surface table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Updated</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr>
                        <td class="fw-semibold">{{ $post->title }}</td>
                        <td>{{ $post->category?->name ?? 'Uncategorized' }}</td>
                        <td>
                            @if ($post->published_at?->lte(now()))
                                <span class="badge text-bg-success">Published</span>
                            @elseif ($post->published_at?->isFuture())
                                <span class="badge text-bg-info">Scheduled</span>
                            @else
                                <span class="badge text-bg-secondary">Draft</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $post->updated_at->diffForHumans() }}</td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard.posts.edit', $post) }}" aria-label="Edit {{ $post->title }}"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                            <form class="d-inline" action="{{ route('dashboard.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this article?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Delete {{ $post->title }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No articles yet. Create the first one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $posts->links('pagination::bootstrap-5') }}</div>
@endsection
