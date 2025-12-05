<div class="col-md-12 mt-3 table-responsive">
    <h5>Total de registros: <strong>{{ $data->total() }}</strong></h5>
    <table class="table table-striped table-centered mb-0">
        <thead class="table-dark">
            <tr>
                @can('clientes_delete')
                <th>
                    <div class="form-check form-checkbox-danger mb-2">
                        <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                    </div>
                </th>
                @endcan
                <th>Imagem</th>
                <th>#</th>
                <th>Razão Social</th>
                <th>CPF/CNPJ</th>
                <th>Cidade</th>
                <th>Endereço</th>
                <th>CEP</th>
                <th>Status</th>
                <th>Data de cadastro</th>
                <th width="10%">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                @can('clientes_delete')
                <td>
                    <div class="form-check form-checkbox-danger mb-2">
                        <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item->id }}">
                    </div>
                </td>
                @endcan
                <td><img class="img-60" src="{{ $item->img }}"></td>

                <td data-label="Código">{{ $item->numero_sequencial }}</td>
                <td data-label="Razão social"><label style="width: 300px">{{ $item->razao_social }}</label></td>
                <td data-label="CPF/CNPJ">{{ $item->cpf_cnpj }}</td>
                <td data-label="Cidade"><label style="width: 200px">{{ $item->cidade ? $item->cidade->info : '' }}</label></td>
                <td data-label="Endereço"><label style="width: 300px">{{ $item->rua ? $item->endereco : '--' }}</label></td>
                <td data-label="CEP">{{ $item->cep }}</td>
                <td data-label="Status">
                    @if($item->status)
                    <i class="ri-checkbox-circle-fill text-success"></i>
                    @else
                    <i class="ri-close-circle-fill text-danger"></i>
                    @endif
                </td>
                <td data-label="Data de cadastro"><label style="width: 150px">{{ __data_pt($item->created_at) }}</label></td>
                <td>
                    <form action="{{ route('clientes.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 230px;">
                        @method('delete')
                        @can('clientes_edit')
                        <a class="btn btn-warning btn-sm" href="{{ route('clientes.edit', [$item->id]) }}">
                            <i class="ri-pencil-fill"></i>
                        </a>
                        @endcan

                        @csrf
                        @can('clientes_delete')
                        <button type="button" class="btn btn-delete btn-sm btn-danger">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                        @endcan

                        <a title="Informações de cashBack" class="btn btn-dark btn-sm" href="{{ route('clientes.cash-back', [$item->id]) }}">
                            <i class="ri-coins-fill"></i>
                        </a>

                        <a title="Histórico" class="btn btn-primary btn-sm" href="{{ route('clientes.historico', [$item->id]) }}">
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
                <td colspan="10" class="text-center">Nada encontrado</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
</div>