@extends('layouts.app')

@section('title', $post->title.' | Kian Blog')

@push('styles')
    <style>
        .post-detail-image {
            display: block;
            width: 100%;
            max-width: 560px;
            max-height: 315px;
            object-fit: contain;
            margin-inline: auto;
        }
    </style>
@endpush

@section('content')
    <a href="{{ url()->previous() === url()->current() ? route('posts.index') : url()->previous() }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Back to articles
    </a>

    <article class="surface p-4 p-lg-5 mt-3 mb-4">
        @if ($post->category)
            <a class="small text-uppercase fw-semibold text-decoration-none" href="{{ route('posts.category', $post->category) }}">{{ $post->category->name }}</a>
        @endif
        <h1 class="display-5 fw-bold mt-2">{{ $post->title }}</h1>
        <div class="d-flex flex-wrap gap-3 text-muted small mb-4">
            <span><i class="bi bi-person me-1" aria-hidden="true"></i>{{ $post->author?->name ?? 'Unknown author' }}</span>
            <span><i class="bi bi-calendar3 me-1" aria-hidden="true"></i>{{ $post->published_at?->format('M j, Y') }}</span>
            <span><i class="bi bi-eye me-1" aria-hidden="true"></i>{{ $post->views }} views</span>
            <span><i class="bi bi-chat-left-text me-1" aria-hidden="true"></i>{{ $post->comments->count() }} comments</span>
        </div>

        @if ($post->excerpt)
            <p class="fs-5 text-muted border-start border-3 ps-3 mb-4">{{ $post->excerpt }}</p>
        @endif

        @foreach ($post->images as $image)
            <img class="post-detail-image rounded mb-4" src="{{ url('/storage/' . $image->path) }}" alt="{{ $image->alt_text ?? $post->title }}">
        @endforeach

        <div class="post-body fs-5" style="line-height: 1.85; white-space: pre-line">{{ $post->body }}</div>

        @if ($post->tags->isNotEmpty())
            <div class="d-flex flex-wrap gap-2 mt-4">
                @foreach ($post->tags as $tag)
                    <a class="badge text-bg-light border text-decoration-none" href="{{ route('posts.tag', $tag) }}">#{{ $tag->name }}</a>
                @endforeach
            </div>
        @endif
    </article>

    <section class="surface p-4 mb-4" id="comments">
        <h2 class="h4 mb-1">Comments</h2>
        <p class="text-muted small mb-4">{{ $post->comments->count() }} responses</p>

        <form action="{{ route('posts.comments.store', $post) }}" method="POST" class="mb-4">
            @csrf
            <label for="comment-body" class="form-label">Leave a comment</label>
            <textarea id="comment-body" name="body" rows="4" maxlength="2000" required class="form-control mb-3" placeholder="Add to the conversation">{{ old('body') }}</textarea>
            <button type="submit" class="btn btn-dark"><i class="bi bi-send me-2" aria-hidden="true"></i>Post comment</button>
        </form>

        <div class="d-grid gap-3">
            @forelse ($post->comments as $comment)
                <article class="border-top pt-3">
                    <div class="d-flex justify-content-between gap-3 mb-2">
                        <div>
                            <strong>{{ $comment->user?->name ?? 'Guest' }}</strong>
                            <time class="small text-muted d-block">{{ $comment->created_at->diffForHumans() }}</time>
                        </div>

                        @auth
                            @if (auth()->user()->isAdmin() || auth()->id() === $comment->user_id)
                                <form action="{{ route('posts.comments.destroy', ['post' => $post, 'comment' => $comment]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التعليق؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                    <p class="mb-0" style="white-space: pre-line">{{ $comment->body }}</p>
                </article>
            @empty
                <p class="text-muted mb-0">No comments yet.</p>
            @endforelse
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="mb-5">
            <h2 class="h4 mb-3">Related articles</h2>
            <div class="row g-3">
                @foreach ($related as $relatedPost)
                    <div class="col-md-4">
                        <a class="surface d-block h-100 p-3 text-decoration-none text-reset" href="{{ route('posts.show', $relatedPost) }}">
                            <h3 class="h6">{{ $relatedPost->title }}</h3>
                            <span class="small text-muted">{{ $relatedPost->comments_count }} comments</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
