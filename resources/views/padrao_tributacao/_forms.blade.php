<div class="row g-2">
    @if(!isset($not_submit))
    <p class="text-danger">
        <i class="ri-information-line"></i> Utilizado no cadastro de produtos, para reduzir o tempo de cadastro
    </p>

    <div class="col-md-4">
        {!!Form::text('descricao', 'Descrição')
        ->required()
        !!}
    </div>
    @endif

    <div class="col-md-2">
        {!!Form::select('padrao', 'Padrão', [0 => 'Não', 1 => 'Sim'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::select('ncm', 'NCM')
        ->options(isset($item) && $item->_ncm ? [$item->ncm => $item->_ncm->descricao] : [])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('cest', 'CEST')
        ->attrs(['class' => 'cest'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('perc_icms', '% ICMS')
        ->attrs(['class' => 'percentual'])
        ->required()
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::tel('perc_pis', '% PIS')
        ->required()
        ->attrs(['class' => 'percentual'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('perc_cofins', '% COFINS')
        ->required()
        ->attrs(['class' => 'percentual'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('perc_ipi', '% IPI')
        ->required()
        ->attrs(['class' => 'percentual'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('perc_red_bc', '% Red BC')
        ->attrs(['class' => 'percentual'])
        !!}
    </div>

    <div class="col-md-6">
        {!!Form::select('cst_csosn', 'CSOSN', ['' => 'Selecione']+App\Models\Produto::listaCSTCSOSN())
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::select('cst_pis', 'CST PIS', ['' => 'Selecione']+App\Models\Produto::listaCST_PIS_COFINS())
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::select('cst_cofins', 'CST COFINS', ['' => 'Selecione']+App\Models\Produto::listaCST_PIS_COFINS())
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::select('cst_ipi', 'CST IPI', ['' => 'Selecione']+App\Models\Produto::listaCST_IPI())
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::select('cEnq', 'Código de enquandramento de IPI', ['' => 'Selecione']+App\Models\Produto::listaCenqIPI())
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::tel('cfop_estadual', 'CFOP Estadual')
        ->required()
        ->attrs(['class' => 'cfop'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('cfop_outro_estado', 'CFOP Inter Estadual')
        ->required()
        ->attrs(['class' => 'cfop'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('cfop_entrada_estadual', 'CFOP Entrada Estadual')
        ->required()
        ->attrs(['class' => 'cfop'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('cfop_entrada_outro_estado', 'CFOP Entrada Inter Estadual')
        ->required()
        ->attrs(['class' => 'cfop'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('codigo_beneficio_fiscal', 'Código benefício')
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::select('modBCST', 'Modalidade BC-ST', App\Models\Produto::modalidadesBCST())
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('pICMSST', '% ICMS ST')
        ->attrs(['class' => 'percentual'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('pMVAST', '% MVA ST')
        ->attrs(['class' => 'percentual'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('redBCST', '% Red BC ST')
        ->attrs(['class' => 'percentual'])
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::select('cst_ibscbs', 'CST IBS/CBS', ['' => 'Selecione'] + App\Models\Produto::listaCSTCbsIbs())
        ->attrs(['class' => 'form-select select2'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('cclass_trib', 'Classificação Tributária')
        ->attrs(['class' => ''])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('perc_ibs_uf', '% IBS UF')
        ->attrs(['class' => 'percentual'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('perc_ibs_mun', '% IBS Municipal')
        ->attrs(['class' => 'percentual'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('perc_cbs', '% CBS')
        ->attrs(['class' => 'percentual'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('perc_dif', '% Diferido')
        ->attrs(['class' => 'percentual'])
        !!}
    </div>

    @if(!isset($not_submit))
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
    @endif
</div>

