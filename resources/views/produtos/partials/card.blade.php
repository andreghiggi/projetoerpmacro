<div class="row g-2 mt-3">
  <h6>Total de registros: <strong>{{ $data->total() }}</strong></h6>
  <div class="form-check form-checkbox-danger mb-2">
    <input class="form-check-input" type="checkbox" id="select-all-checkbox"> <label class="text-muted">Selecionar todos</label>
  </div>
  @foreach ($data as $item)
  <div class="col-md-3 col-xl-2 col-sm-4 col-12">
    <div class="card border-0 shadow-sm h-100 produto-card">

      <div class="img-wrapper">
        <img src="{{ $item->img }}" class="card-img-top produto-img" alt="{{ $item->nome }}">
      </div>

      <div class="card-body text-center">
        <h6 class="card-title fw-bold text-dark mb-1">
          <div class="form-check form-checkbox-danger mb-2">
            <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item->id }}">
            {{ $item->nome }}
          </div>
        </h6>
        <p class="card-text text-muted small mb-2">
          <i class="ri-barcode-box-line text-success"></i> {{ $item->codigo_barras ?? '--' }}
        </p>
        <h5 class="text-success fw-bold mb-0">
          R$ {{ __moeda($item->valor_unitario) }}
        </h5>

        @if($item->status)
        <span class="badge bg-success mt-1">Ativo</span>
        @else
        <span class="badge bg-danger mt-1">Desativado</span>
        @endif
      </div>

      <div class="card-footer bg-transparent border-0 text-center" style="margin-top: -20px;">
        <form action="{{ route('produtos.destroy', $item->id) }}" method="post" id="form-{{ $item->id }}">
          @method('delete')
          @csrf
          @can('produtos_edit')
          <a href="{{ route('produtos.edit', $item->id) }}" title="Editar produto" class="btn btn-warning btn-sm me-1">
            <i class="ri-pencil-fill"></i>
          </a>
          @endcan

          @can('produtos_delete')
          <button type="button" class="btn btn-delete btn-sm btn-danger me-1">
            <i class="ri-delete-bin-line"></i>
          </button>
          @endcan

          <button type="button" class="btn btn-light btn-sm" onclick="openModal('{{ $item->id }}')">
            <i class="ri-eye-2-line"></i>
          </button>
        </form>
      </div>
    </div>
  </div>


  @endforeach
</div>
