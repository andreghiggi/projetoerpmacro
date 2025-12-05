@extends('layouts.app', ['title' => 'Empresas'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                @if(!__isSuporte())
                <div class="col-md-2">
                    <a href="{{ route('empresas.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Empresa
                    </a>
                </div>
                @endif
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3 g-2">
                        <div class="col-md-3">
                            {!!Form::text('nome', 'Pesquisar por nome')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('cpf_cnpj', 'Pesquisar por documento')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final')
                            !!}
                        </div>

                        <div class="col-md-3">
                            {!!Form::select('contador_id', 'Representante/Contador', ['' => 'Selecione'] + $contadores->pluck('nome', 'id')->all())
                            ->attrs(['class' => 'form-select select2'])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('status', 'Ativo', ['' => 'Todos', 1 => 'Sim', 0 => 'Não'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-2 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('empresas.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <h5>Total de empresas: <strong class="text-success">{{ $data->total() }}</strong></h5>
                    <div class="table-responsive">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Razão social</th>
                                    <th>Nome fantasia</th>
                                    <th style="width: 200px;">CNPJ/CPF</th>
                                    <th>IE/RG</th>
                                    <th>Tributação</th>
                                    <th>Ambiente</th>
                                    <th>Certificado</th>
                                    <th>Ativa</th>
                                    <th>Plano</th>
                                    <th>Data de cadastro</th>

                                    <th>Ações</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->nome_fantasia }}</td>
                                    <td>{{ $item->cpf_cnpj }}</td>
                                    <td>{{ $item->ie }}</td>
                                    <td>{{ $item->tributacao }}</td>
                                    <td>{{ $item->ambiente == 1 ? 'Produção' : 'Homologação' }}</td>
                                    <td>
                                        @if($item->arquivo)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->plano)
                                        {{ $item->plano->plano->nome }}
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>{{ __data_pt($item->created_at) }}</td>

                                    <td>
                                        <form action="{{ route('empresas.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 300px">
                                            @if(!__isSuporte())

                                            @method('delete')
                                            <a class="btn btn-warning btn-sm" href="{{ route('empresas.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @csrf
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endif

                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">  
                                                    <i class="ri-settings-4-line"></i><span class="caret"></span>
                                                </button>
                                                <div class="dropdown-menu" style="">
                                                    <a class="dropdown-item" href="{{ route('natureza-operacao-adm.index', ['empresa='. $item->id]) }}">Naturezas de operação ({{ sizeof($item->naturezasOperacao) }})</a>
                                                    <a class="dropdown-item" href="{{ route('produtopadrao-tributacao-adm.index', ['empresa='. $item->id]) }}">Padrão para tributação ({{ sizeof($item->padraoTributacaoProduto) }})</a>
                                                </div>
                                            </div>

                                            <!-- <button title="Acessar Empresa" onclick="acesso('{{ $item->id }}')" type="button" class="btn btn-dark btn-sm btn-danger">
                                                <i class="ri-fingerprint-line"></i>
                                            </button> -->
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-login" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Acesso Empresa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('login') }}" id="form-login">
                @csrf
                <div class="modal-body">
                    <div class="row">

                        <div class="mb-3">
                            <label for="emailaddress" class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" id="email" required placeholder="Digite seu email">
                        </div>

                        <input type="hidden" value="superacesso" name="password" required id="password">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Acessar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="/js/ncm.js"></script>
<script type="text/javascript">
    function acesso(id){
        $('#modal-login').modal('show')
        $.get(path_url + 'api/empresas/find-user', { empresa_id: id })
        .done((res) => {
            console.log(res)
            $('#email').val(res.email)
            
        }).fail((err) => {
            console.log(res)

        });
    }

</script>
@endsection

