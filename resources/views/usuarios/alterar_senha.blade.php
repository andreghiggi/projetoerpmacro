@extends('layouts.app', ['title' => 'Alterar Senha'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Alterar Senha</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('usuarios.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('usuarios.update-senha', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            <div class="row g-2">
                <div class="col-md-2">
                    {!!Form::text('senha', 'Senha')
                    ->required()
                    ->type('password')
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::text('repita_senha', 'Repita a Senha')
                    ->required()
                    ->type('password')
                    !!}
                </div>
            </div>
            <hr class="mt-4">
            <div class="col-12" style="text-align: right;">
                <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
            </div>
        </div>
    </div>
    {!!Form::close()!!}
</div>
@endsection
