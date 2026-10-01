@extends('layouts.app')

@section('title', 'Create account | Kian Blog')

@section('content')
    <section class="surface mx-auto p-4 p-md-5" style="max-width: 480px">
        <p class="text-uppercase small fw-bold text-muted mb-2">Join the conversation</p>
        <h1 class="h3 mb-4">Create an account</h1>
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="name">Name</label>
                <input class="form-control" id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input class="form-control" id="password" name="password" type="password" autocomplete="new-password" required>
            </div>
            <div class="mb-4">
                <label class="form-label" for="password_confirmation">Confirm password</label>
                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            </div>
            <button class="btn btn-dark w-100" type="submit">Create account</button>
        </form>
        <p class="text-muted small mt-4 mb-0">Already registered? <a href="{{ route('login') }}">Sign in</a></p>
    </section>
@endsection
