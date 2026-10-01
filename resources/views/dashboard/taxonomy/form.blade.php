@extends('layouts.app')

@section('title', ($item ? 'Edit ' : 'New ').$singular.' | Kian Blog')

@section('content')
    @php($indexRoute = 'dashboard.'.$resource.'.index')
    @php($storeRoute = 'dashboard.'.$resource.'.store')
    @php($updateRoute = 'dashboard.'.$resource.'.update')
    <a href="{{ route($indexRoute) }}" class="small text-muted text-decoration-none"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Back to {{ $resource }}</a>
    <section class="surface p-4 p-lg-5 mt-3" style="max-width: 680px">
        <h1 class="h2 mb-4">{{ $item ? 'Edit '.$singular : 'New '.$singular }}</h1>
        <form action="{{ $item ? route($updateRoute, $item) : route($storeRoute) }}" method="POST">
            @csrf
            @if ($item)
                @method('PUT')
            @endif
            <div class="mb-4">
                <label class="form-label" for="name">{{ $singular }} name</label>
                <input class="form-control" id="name" name="name" value="{{ old('name', $item?->name) }}" maxlength="120" required autofocus>
            </div>
            <button class="btn btn-dark" type="submit">{{ $item ? 'Save changes' : 'Create '.$singular }}</button>
        </form>
    </section>
@endsection
