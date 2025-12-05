@extends('layouts.app', ['title' => 'Configuração de TEF'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    <a href="{{ route('tef-config.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Configuração
                    </a>
                </div>
                <hr class="mt-3">
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Usuário</th>
                                    <th>CNPJ</th>
                                    <th>PDV</th>
                                    <th>Status</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Usuário">{{ $item->usuario->name }}</td>
                                    <td data-label="CNPJ">{{ $item->cnpj }}</td>
                                    <td data-label="PDV">{{ $item->pdv }}</td>
                                    <td data-label="Status">
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('tef-config.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 200px">
                                            @method('delete')
                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('tef-config.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @csrf
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @if($item->status)
                                            <button id="btn-status" title="Consultar Status" type="button" class="btn btn-sm btn-dark">
                                                <i class="ri-checkbox-circle-line"></i>
                                            </button>
                                            @endif
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

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript">

    $('body').on('click', '#btn-status', function () {
        $.get(path_url + "api/tef/verifica-ativo",
        {
            empresa_id: $('#empresa_id').val(),
            usuario_id: $('#usuario_id').val(),
        })
        .done((data) => {
            console.log(data);
            swal("Sucesso", "TEF Ativo", "success")
        })
        .fail((e) => {
            console.log(e);
            swal("Erro", e.responseJSON, "error")
        });
    })

</script>
@endsection
