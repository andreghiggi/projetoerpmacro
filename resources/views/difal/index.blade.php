@extends('layouts.app', ['title' => 'Op. Interestadual - Difal'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('difal_view')
                    <a href="{{ route('difal.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Adicionar
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-2">
                            {!!Form::text('cfop', 'Pesquisar por cfop')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('difal.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>UF</th>
                                    <th>CFOP</th>
                                    <th>% ICMS UF Destino</th>
                                    <th>% ICMS Interno</th>
                                    <th>% ICMS Interestadual UF</th>
                                    <th>% Fundo Combate a Pobreza</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="UF">{{ $item->uf }}</td>
                                    <td data-label="CFOP">{{ $item->cfop }}</td>
                                    <td data-label="% ICMS UF Destino">{{ $item->pICMSUFDest }}</td>
                                    <td data-label="% ICMS Interno">{{ $item->pICMSInter }}</td>
                                    <td data-label="% ICMS Interestadual UF">{{ $item->pICMSInterPart }}</td>
                                    <td data-label="% Fundo Combate a Pobreza">{{ $item->pFCPUFDest }}</td>
                                    <td>
                                        <form style="width: 100px;" action="{{ route('difal.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('difal_edit')
                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('difal.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('difal_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection