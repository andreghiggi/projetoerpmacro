@extends('layouts.app', ['title' => 'MDFe - Documentos não encerrados'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-header">
                <h4>MDFe - Documentos não encerrados</h4>
                <div style="text-align: right; margin-top: -35px;">
                    <a href="{{ route('mdfe.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="col-lg-12">

                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead>
                                <tr>
                                    <th>Chave</th>
                                    <th>Protocolo</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if(sizeof($data) == 0)
                                <tr>
                                    <td colspan="3" class="center-align">
                                        <h5 class="text-center">Nada Encontrado</h5>
                                    </td>
                                </tr>
                                @endif
                                @foreach($data as $m)

                                <tr class="datatable-row">

                                    <td class="datatable-cell">
                                        <span class="codigo" style="width: 250px;" id="chave">
                                            {{$m['chave']}}
                                        </span>
                                    </td>

                                    <td class="datatable-cell">
                                        <span class="codigo" style="width: 150px;" id="protocolo">
                                            {{$m['protocolo']}}
                                        </span>
                                    </td>

                                    <td class="datatable-cell">
                                        <form action="{{ route('mdfe.encerrar') }}" method="get" id="form-{{$m['chave']}}">
                                            <input type="hidden" value="{{$m['chave']}}" name="chave">
                                            <input type="hidden" value="{{$m['protocolo']}}" name="protocolo">
                                            <button class="btn btn-sm btn-danger btn-confirm">Encerrar</button>
                                        </form>

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
