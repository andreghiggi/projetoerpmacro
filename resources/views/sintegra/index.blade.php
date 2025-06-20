@extends('layouts.app', ['title' => 'Sintegra'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Gerar Arquivo Sintegra</h4>

    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('sintegra.store')
        !!}
        <div class="pl-lg-4">
            <div class="row g-2">

                <div class="col-md-2">
                    {!!Form::date('start_date', 'Data de início')->required()
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::date('end_date', 'Data de fim')->required()
                    !!}
                </div>

                @if(__countLocalAtivo() > 1)
                <div class="col-md-2">
                    {!!Form::select('local_id', 'Local', ['' => 'Selecione'] + __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
                    ->attrs(['class' => 'select2'])
                    !!}
                </div>
                @endif
            </div>

            <div class="col-12" style="text-align: right;">
                <button type="submit" class="btn btn-success px-5">Gerar</button>
            </div>
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
