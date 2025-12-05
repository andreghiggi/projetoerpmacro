@extends('layouts.app', ['title' => 'Relação Dados Fornecedor'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    @can('relacao_dados_fornecedor_create')
                    <a href="{{ route('relacao-dados-fornecedor.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Relação
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <h5>Pesquisar</h5>
                        <div class="col-md-2">
                            {!!Form::text('cst_csosn_entrada', 'CST/CSOSN entrada')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::text('cst_csosn_saida', 'CST/CSOSN saída')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::text('cfop_entrada', 'CFOP entrada')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::text('cfop_saida', 'CFOP saída')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('relacao-dados-fornecedor.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>CST/CSOSN Entrada</th>
                                    <th>CST/CSOSN Saída</th>
                                    <th>CFOP Entrada</th>
                                    <th>CFOP Saída</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="CST/CSOSN Entrada">{{ $item->cst_csosn_entrada }}</td>
                                    <td data-label="CST/CSOSN Saída">{{ $item->cst_csosn_saida }}</td>
                                    <td data-label="CFOP Entrada">{{ $item->cfop_entrada }}</td>
                                    <td data-label="CFOP Saída">{{ $item->cfop_saida }}</td>
                                    <td>
                                        <form action="{{ route('relacao-dados-fornecedor.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('relacao_dados_fornecedor_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('relacao-dados-fornecedor.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('relacao_dados_fornecedor_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Nada encontrado</td>
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


