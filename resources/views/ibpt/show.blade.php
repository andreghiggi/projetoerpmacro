@extends('layouts.app', ['title' => 'IBPT'])
@section('css')
<style type="text/css">
    @page { size: auto;  margin: 0mm; }

    @media print {
        .print{
            margin: 10px;
        }
    }
</style>
@endsection
@section('content')
<div class="mt-1 print">
    <div class="row">

        <div class="card">
            <div class="card-body">

                <!-- Invoice Logo-->
                <div class="clearfix">
                    <div class="float-start mb-3">

                    </div>
                    <div class="float-">
                        <h4>UF: <strong class="text-danger">{{ $item->uf }}</strong></h4>
                        <h5>versão: <strong class="text-danger">{{ $item->versao }}</strong></h5>
                    </div>

                    <div style="text-align: right; margin-top: -35px;">
                        <a href="{{ route('ibpt.index') }}" class="btn btn-danger btn-sm px-3">
                            <i class="ri-arrow-left-double-fill"></i>Voltar
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>NCM</th>
                                    <th style="width: 20%">Descrição</th>
                                    <th>Nacional/Federal</th>
                                    <th>Importado/Federal</th>
                                    <th>Estadual</th>
                                    <th>Municipal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $i)
                                <tr>
                                    <td>{{ $i->codigo }}</td>
                                    <td>{{ $i->descricao }}</td>
                                    <td>{{ $i->nacional_federal }}</td>
                                    <td>{{ $i->importado_federal }}</td>
                                    <td>{{ $i->estadual }}</td>
                                    <td>{{ $i->municipal }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-3">
                    {!! $data->appends(request()->all())->links() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
