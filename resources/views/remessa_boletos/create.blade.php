@extends('layouts.app', ['title' => 'Nova Remessa'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Remessa</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('remessa-boleto.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>

        <div class="col-lg-12">
            {!!Form::open()->fill(request()->all())
            ->get()
            !!}
            <div class="row mt-3">
                <div class="col-md-2">
                    {!!Form::date('start_date', 'Data inicial')
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::date('end_date', 'Data final')
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::select('conta_boleto_id', 'Conta boleto', ['' => 'Selecione'] + $contasBoleto->pluck('info', 'id')->all())
                    ->attrs(['class' => 'form-select'])
                    !!}
                </div>
                <div class="col-md-3 text-left ">
                    <br>
                    <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                    <a id="clear-filter" class="btn btn-danger" href="{{ route('remessa-boleto.create') }}"><i class="ri-eraser-fill"></i>Limpar</a>

                </div>
            </div>
            {!!Form::close()!!}
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('remessa-boleto.store')
        !!}
        <div class="pl-lg-4">
            @include('remessa_boletos._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
