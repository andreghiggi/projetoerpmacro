@extends('layouts.app', ['title' => 'Tributações Padrão'])
@section('content')

<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    @can('config_produto_fiscal_create')
                    <a href="{{ route('produtopadrao-tributacao.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Padrão
                    </a>
                    @endcan
                    @can('config_produto_fiscal_edit')
                    <a href="{{ route('produtopadrao-tributacao.alterar') }}" class="btn btn-dark float-end">
                        <i class="ri-refresh-line"></i>
                        Alterar tributação dos produtos
                    </a>
                    @endcan

                </div>
                <hr class="mt-3">

                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    @can('config_produto_fiscal_delete')
                                    <th>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                        </div>
                                    </th>
                                    @endcan
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
                                    @can('config_produto_fiscal_delete')
                                    <td data-label="Selecionar">
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item->id }}">
                                        </div>
                                    </td>
                                    @endcan

                                    <td data-label="Descrição">{{ $item->descricao }}</td>

                                    <td data-label="Padrão">
                                        @if($item->padrao)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>

                                    <td data-label="NCM">{{ $item->ncm }}</td>
                                    <td data-label="%ICMS">{{ $item->perc_icms }}</td>
                                    <td data-label="%PIS">{{ $item->perc_pis }}</td>
                                    <td data-label="%COFINS">{{ $item->perc_cofins }}</td>
                                    <td data-label="%IPI">{{ $item->perc_ipi }}</td>
                                    <td data-label="CST/CSOSN">{{ $item->cst_csosn }}</td>
                                    <td data-label="CST PIS">{{ $item->cst_pis }}</td>
                                    <td data-label="CST COFINS">{{ $item->cst_cofins }}</td>
                                    <td data-label="CST IPI">{{ $item->cst_ipi }}</td>

                                    <td>
                                        <form action="{{ route('produtopadrao-tributacao.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('config_produto_fiscal_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('produtopadrao-tributacao.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('config_produto_fiscal_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
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
                        @can('config_produto_fiscal_delete')
                        <form action="{{ route('produtopadrao-tributacao.destroy-select') }}" method="post" id="form-delete-select">
                            @method('delete')
                            @csrf
                            <div></div>
                            <button type="button" class="btn btn-danger btn-sm btn-delete-all" disabled>
                                <i class="ri-close-circle-line"></i> Remover selecionados
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
                <br>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript" src="/js/delete_selecionados.js"></script>
@endsection
