<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')
        ->value(isset($item) ? $item->name : '')
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('slug', 'Slug')
        ->value(isset($item) ? $item->slug : '')
        ->required()
        !!}
    </div>

    <div class="col-md-6">
        {!!Form::textarea('descricao', 'Descrição')
        ->value(isset($item) ? $item->description : '')
        !!}
    </div>

    <div class="card col-md-3 mt-3 form-input">
        <div class="preview">
            <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>
            @if(isset($item) && $item->image != null)
            <img id="file-ip-1-preview" src="{{ $item->image->src }}">
            @else
            <img id="file-ip-1-preview" src="/imgs/no-image.png">
            @endif
        </div>
        <label for="file-ip-1">Imagem</label>
        
        <input type="file" id="file-ip-1" name="image" accept="image/*" onchange="showPreview(event);">
    </div>


    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>