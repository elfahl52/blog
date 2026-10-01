@extends('layouts.app')

@section('title', 'Edit article | Kian Blog')

@section('content')
    <a href="{{ route('dashboard.posts.index') }}" class="small text-muted text-decoration-none"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i>All articles</a>
    <section class="surface p-4 p-lg-5 mt-3">
        <h1 class="h2 mb-4">Edit article</h1>
        <form action="{{ route('dashboard.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('dashboard.posts._form')
            <button class="btn btn-dark" type="submit">Save changes</button>
        </form>
    </section>
@endsection
