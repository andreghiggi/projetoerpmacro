@extends('layouts.app', ['title' => 'Localizações'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    @can('localizacao_create')
                    <a href="{{ route('localizacao.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Localização
                    </a>
                    @endcan

                </div>

                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Descrição</th>
                                    <th>Razão Social/Nome</th>
                                    <th>CPF/CNPJ</th>
                                    <th>Cidade</th>
                                    <th>Endereço</th>
                                    <th>CEP</th>
                                    <th>Status</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Descrição">{{ $item->descricao }}</td>
                                    <td data-label="Razão Social/Nome">{{ $item->nome }}</td>
                                    <td data-label="CPF/CNPJ">{{ $item->cpf_cnpj }}</td>
                                    <td data-label="Cidade">{{ $item->cidade ? $item->cidade->info : '' }}</td>
                                    <td data-label="Endereço">{{ $item->rua ? $item->endereco : '--' }}</td>
                                    <td data-label="CEP">{{ $item->cep }}</td>
                                    <td data-label="Status">
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('localizacao.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 150px;">
                                            @method('delete')
                                            @can('localizacao_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('localizacao.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @if(!$loop->first)
                                            @can('localizacao_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                            @endif
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
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

