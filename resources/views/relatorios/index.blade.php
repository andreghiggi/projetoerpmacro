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
            <form method="get" action="{{ route('relatorios.produtos') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Produtos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                {!!Form::select('estoque', 'Estoque',
                                ['' => 'Selecione', '1' => 'Positivo', '-1' => 'Negativo'])
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
                            <div class="col-md-6 col-12">
                                {!!Form::select('tipo', 'Tipo',
                                ['' => 'Selecione', '1' => 'Mais vendidos', '-1' => 'Menos vendidos'])
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
                            <div class="col-md-6 col-12 mt-2">
                                {!!Form::select('marca_id', 'Marca', ['' => 'Selecione'] + $marcas->pluck('nome', 'id')->all())
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
                            <div class="col-md-6 col-12 mt-2">
                                {!!Form::select('categoria_id', 'Categoria', ['' => 'Selecione'] + $categorias->pluck('nome', 'id')->all())
                                ->attrs(['class' => 'form-select'])
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.nfe') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de NFe</h5>
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
                                {!!Form::select('tipo', 'Tipo',
                                [
                                '' => 'Selecione',
                                '1' => 'Saída',
                                '-1' => 'Entrada',
                                ])
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            <div class="col-md-6 col-12 mt-2">
                                {!!Form::select('_cliente', 'Cliente')
                                ->attrs(['class' => 'form-select cliente_id'])
                                !!}
                            </div>

                            <div class="col-md-3 col-12 mt-2">
                                {!!Form::select('finNFe', 'Finalidade NFe', [
                                '1' => 'NFe normal',
                                '2' => 'NFe complementar',
                                '3' => 'NFe de ajuste',
                                '4' => 'Devolução de mercadoria'])
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            <div class="col-md-3 mt-2">
                                {!!Form::select('estado', 'Estado',
                                ['novo' => 'Novas',
                                'rejeitado' => 'Rejeitadas',
                                'cancelado' => 'Canceladas',
                                'aprovado' => 'Aprovadas',
                                '' => 'Todos'])
                                ->attrs(['class' => 'form-select'])
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.clientes') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Clientes</h5>
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
                                {!!Form::select('tipo', 'Tipo',
                                [
                                '' => 'Selecione',
                                '1' => 'Mais vendas',
                                '-1' => 'Menos vendas',
                                ])
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.fornecedores') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Fornecedores</h5>
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
                                {!!Form::select('tipo', 'Tipo',
                                [
                                '' => 'Selecione',
                                '1' => 'Mais compras',
                                '-1' => 'Menos compras',
                                ])
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.cte') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de CTe</h5>
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
                            <div class="col-md-3 col-12">
                                {!!Form::select('estado', 'Estado',
                                ['novo' => 'Novas',
                                'rejeitado' => 'Rejeitadas',
                                'cancelado' => 'Canceladas',
                                'aprovado' => 'Aprovadas',
                                '' => 'Todos'])
                                ->attrs(['class' => 'form-select'])
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


        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.nfce') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de NFCe</h5>
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
                                {!!Form::select('cliente_id', 'Cliente')
                                ->attrs(['class' => 'form-select cliente_id'])
                                !!}
                            </div>

                            <div class="col-md-3 mt-2">
                                {!!Form::select('estado', 'Estado',
                                ['novo' => 'Novas',
                                'rejeitado' => 'Rejeitadas',
                                'cancelado' => 'Canceladas',
                                'aprovado' => 'Aprovadas',
                                '' => 'Todos'])
                                ->attrs(['class' => 'form-select'])
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.conta_pagar') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Contas a Pagar</h5>
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
                                {!!Form::select('status', 'Estado',
                                ['1' => 'Quitadas', '-1' => 'Pendentes', '' => 'Todas'])
                                ->attrs(['class' => 'form-select'])
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.conta_receber') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Contas a Receber</h5>
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
                                {!!Form::select('status', 'Estado',
                                ['1' => 'Recebidas',
                                '-1' => 'Pendentes',
                                '' => 'Todos'])
                                ->attrs(['class' => 'form-select'])
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


        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.comissao') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Comissão</h5>
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
                                {!!Form::select('funcionario_id', 'Funcionário', ['' => 'Selecione'] + $funcionarios->pluck('nome', 'id')->all())
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.compras') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Compras</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-12">
                                {!!Form::date('start_date', 'Data inicial')
                                !!}
                            </div>
                            <div class="col-md-3 col-12">
                                {!!Form::date('end_date', 'Data final')
                                !!}
                            </div>
                            @if(__countLocalAtivo() > 1)
                            <div class="col-md-6 col-12">
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.vendas') }}" target="_blank">
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.mdfe') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de MDFe</h5>
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
                            <div class="col-md-3 col-12">
                                {!!Form::select('estado', 'Estado',
                                ['novo' => 'Novas',
                                'rejeitado' => 'Rejeitadas',
                                'cancelado' => 'Canceladas',
                                'aprovado' => 'Aprovadas',
                                '' => 'Todos'])
                                ->attrs(['class' => 'form-select'])
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.taxas') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Taxas</h5>
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.lucro') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Lucros</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-12">
                                {!!Form::date('start_date', 'Data inicial')
                                !!}
                            </div>
                            <div class="col-md-3 col-12">
                                {!!Form::date('end_date', 'Data final')
                                !!}
                            </div>
                            @if(__countLocalAtivo() > 1)
                            <div class="col-md-6 col-12">
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.despesa-frete') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Despesa de Fretes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-12">
                                {!!Form::date('start_date', 'Data inicial')
                                !!}
                            </div>
                            <div class="col-md-3 col-12">
                                {!!Form::date('end_date', 'Data final')
                                !!}
                            </div>

                            <div class="col-md-3 col-12">
                                {!!Form::select('tipo_despesa_frete_id', 'Tipo de despesa', ['' => 'Todos'] + $tiposDespesaFrete->pluck('nome', 'id')->all())
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.totaliza-produtos') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório Totalizador de Produtos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-12">
                                {!!Form::date('start_date', 'Data inicial cadastro')
                                !!}
                            </div>
                            <div class="col-md-3 col-12">
                                {!!Form::date('end_date', 'Data final cadastro')
                                !!}
                            </div>

                            @if(__countLocalAtivo() > 1)
                            <div class="col-md-6 col-12">
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.estoque') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Estoque</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-3 col-12">
                                {!!Form::date('start_date', 'Data inicial')
                                !!}
                            </div>
                            <div class="col-md-3 col-12">
                                {!!Form::date('end_date', 'Data final')
                                !!}
                            </div>

                            <div class="col-md-6 col-12">
                                {!!Form::select('categoria_id', 'Categoria', ['' => 'Selecione'] + $categorias->pluck('nome', 'id')->all())
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            <div class="col-md-4 col-12">
                                {!!Form::select('estoque_minimo', 'Estoque mínimo',
                                ['-1' => 'Não', '1' => 'Sim'])
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            <div class="col-md-4 col-12">
                                {!!Form::select('esportar_excel', 'Exportar excel',
                                ['-1' => 'Não', '1' => 'Sim'])
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            @if(__countLocalAtivo() > 1)
                            <div class="col-md-4 col-12">
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.inventario-custo-medio') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Inventário de Custo Médio</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-3 col-12">
                                {!!Form::date('start_date', 'Data cadastro inicial')
                                !!}
                            </div>
                            <div class="col-md-3 col-12">
                                {!!Form::date('end_date', 'Data cadastro final')
                                !!}
                            </div>
                            <div class="col-md-6 col-12">
                                {!!Form::select('categoria_id', 'Categoria', ['' => 'Selecione'] + $categorias->pluck('nome', 'id')->all())
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            <div class="col-md-6 col-12">
                                {!!Form::select('ordem', 'Ordem', ['desc' => 'Mais Estoque', 'asc' => 'Menos Estoque', 'alfa' => 'Alfabética'])
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
                            @if(__countLocalAtivo() > 1)
                            <div class="col-md-6 col-12">
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.curva-abc-clientes') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório Curva ABC - Clientes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                                
                            <div class="col-md-6 col-12">
                                {!!Form::select('tipo', 'Tipo', ['clientes' => 'Clientes', 'produtos' => 'Produtos'] )
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            <div class="col-md-3 col-12">
                                {!!Form::date('start_date', 'Data inicial')
                                !!}
                            </div>
                            <div class="col-md-3 col-12">
                                {!!Form::date('end_date', 'Data final')
                                !!}
                            </div>
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.entrega-produtos') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Entrega de Produtos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-3 col-12">
                                {!!Form::date('start_date', 'Data inicial')
                                !!}
                            </div>
                            <div class="col-md-3 col-12">
                                {!!Form::date('end_date', 'Data final')
                                !!}
                            </div>
                            <div class="col-md-6 col-12">
                                <label>Vendas</label>
                                <select class="form-control inp-vendas" name="vendas[]" >
                                    
                                </select>
                            </div>
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

        <div class="col-12 col-md-6">
            <form method="get" action="{{ route('relatorios.venda-produtos') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Venda de Produtos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-3 col-12">
                                {!!Form::date('start_date', 'Data inicial')
                                !!}
                            </div>
                            <div class="col-md-3 col-12">
                                {!!Form::date('end_date', 'Data final')
                                !!}
                            </div>

                            <div class="col-md-6 col-12">
                                {!!Form::select('marca_id', 'Marca', ['' => 'Selecione'] + $marcas->pluck('nome', 'id')->all())
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
                            <div class="col-md-6 col-12">
                                {!!Form::select('categoria_id', 'Categoria', ['' => 'Selecione'] + $categorias->pluck('nome', 'id')->all())
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            <div class="col-md-6 col-12">
                                {!!Form::select('produto_id', 'Produto')
                                ->attrs(['class' => 'form-select produtos_filtro'])
                                !!}
                            </div>

                            <div class="col-md-6 col-12">
                                {!!Form::select('ordem', 'Ordem', ['desc' => 'Mais Vendidos', 'asc' => 'Menos Vendidos', 'alfa' => 'Alfabética'])
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
                            @if(__countLocalAtivo() > 1)
                            <div class="col-md-6 col-12">
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


