@extends('layouts.header_auth', ['title' => 'Login'])

@section('content')
    @php
        $login = isset($_COOKIE['ckLogin']) ? base64_decode($_COOKIE['ckLogin']) : '';
        $pass = isset($_COOKIE['ckPass']) ? base64_decode($_COOKIE['ckPass']) : '';
        $remember = isset($_COOKIE['ckRemember']) ? $_COOKIE['ckRemember'] : '';
    @endphp

@section('css')
    <style type="text/css">

        h3{
            font-weight: 700; 
            text-align: center;
        }

        p{
            text-align: center;
            margin-bottom: 0px;
        }
    </style>
@endsection
<div class="auth-fluid">
    <div class="image-auth">
        <img style="width: 500px;" src="/porquinho.png" alt="dark logo">
    </div>

    <div class="card-auth d-flex flex-column gap-3">

        <!-- Logo -->
        <div class="auth-logo text-center text-lg-start logo-mob">
            <span><img style="width: 180px;" src="/logo.png" alt="dark logo"></span>
        </div>

        <div class="my-auto">
            <!-- title-->
            @if (env('APP_ENV') == 'demo')
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
                                    ADMNISTRADOR
                                </button>
                            </div>
                        </div>
                        <br>
                        <a href="https://wa.me/5543920004769">WhatsApp <strong>43920004769</strong></a>
                    </div>
            @endif
            <h3 class="m-0">Acesse sua conta</h3>
            <p class="text-muted mb-2">Informe seu e-mail e senha para acessar a conta.</p>
            <hr/>

            <!-- form -->
            <form method="POST" action="{{ route('login') }}" id="form-login">

                @csrf

                <div class="mb-3">
                    <label for="emailaddress" class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" id="email" required
                        value="{{ $login }}" placeholder="Digite seu email">
                </div>
                <div class="mb-3">
                    <a href="{{ route('password.request') }}" class="text-muted float-end"><small>Esqueceu sua
                            senha?</small></a>
                    <label for="password" class="form-label">Senha</label>
                    <input class="form-control" type="password" name="password" required value="{{ $pass }}"
                        id="password" placeholder="Digite sua senha">
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input name="remember" type="checkbox" {{ $remember ? 'checked' : '' }} class="form-check-input"
                            id="checkbox-signin">
                        <label class="form-check-label" for="checkbox-signin">lembrar-me</label>
                    </div>
                </div>

                @if (Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error') }}</div>
                @endif

                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                <div class="d-grid mb-0 text-center">
                    <button class="btn btn-primary" type="submit">Acessar</button>
                </div>
            </form>
            <!-- end form-->
        </div>

        <!-- Footer-->
        @if (request()->auto_cadastro)
            <p class="text-muted">Não tem uma conta? <a href="{{ route('register') }}"
                        class="text-muted ms-1"><b>Inscrever-se</b></a></p>
        @endif

    </div> <!-- end .card-body -->
</div>
@endsection

@section('js')
<script type="text/javascript">
    function login(email, senha) {
        $('#email').val(email)
        $('#password').val(senha)
        $('#form-login').submit()
    }
</script>
@endsection
