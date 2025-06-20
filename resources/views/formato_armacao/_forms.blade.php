<div class="row g-2">

    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')->required()
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::select('status', 'Status', [1 => 'Sim', 0 => 'Não'])->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-12"></div>

    <div class="card col-md-3 mt-3 form-input">
        <div class="preview">
            <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>
            @isset($item)
            <img id="file-ip-1-preview" src="{{ $item->img }}">
            @else
            <img id="file-ip-1-preview" src="/imgs/no-image.png">
            @endif
        </div>
        <label for="file-ip-1">Imagem</label>

        <input type="file" id="file-ip-1" name="image" accept="image/*" onchange="showPreview(event);">
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>