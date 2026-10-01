@extends('layouts.app')

@section('title', 'Sign in | Kian Blog')

@section('content')
    <section class="surface mx-auto p-4 p-md-5" style="max-width: 480px">
        <p class="text-uppercase small fw-bold text-muted mb-2">Welcome back</p>
        <h1 class="h3 mb-4">Sign in to your account</h1>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <label class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" value="1">
                <span class="form-check-label">Remember me</span>
            </label>
            <button class="btn btn-dark w-100" type="submit">Sign in</button>
        </form>
        <p class="text-muted small mt-4 mb-0">New here? <a href="{{ route('register') }}">Create an account</a></p>
    </section>
@endsection
