@extends('layouts.header_auth', ['title' => 'Cadastre-se'])
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

    .my-auto{
        margin-top: 50px !important;
    }
</style>
@endsection
@section('content')
<div class="auth-fluid">
    <!-- Auth fluid right content -->
    <div class="auth-fluid-right text-center">
        <div class="auth-user-testimonial">
            <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">

            </div>
        </div> <!-- end auth-user-testimonial-->
    </div>
    <!-- end Auth fluid right content -->

    @php
    $captchaa = 0;
    try{
        captcha_img();
        $captchaa = 1;
    }catch(\Exception $e){
    }
    @endphp

    <!--Auth fluid left content -->
    <div class="auth-fluid-form-box">
        <div class="card-body d-flex flex-column h-100 gap-3">

            <!-- Logo -->
            <div class="auth-brand text-center text-lg-start logo-mob">
                <a href="index.html" class="logo-dark">
                    <span><img style="width: 300px" src="/logo.png" alt="dark logo"></span>

                </a>
                
            </div>

            <div class="my-auto">
                <!-- title-->
                <h4 class="mt-3">Cadastre-se</h4>
                <p class="text-muted mb-4">Crie sua conta, leva menos de um minuto!</p>

                <!-- form -->
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <input value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" type="text" id="name" placeholder="Nome" required name="name">
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <input value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" type="email" id="email" placeholder="Email" required name="email">
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <input value="{{ old('password') }}" class="form-control @error('password') is-invalid @enderror" type="password" id="password" placeholder="Senha" required name="password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" id="password_confirmation" placeholder="Confirme a Senha" value="{{ old('password_confirmation') }}" required name="password_confirmation">
                        @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <input type="hidden" name="plano" value="{{ request()->plano }}">
                    <input type="hidden" name="captchaa" value="{{ $captchaa }}">

                    @if($captchaa == 1)
                    <div class="mb-3">
                        {!! captcha_img() !!}
                        <br>
                        <input class="form-control @error('captcha') is-invalid @enderror" type="text" id="captcha" placeholder="Caracteres" required name="captcha">
                    </div>
                    @endif

                    <div class="mb-0 d-grid text-center">
                        <button class="btn btn-primary fw-semibold" type="submit">Cadastrar</button>
                    </div>
                    <!-- social-->

                </form>
                <!-- end form-->
            </div>

            <!-- Footer-->
            <footer class="footer footer-alt">
                <p class="text-muted">Já tem conta? <a href="{{ route('login') }}" class="text-muted ms-1"><b>Login</b></a></p>
            </footer>

        </div> <!-- end .card-body -->
    </div>
    <!-- end auth-fluid-form-box-->
</div>
@endsection

@section('js')
<script type="text/javascript">

    $(html).attr('data-bs-theme', '{{ __dataThemeDefault() }}')
</script>
@endsection
