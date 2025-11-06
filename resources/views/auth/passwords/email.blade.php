@extends('layouts.header_auth', ['title' => 'Esqueci minha senha'])

@section('css')
  <style type="text/css">
    h3 {
      font-weight: 700;
      text-align: center;
    }

    p {
      text-align: center;
      margin-bottom: 0px;
    }
  </style>
@endsection

@section('content')
  <div class="auth-fluid">

    <div class="image-auth">
      <img style="width: 500px;" src="/porquinho.png" alt="dark logo">
    </div>

    <div class="card-auth d-flex flex-column gap-3">
      <!-- Logo -->
      <div class="auth-brand text-center logo-mob">
        <span><img style="width: 180px" src="/logo.png" alt="dark logo"></span>
      </div>

      <div class="my-auto">
        <h3 class="m-0">Redefina sua senha</h3>
        <p class="text-muted mb-2">Informe seu e-mail para redefinir sua senha.</p>
        <hr />

        <form method="POST" action="{{ route('reset.pass') }}">
          @csrf
          <div class="mb-3">
            <label for="emailaddress" class="form-label">Email</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
              name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

            @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>


          @if (Session::has('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
          @endif
          <div class="d-grid mb-0 text-center">
            <button class="btn btn-primary" type="submit">
              Redefinir senha
            </button>
          </div>
        </form>
        <!-- end form-->
      </div>

      <p class="text-muted">
        <a href="{{ route('login') }}" class="text-muted ms-1">
          Voltar para login
        </a>
      </p>
    </div>
  </div>
@endsection
