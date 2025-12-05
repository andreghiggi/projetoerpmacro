@extends('layouts.app', ['title' => 'Fornecedores'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    @can('fornecedores_create')
                    <a href="{{ route('fornecedores.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Fornecedor
                    </a>
                    <a href="{{ route('fornecedores.import') }}" class="btn btn-info pull-right">
                        <i class="ri-file-upload-line"></i>
                        Upload
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3 g-1">
                        <div class="col-md-4 col-xl-2">
                            {!!Form::text('razao_social', 'Pesquisar por nome')
                            !!}
                        </div>
                        <div class="col-md-4 col-xl-2">
                            {!!Form::text('cpf_cnpj', 'Pesquisar por CPF/CNPJ')
                            ->attrs(['class' => 'cpf_cnpj'])
                            ->type('tel')
                            !!}
                        </div>
                        <div class="col-md-4 col-xl-2">
                            {!!Form::date('start_date', 'Data inicial cadastro')
                            !!}
                        </div>
                        <div class="col-md-4 col-xl-2">
                            {!!Form::date('end_date', 'Data final cadastro')
                            !!}
                        </div>
                        <div class="col-md-4 col-xl-2">
                            {!!Form::select('ordem', 'Ordenar por', ['razao_social' => 'Razão social', 'numero_sequencial' => 'Código', 'created_at' => 'Data de cadastro'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-4 col-xl-2 col-12 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('fornecedores.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    @can('fornecedores_delete')
                                    <th>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                        </div>
                                    </th>
                                    @endcan
                                    <th>#</th>
                                    <th>Razão Social</th>
                                    <th>CPF/CNPJ</th>
                                    <th>Cidade</th>
                                    <th>Endereço</th>
                                    <th>CEP</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    @can('fornecedores_delete')
                                    <td>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item->id }}">
                                        </div>
                                    </td>
                                    @endcan
                                    <td data-label="Código">{{ $item->numero_sequencial }}</td>
                                    <td data-label="Razão social">{{ $item->razao_social }}</td>
                                    <td data-label="CPF/CNPJ">{{ $item->cpf_cnpj }}</td>
                                    <td data-label="Cidade">{{ $item->cidade ? $item->cidade->info : '' }}</td>
                                    <td data-label="Endereço">{{ $item->endereco }}</td>
                                    <td data-label="CEP">{{ $item->cep }}</td>
                                    <td>
                                        <form action="{{ route('fornecedores.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 200px;">
                                            @method('delete')
                                            @can('fornecedores_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('fornecedores.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('fornecedores_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan

                                            <a title="Histórico" class="btn btn-primary btn-sm" href="{{ route('fornecedores.historico', [$item->id]) }}">
                                                <i class="ri-file-list-3-fill"></i>
                                            </a>

                                            @can('crm_create')
                                            <button type="button" title="CRM" class="btn btn-light btn-sm" onclick="modalCrm('{{ $item->id }}')">
                                                <i class="ri-user-voice-fill"></i>
                                            </button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <br>
                        @can('fornecedores_delete')
                        <form action="{{ route('fornecedores.destroy-select') }}" method="post" id="form-delete-select">
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
@include('modals._crm')

@endsection
@section('js')
<script type="text/javascript" src="/js/delete_selecionados.js"></script>
<script type="text/javascript">
    function modalCrm(fornecedor_id){
        $('#fornecedor_id').val(fornecedor_id)
        $('#modal_crm').modal('show')
        montaSelect2()
    }
</script>
<script type="text/javascript" src="/js/modal_crm.js"></script>
@endsection
