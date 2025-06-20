<div class="row g-2">
    <div class="col-md-2">
        {!!Form::text('pagina', 'Página')->required()
        !!}
    </div>
    <div class="col-md-5">
        {!!Form::text('url_servidor', 'URL servidor')->required()
        !!}
    </div>
    <div class="col-md-5">
        {!!Form::text('url_video', 'URL vídeo')->required()
        !!}
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>