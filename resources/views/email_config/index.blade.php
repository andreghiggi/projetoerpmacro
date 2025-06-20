@extends('layouts.app', ['title' => 'Configuração de Email'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração de Email</h4>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('email-config.store')
        !!}
        <div class="pl-lg-4">
            @include('email_config._forms')
        </div>
        {!!Form::close()!!}

        <div class="row">
            <div class="col-12">
                @if(isset($item) && $item->status)
                <p>Utilizando o email configurado <strong>{{ $item->email }}</strong></p>
                @else
                <p>Utilizando o email administrador <strong>{{ env("MAIL_USERNAME") }}</strong></p>
                @endif

                <a class="float-right" href="{{ route('teste-email') }}">testar email</a>
            </div>
        </div>
    </div>
</div>
@endsection
