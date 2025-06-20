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
            <form method="get" action="{{ route('relatorios-adm.empresas') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Empresas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6 col-12">
                                {!!Form::date('start_date', 'Dt. cadastro inicial')
                                !!}
                            </div>
                            <div class="col-md-6 col-12">
                                {!!Form::date('end_date', 'Dt. cadastro final')
                                !!}
                            </div>
                            <div class="col-md-6 col-12">
                                {!!Form::select('status', 'Status',
                                [
                                '' => 'Selecione',
                                '1' => 'Ativa',
                                '-1' => 'Desativada',
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
            <form method="get" action="{{ route('relatorios-adm.historico-acesso') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Histórico de Acessos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6 col-12">
                                {!!Form::date('start_date', 'Dt. inicial')
                                !!}
                            </div>
                            <div class="col-md-6 col-12">
                                {!!Form::date('end_date', 'Dt. final')
                                !!}
                            </div>
                            <div class="col-md-12">
                                {!!Form::select('empresa', 'Empresa')
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
            <form method="get" action="{{ route('relatorios-adm.planos') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Planos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6 col-12">
                                {!!Form::date('start_date', 'Dt. inicial expiração')
                                !!}
                            </div>
                            <div class="col-md-6 col-12">
                                {!!Form::date('end_date', 'Dt. final expiração')
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
            <form method="get" action="{{ route('relatorios-adm.certificados') }}" target="_blank">
                <div class="card">
                    <div class="card-header">
                        <h5>Relatório de Certificados à Vencer</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6 col-12">
                                {!!Form::date('start_date', 'Dt. inicial')
                                !!}
                            </div>
                            <div class="col-md-6 col-12">
                                {!!Form::date('end_date', 'Dt. final')
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

    </div>
</div>
@endsection