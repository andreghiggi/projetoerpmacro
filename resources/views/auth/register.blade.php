@extends('layouts.header_auth', ['title' => 'Cadastre-se'])

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

@section('content')
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
        <h3 class="m-0">Cadastre-se</h3>
        <p class="text-muted mb-2">Crie sua conta, leva menos de um minuto!</p>
        <hr />

        <!-- form -->
        <form method="POST" action="{{ route('register') }}">
          @csrf
          <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name"
              placeholder="Nome" required name="name">
            @error('name')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input class="form-control @error('email') is-invalid @enderror" type="email" id="email"
              placeholder="Email" required name="email">
            @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input class="form-control @error('password') is-invalid @enderror" type="password" id="password"
              placeholder="Senha" required name="password">
            @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirme a Senha</label>
            <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password"
              id="password_confirmation" placeholder="Confirme a Senha" required name="password_confirmation">
            @error('password_confirmation')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <div class="mb-0 d-grid text-center">
            <button class="btn btn-primary fw-semibold" type="submit">Cadastrar </button>
          </div>
        </form> <!-- end form-->

        <p class="text-muted">Já tem conta? <a href="{{ route('login') }}" class="text-muted ms-1"><b>Login</b></a></p>
      </div>
    </div>
  </div>
@endsection
