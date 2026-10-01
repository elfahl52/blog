@extends('layouts.app')

@section('title', 'Manage '.$resource.' | Kian Blog')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-bold text-muted mb-1">Blog settings</p>
            <h1 class="h2 mb-0">{{ $resource === 'categories' ? 'Categories' : 'Tags' }}</h1>
        </div>
        <a class="btn btn-dark" href="{{ route('dashboard.'.$resource.'.create') }}"><i class="bi bi-plus-lg me-2" aria-hidden="true"></i>New {{ strtolower($singular) }}</a>
    </div>

    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('dashboard.posts.index') }}" class="btn btn-sm btn-outline-secondary">Articles</a>
        <a href="{{ route('dashboard.categories.index') }}" class="btn btn-sm {{ $resource === 'categories' ? 'btn-dark' : 'btn-outline-secondary' }}">Categories</a>
        <a href="{{ route('dashboard.tags.index') }}" class="btn btn-sm {{ $resource === 'tags' ? 'btn-dark' : 'btn-outline-secondary' }}">Tags</a>
    </div>

    <div class="surface table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Name</th><th>Slug</th><th>Articles</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->name }}</td>
                        <td class="text-muted">{{ $item->slug }}</td>
                        <td>{{ $item->posts_count }}</td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard.'.$resource.'.edit', $item) }}" aria-label="Edit {{ $item->name }}"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                            <form class="d-inline" action="{{ route('dashboard.'.$resource.'.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this {{ strtolower($singular) }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Delete {{ $item->name }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-5">No {{ strtolower($resource) }} yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $items->links('pagination::bootstrap-5') }}</div>
@endsection
