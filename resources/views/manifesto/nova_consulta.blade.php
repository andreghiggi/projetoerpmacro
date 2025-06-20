@extends('layouts.app', ['title' => 'Manifesto'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
                    <h4>Nova Consulta</h4>
                    <a href="{{ route('manifesto.index') }}" class="btn btn-danger btn-sm">
                        <i class="ri-arrow-left-double-fill"></i>
                        Voltar para os documentos
                    </a>
                    <p id="aguarde" class="text-info d-none">
                        <a id="btn-enviar" class="btn btn-success spinner-white spinner spinner-right">
                            Consultado novos documentos, aguarde ...
                        </a>
                    </p>
                    <p id="sem-resultado" style="display: none" class="center-align text-danger">Nenhum novo resultado...</p>
                    <div class="col-xl-12" id="table" style="display: none">

                        <div class="table-responsive mt-2">
                            <table class="table table-striped table-centered mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nome</th>
                                        <th>CPF/CNPJ</th>
                                        <th>Valor</th>
                                        <th>Chave</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')

<script type="text/javascript" src="/js/dfe.js"></script>
@endsection


@endsection

