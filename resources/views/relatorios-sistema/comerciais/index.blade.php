@extends('layouts.app', ['title' => 'Relatórios'])
@section('css')
<style type="text/css">
    .card-header {
        border-bottom: 1px solid #999;
        margin-left: 5px;
        margin-right: 5px;
    }

</style>
@endsection
@section('content')
<div class="mt-3">
    <div class="row">

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios-comerciais.vendas') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Vendas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 col-12">
                                {!!Form::date('start_date', 'Data inicial')
                                !!}
                            </div>
                            <div class="col-md-4 col-12">
                                {!!Form::date('end_date', 'Data final')
                                !!}
                            </div>
                            <div class="col-md-4 col-12">
                                {!!Form::select('estado', 'Estado',
                                ['novo' => 'Novas',
                                'rejeitado' => 'Rejeitadas',
                                'cancelado' => 'Canceladas',
                                'aprovado' => 'Aprovadas',
                                '' => 'Todos'])
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            <div class="col-md-6 col-12 mt-2">
                                {!!Form::select('cliente', 'Cliente')
                                ->attrs(['class' => 'form-select cliente_id'])
                                !!}
                            </div>

                            <div class="col-md-3 col-6 mt-2">
                                {!!Form::time('start_time', 'Horário inicial')
                                !!}
                            </div>
                            <div class="col-md-3 col-6 mt-2">
                                {!!Form::time('end_time', 'Horário final')
                                !!}
                            </div>

                            @if(__countLocalAtivo() > 1)
                            <div class="col-md-6 col-12 mt-2">
                                {!!Form::select('local_id', 'Local', ['' => 'Selecione'] + __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
                            @endif


                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-dark w-100">
                            <i class="ri-printer-line"></i> Gerar relatório
                        </button>
                    </div>
                </div>
            </form>
        </div>


    </div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="/js/relatorio.js"></script>
@endsection


