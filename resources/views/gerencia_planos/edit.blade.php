@extends('layouts.app', ['title' => 'Editar Plano'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Plano</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('gerenciar-planos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->put()
        ->route('gerenciar-planos.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            <div class="row g-2">

                <div class="col-md-2">
                    {!!Form::text('plano', 'Plano')
                    ->readonly()
                    ->value($item->plano->nome)
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::date('data_expiracao', 'Vencimento')
                    ->value($item->data_expiracao)->required()
                    !!}
                </div>
                <div class="col-12" style="text-align: right;">
                    <button type="submit" class="btn btn-success px-5" id="btn-store">Atualizar</button>
                </div>
            </div>
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
