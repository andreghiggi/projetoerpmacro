@extends('layouts.header_auth', ['title' => 'Login'])

@section('content')

@php
$login = (isset($_COOKIE['ckLogin'])) ? base64_decode($_COOKIE['ckLogin']) : '';
$pass = (isset($_COOKIE['ckPass'])) ? base64_decode($_COOKIE['ckPass']) : '';
$remember = (isset($_COOKIE['ckRemember'])) ? ($_COOKIE['ckRemember']) : '';
@endphp

@section('css')
<style type="text/css">
    img{
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 50%;
    }

    @media screen and (min-width: 800px) {
        .logo-mob{
            margin-top: -40px;
            height: 100px;
        }
    }

    .logo-mob{
        margin-top: -80px;
        height: 170px;

    }
</style>
@endsection
<div class="auth-fluid">

    <!-- Auth fluid right content -->
    <div class="auth-fluid-right text-center">
        <div class="auth-user-testimonial">
            <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-inner">

                </div>
            </div>
        </div> <!-- end auth-user-testimonial-->
    </div>
    <!-- end Auth fluid right content -->

    <!--Auth fluid left content -->
    <div class="auth-fluid-form-box">

        <div class="card-body d-flex flex-column h-100 gap-3">

            <!-- Logo -->
            <div class="auth-brand text-center text-lg-start logo-mob">
                <span><img style="width: 300px" src="/logo.png" alt="dark logo"></span>
                <h4 class="text-center text-muted" style="margin-top: -70px">{{ env("APP_DESCRIPTION") }}</h4>
            </div>

            <div class="my-auto">
                <!-- title-->
                @if(env("APP_ENV") == "demo")
                <div class="card">
                    <div class="card-body">
                        <p>Clique nos botões abaixo para acessar os usuários pré configurados!</p>
                        <div class="row">
                            <div class="col-12 col-lg-6 mt-1">
                                <button class="btn btn-success w-100" onclick="login('slym@slym.com', '123456')">
                                    SUPERADMIN
                                </button>
                            </div>
                            <div class="col-12 col-lg-6 mt-1">
                                <button class="btn btn-dark w-100" onclick="login('teste@teste.com', '123456')">
                                    EMPRESA TESTE
                                </button>
                            </div>
                        </div>
                        <br>
                        <a href="https://wa.me/5543920004769">WhatsApp <strong>43920004769</strong></a>
                    </div>
                    @endif
                    <h4 class="mt-0">Login</h4>
                    <p class="text-muted mb-4">Digite seu endereço de email e senha para acessar a conta.</p>

                    <!-- form -->
                    <form method="POST" action="{{ route('login') }}" id="form-login">
                        @csrf

                        <div class="mb-3">
                            <label for="emailaddress" class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" id="email" required value="{{ $login }}" placeholder="Digite seu email">
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('password.request') }}" class="text-muted float-end"><small>Esqueceu sua senha?</small></a>
                            <label for="password" class="form-label">Senha</label>
                            <input class="form-control" type="password" name="password" required value="{{ $pass }}" id="password" placeholder="Digite sua senha">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input name="remember" type="checkbox" {{ $remember ? 'checked' : '' }} class="form-check-input" id="checkbox-signin">
                                <label class="form-check-label" for="checkbox-signin">lembrar-me</label>
                            </div>
                        </div>

                        @if(Session::has('error'))
                        <div class="alert alert-danger">{{ Session::get('error') }}</div>
                        @endif

                        @if(Session::has('success'))
                        <div class="alert alert-success">{{ Session::get('success') }}</div>
                        @endif
                        <div class="d-grid mb-0 text-center">
                            <button class="btn btn-primary" type="submit"><i class="ri-login-box-line"></i> Acessar </button>
                        </div>
                        <!-- social-->
                        <br>
                        <a target="_blank" href="https://wa.me/55{{env('APP_FONE')}}"><i class="ri-whatsapp-fill"></i> Suporte</a>
                    </form>
                    <!-- end form-->
                </div>

                <!-- Footer-->
                @if(request()->auto_cadastro)
                <footer class="footer footer-alt">
                    <p class="text-muted">Não tem uma conta? <a href="{{ route('register') }}" class="text-muted ms-1"><b>Inscrever-se</b></a></p>
                </footer>
                @endif

            </div> <!-- end .card-body -->
        </div>
        <!-- end auth-fluid-form-box-->
    </div>
    @endsection

    @section('js')
    <script type="text/javascript">
        function login(email, senha){
            $('#email').val(email)
            $('#password').val(senha)
            $('#form-login').submit()
        }
        $(html).attr('data-bs-theme', '{{ __dataThemeDefault() }}')
    </script>
    @endsection
