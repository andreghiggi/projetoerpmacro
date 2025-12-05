@extends('layouts.app', ['title' => 'Gerenciar Planos'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    <button class="btn btn-success btn-cad" data-bs-toggle="modal" data-bs-target="#modal-cad">
                        <i class="ri-add-circle-fill"></i>
                        Atribuir Plano
                    </button>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}

                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('empresa', 'Pesquisar por empresa')
                            ->options($empresa ? [$empresa->id => $empresa->info] : [])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('gerenciar-planos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Empresa</th>
                                    <th>Plano</th>
                                    <th>Valor</th>
                                    <th>Forma de pagamento</th>
                                    <th>Data de cadastro</th>
                                    <th>Data de expiração</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>

                                    <td>{{ $item->empresa ? $item->empresa->info : 'Empresa removida' }}</td>
                                    <td>{{ $item->plano->nome }}</td>
                                    <td>{{ __moeda($item->valor) }}</td>
                                    <td>
                                        {{ $item->forma_pagamento }}
                                    </td>

                                    <td>{{ __data_pt($item->created_at, 1) }}</td>
                                    <td>{{ __data_pt($item->data_expiracao, 0) }}</td>
                                    <td>

                                        <form action="{{ route('gerenciar-planos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('gerenciar-planos.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>

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

<div class="modal fade" id="modal-cad" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form class="modal-content" method="post" action="{{ route('gerenciar-planos.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Atribuir plano</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        {!!Form::select('empresa_atribuir', 'Empresa')
                        ->required()
                        ->attrs(['class' => 'select2 empresa'])
                        !!}
                    </div>

                    <div class="col-md-3">

                        <label>Plano</label>
                        <select required id="plano" name="plano_id" class="form-select">
                            <option value="">Selecione</option>
                            @foreach($planos as $p)
                            <option value="{{ $p->id }}" data-valor="{{ $p->valor }}">{{ $p->nome }} R$ {{ __moeda($p->valor)}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        {!!Form::select('forma_pagamento', 'Tipo de pagamento', \App\Models\Plano::formasPagamento())
                        ->required()
                        ->attrs(['class' => 'select2'])
                        !!}
                    </div>

                    <div class="col-md-3 mt-2">
                        {!!Form::tel('valor', 'Valor')
                        ->required()
                        ->attrs(['class' => 'moeda'])
                        !!}
                    </div>

                    <div class="col-md-3 mt-2">
                        {!!Form::select('status_pagamento', 'Status de pagamento', \App\Models\FinanceiroPlano::statusDePagamentos())
                        ->required()
                        ->attrs(['class' => 'select2'])
                        ->value('recebido')
                        !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript">
    $(function(){
        setTimeout(() => {
            $("#modal-cad .empresa").select2({
                minimumInputLength: 2,
                language: "pt-BR",
                placeholder: "Digite para buscar a empresa",
                width: "100%",
                theme: "bootstrap4",
                dropdownParent: $('#modal-cad'),
                ajax: {
                    cache: true,
                    url: path_url + "api/empresas/find-all",
                    dataType: "json",
                    data: function (params) {

                        var query = {
                            pesquisa: params.term,
                        };
                        return query;
                    },
                    processResults: function (response) {
                        var results = [];

                        $.each(response, function (i, v) {
                            var o = {};
                            o.id = v.id;

                            o.text = v.info;
                            o.value = v.id;
                            results.push(o);
                        });
                        return {
                            results: results,
                        };
                    },
                },
            });
        }, 200)

    });

    $(document).on("change", "#plano", function () {
        if($(this).val()){
            let empresa_id = $('#inp-empresa_atribuir').val()

            $.get(path_url + 'api/planos/find', {empresa_id: empresa_id, plano_id: $(this).val()})
            .done((res) => {
                console.log(res)
                $('#inp-valor').val(convertFloatToMoeda(res.valor))
            })
            .fail((err) => {
                console.log(err)
                swal("Erro", "Algo deu errado", "error")
            })
        }else{
            $('#inp-valor').val(convertFloatToMoeda(0))
        }
    });

</script>
@endsection
