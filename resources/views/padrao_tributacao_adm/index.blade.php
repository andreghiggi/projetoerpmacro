@extends('layouts.app', ['title' => 'Tributações Padrão'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-header">
                <div style="text-align: right; margin-top: -5px;">
                    <a href="{{ route('empresas.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <h5>Empresa <strong class="text-primary">{{ $empresa->nome }}</strong></h5>

                <div class="col-md-12">
                    <a href="{{ route('produtopadrao-tributacao-adm.create', ['empresa='. $empresa->id]) }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Padrão
                    </a>

                </div>
                <hr class="mt-3">

                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    
                                    <th>Descrição</th>
                                    <th>Padrão</th>
                                    <th>NCM</th>
                                    <th>%ICMS</th>
                                    <th>%PIS</th>
                                    <th>%COFINS</th>
                                    <th>%IPI</th>
                                    <th>CST/CSOSN</th>
                                    <th>CST PIS</th>
                                    <th>CST COFINS</th>
                                    <th>CST IPI</th>
                                    <th width="12%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    
                                    <td width="300">{{ $item->descricao }}</td>
                                    <td>
                                        @if($item->padrao)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>{{ $item->ncm }}</td>
                                    <td>{{ $item->perc_icms }}</td>
                                    <td>{{ $item->perc_pis }}</td>
                                    <td>{{ $item->perc_cofins }}</td>
                                    <td>{{ $item->perc_ipi }}</td>
                                    <td>{{ $item->cst_csosn }}</td>
                                    <td>{{ $item->cst_pis }}</td>
                                    <td>{{ $item->cst_cofins }}</td>
                                    <td>{{ $item->cst_ipi }}</td>
                                    <td>
                                        <form action="{{ route('produtopadrao-tributacao-adm.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            <a class="btn btn-warning btn-sm" href="{{ route('produtopadrao-tributacao-adm.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>

                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>

                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="13" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <br>
                        
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection

