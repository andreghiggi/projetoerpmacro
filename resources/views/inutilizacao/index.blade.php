@extends('layouts.app', ['title' => 'Inutilização ' . ($modelo == '55' ? 'NFe' : 'NFCe')])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-cad">
                        <i class="fa fa-plus"></i>
                        Nova Inutilização {{$modelo == '55' ? 'NFe' : 'NFCe'}}
                    </button>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final')
                            !!}
                        </div>

                        <div class="col-md-2">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('nfe.inutilizar') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                            
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Número inicial</th>
                                    <th>Número final</th>
                                    <th>Número série</th>
                                    <th>Modelo</th>
                                    <th>Estado</th>
                                    <th>Justificativa</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td data-label="Número inicial">{{ $item->numero_inicial }}</td>
                                    <td data-label="Número final">{{ $item->numero_final }}</td>
                                    <td data-label="Número série">{{ $item->numero_serie }}</td>
                                    <td data-label="Modelo">{{ $item->modelo == '55' ? 'NFe' : 'NFCe' }}</td>
                                    <td data-label="Estado">
                                        @if($item->estado == 'aprovado')
                                        <span class="btn btn-success text-white btn-sm">aprovado</span>
                                        @elseif($item->estado == 'rejeitado')
                                        <span class="btn btn-warning text-white btn-sm">rejeitado</span>
                                        @else
                                        <span class="btn btn-info text-white btn-sm">novo</span>
                                        @endif
                                    </td>
                                    <td data-label="Justificativa">{{ $item->justificativa }}</td>
                                    <td data-label="Data">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>

                                    <td>
                                        <form style="width: 100px;" action="{{ route('nfe-inutilizar.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            @if($item->estado == 'novo' || $item->estado == 'rejeitado')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>

                                            <button title="Transmitir Inutilização" type="button" class="btn btn-success btn-sm" onclick="transmitir('{{$item->id}}')">
                                                <i class="ri-send-plane-fill"></i>
                                            </button>
                                            @endif
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

<div class="modal fade" id="modal-cad" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form class="modal-content" method="post" action="{{ $modelo == '55' ? route('nfe-inutilizar.store') : route('nfce-inutilizar.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Inutilização {{$modelo == '55' ? 'NFe' : 'NFCe'}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-3">
                        {!!Form::tel('numero_inicial', 'Número inicial')
                        ->required()
                        !!}
                    </div>

                    <div class="col-md-3">
                        {!!Form::tel('numero_final', 'Número final')
                        ->required()
                        !!}
                    </div>

                    <div class="col-md-3">
                        {!!Form::tel('numero_serie', 'Número série')
                        ->required()
                        !!}
                    </div>

                    <div class="col-md-12 mt-3">
                        {!!Form::tel('justificativa', 'Justificativa')
                        ->required()
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
    function transmitir(id){
        console.clear()
        $.post(path_url + "api/nfe_painel/inutilizar", {
            id: id,
        })
        .done((success) => {
            swal("Sucesso", success, "success")
            .then(() => {
                location.reload()
            })
        })
        .fail((err) => {
            console.log(err)
            swal("Algo deu errado", err.responseJSON, "error")
            
            .then(() => {
                location.reload()
            })

        })
    }

</script>

@endsection
