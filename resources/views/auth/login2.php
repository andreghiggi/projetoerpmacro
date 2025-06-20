@extends('layouts.header_auth', ['title' => 'Login'])

@section('content')

@php
$login = (isset($_COOKIE['ckLogin'])) ? base64_decode($_COOKIE['ckLogin']) : '';
$pass = (isset($_COOKIE['ckPass'])) ? base64_decode($_COOKIE['ckPass']) : '';
$remember = (isset($_COOKIE['ckRemember'])) ? ($_COOKIE['ckRemember']) : '';

@endphp
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Login</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ $login }}" required autocomplete="email" autofocus>

                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">Senha</label>

                            <div class="col-md-6">
                                <input id="password" type="password" value="{{ $pass }}" class="form-control" name="password" required autocomplete="current-password">

                            </div>
                        </div>

                        @if(Session::has('error'))
                        <div class="alert alert-danger">{{ Session::get('error') }}</div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ $remember ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        lembrar-me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    Login
                                </button>

                                <a href="{{ route('register') }}">quero me cadastrar</a>

                                <!-- @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                    @endif -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
