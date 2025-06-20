@extends('layouts.app', ['title' => 'Editar Boleto'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Boleto</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('financeiro-boleto.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('financeiro-boleto.update', [$item->id])

        !!}
        <div class="pl-lg-4">
            <div class="row g-2">
                <div class="col-md-2">
                    {!!Form::tel('valor_recebido', 'Valor recebido')->required()
                    ->attrs(['class' => 'moeda'])
                    ->value(__moeda($item->valor))
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::select('status', 'Status', [1 => 'Recebido', 0 => 'Pendente'])->required()
                    ->attrs(['class' => 'form-select'])
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::date('data_recebimento', 'Data de recebimento')->required()
                    !!}
                </div>
            </div>
            <hr class="mt-4">
            <div class="col-12" style="text-align: right;">
                <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="/js/financeiro_boleto.js"></script>
@endsection


