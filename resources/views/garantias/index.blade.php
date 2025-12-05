@extends('layouts.app', ['title' => 'Garantias'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('garantias_create')
                    <a href="{{ route('garantias.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Garantia
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('produto_id', 'Filtrar por produto')
                            ->options($produto != null ? [$produto->id => $produto->nome] : [])
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::select('cliente_id', 'Filtrar por cliente')
                            ->options($cliente != null ? [$cliente->id => $cliente->razao_social] : [])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('status', 'Status', ['' => 'Todos'] + \App\Models\Garantia::estados())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::date('data_venda', 'Filtrar por data da venda')
                            !!}
                        </div>
                        <div class="col-md-4 col-xl-2 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('garantias.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produto/Serviço</th>
                                    <th>Cliente</th>
                                    <th>Data de cadastro</th>
                                    <th>Data da venda</th>
                                    <th>Data da expiração</th>
                                    <th>Status</th>

                                    <th>Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Produto/Serviço">
                                        @if($item->produto)
                                        {{ $item->produto->nome }}
                                        @elseif($item->servico)
                                        {{ $item->servico->nome }}
                                        @endif
                                    </td>
                                    <td data-label="Cliente">{{ $item->cliente->razao_social }}</td>
                                    <td data-label="Data da venda">{{ __data_pt($item->created_at) }}</td>
                                    <td data-label="Data da venda">{{ __data_pt($item->data_venda, 0) }}</td>
                                    <td data-label="Data da expiração">{{ __data_pt($item->dataValidade(), 0) }}</td>
                                    <td data-label="Status">{!! $item->statusFormatado() !!}</td>
                                    <td>
                                        <form style="width: 150px;" action="{{ route('garantias.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('garantias_edit')
                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('garantias.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('garantias_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan

                                            <button type="button" class="btn btn-light btn-sm" onclick="openModal('{{ $item->id }}')">
                                                <i class="ri-eye-2-line"></i>
                                            </button>

                                            <button type="button" onclick="imprimir('{{$item->id}}')" class="btn btn-primary btn-sm" title="Imprimir Garantia">
                                                <i class="ri-printer-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nada encontrado</td>
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
@include('garantias.partials.modal_info')

@endsection
@section('js')
<script type="text/javascript">
    function openModal(id){
        $.get(path_url + "garantias-modal/"+id)
        .done((data) => {
            $('#modal-info').modal('show')
            $('#modal-info .modal-content').html(data)
        })
        .fail((e) => {
            console.log(e)
        })
    }

    function imprimir(id){
        var disp_setting="toolbar=yes,location=no,";
        disp_setting+="directories=yes,menubar=yes,";
        disp_setting+="scrollbars=yes,width=850, height=600, left=100, top=25";

        var docprint=window.open(path_url+"garantias/imprimir/"+id, "",disp_setting);

        docprint.focus();
    }
</script>
@endsection
