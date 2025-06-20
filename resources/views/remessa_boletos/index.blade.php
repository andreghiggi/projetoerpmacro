@extends('layouts.app', ['title' => 'Remessas'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-12">
                    <a href="{{ route('remessa-boleto.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Remessa
                    </a>

                    <a href="{{ route('remessa-boleto.import') }}" class="btn btn-dark float-end">
                        <i class="ri-file-upload-line "></i>
                        Importar retorno
                    </a>

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
                            {!!Form::select('conta_boleto_id', 'Conta boleto', ['' => 'Selecione'] + $contasBoleto->pluck('info', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('remessa-boleto.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>

                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nome arquivo</th>
                                    <th>Banco</th>
                                    <th>Data de registro</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td>{{ $item->nome_arquivo }}</td>
                                    <td>{{ $item->contaBoleto->banco }}</td>
                                    <td>{{ __data_pt($item->created_at) }}</td>
                                    
                                    <td>
                                        <form action="{{ route('remessa-boleto.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf
                                            <a class="btn btn-dark btn-sm text-white" href="{{ route('remessa-boleto.download', [$item->id]) }}">
                                                <i class="ri-file-download-line"></i>
                                            </a>

                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>

                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection