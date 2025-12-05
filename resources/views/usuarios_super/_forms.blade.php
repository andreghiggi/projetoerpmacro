<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('name', 'Nome')
        ->attrs(['class' => ''])
        ->required()
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('email', 'Email')
        ->attrs(['class' => ''])
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('admin', 'Admin', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select'])
        ->required()
        !!}
    </div>
    @if(!$item->empresa)
    <div class="col-md-4">
        {!!Form::select('empresa', 'Empresa', ['' => 'Selecione'] + $empresas->pluck('info', 'id')->all())
        ->attrs(['class' => 'form-select select2'])
        !!}
    </div>
    @endif
    
    <div class="col-md-2">
        <label for="">Senha</label>
        <div class="input-group" id="show_hide_password">
            <input type="password" class="form-control" id="senha" name="password" autocomplete="off" @if(isset($senhaCookie)) value="{{$senhaCookie}}" @endif>
            <a class="input-group-text"><i class='ri-eye-line'></i></a>
        </div>
    </div>

    @if(sizeof($roles) > 0)
    <div class="col-md-3">
        {!!Form::select('role_id', 'Controle de acesso', ['' => 'Selecione'] + $roles->pluck('description', 'id')->all())
        ->attrs(['class' => 'select2'])
        ->value((isset($item) && $item->roles && $item->roles->first()) ? $item->roles->first()->id : null)
        ->required()
        !!}
    </div>
    @endif
    <hr>
  
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script>
    $(document).ready(function() {
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("bx-hide");
                $('#show_hide_password i').removeClass("bx-show");
            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass("bx-hide");
                $('#show_hide_password i').addClass("bx-show");
            }
        });
    });

</script>
@endsection
