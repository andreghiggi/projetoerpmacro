<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')->attrs(['class' => ''])->required()
        !!}
    </div>

    @if(__isActivePlan(Auth::user()->empresa, 'Delivery'))
    <div class="col-md-2">
        {!!Form::select('marketplace', 'Marketplace', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select tooltipp2'])
        !!}
        <div class="text-tooltip2 d-none">
            Marcar como sim se for usar esta categoria no Delivery/Marketplace
        </div>
    </div>
    @endif
    <!-- <hr> -->
    <!-- <div class="card col-md-3 mt-3 form-input">
        <div class="preview">
            <h5>Selecione uma imagem</h5>
            <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>
            @isset($item)
            <img id="file-ip-1-preview" src="{{ $item->img }}">
            @else
            <img id="file-ip-1-preview" src="/imgs/no-image.png">
            @endif
        </div>
        <label for="file-ip-1">Imagem</label>
        <input type="file" id="file-ip-1" name="image" accept="image/*" onchange="showPreview(event);">
    </div> -->
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
