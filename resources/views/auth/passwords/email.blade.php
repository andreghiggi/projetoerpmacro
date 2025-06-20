@extends('layouts.header_auth', ['title' => 'Esqueci minha senha'])


@section('content')

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
            </div>

            <div class="">
                <form method="POST" action="{{ route('reset.pass') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="emailaddress" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>


                    @if(Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error') }}</div>
                    @endif
                    <div class="d-grid mb-0 text-center">
                        <button class="btn btn-primary" type="submit">
                            <i class="ri-send"></i> 
                            Redefinir senha
                        </button>
                    </div>
                    <!-- social-->
                </form>
                <!-- end form-->
            </div>

            <!-- Footer-->
            <footer class="footer footer-alt">
                <p class="text-muted">
                    <a href="{{ route('login') }}" class="text-muted ms-1"><b>
                        <i class="ri-arrow-go-back-fill"></i>
                        Voltar para login
                    </b></a>
                </p>
            </footer>

        </div> <!-- end .card-body -->
    </div>
    <!-- end auth-fluid-form-box-->
</div>

@endsection
