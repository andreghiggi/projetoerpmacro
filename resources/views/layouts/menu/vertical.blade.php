<div class="leftside-menu">
    <!-- Brand Logo Light -->
    <a href="/" class="logo logo-light">
        <span class="logo-lg">
            <img class="logo-painel" src="/logo.png" alt="logo">
        </span>
        <span class="logo-sm">
            <img src="/logo-sm.png" alt="small logo">
        </span>
    </a>

    <!-- Brand Logo Dark -->
    <a href="/" class="logo logo-dark">
        <span class="logo-lg">
            <img class="logo-painel" src="/logo.png" alt="dark logo">
        </span>
        <span class="logo-sm">
            <img src="/logo-sm.png" alt="small logo">
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <div class="button-sm-hover" data-bs-toggle="tooltip" data-bs-placement="right" title="Show Full Sidebar">
        <i class="ri-checkbox-blank-circle-line align-middle"></i>
    </div>

    <!-- Full Sidebar Menu Close Button -->
    <div class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </div>

    <!-- Sidebar -left -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!-- Leftbar User -->
        <div class="leftbar-user p-3 text-white">
            <a @if(!__isContador()) href="{{ route('usuarios.profile', Auth::user()->id) }}" @endif class="d-flex align-items-center text-reset">
                <div class="flex-shrink-0">
                    @if(Auth::user()->imagem != null)
                    <img src="{{ Auth::user()->img }}" height="42" class="rounded-circle shadow">
                    @else
                    <img src="/assets/images/users/avatar-4.jpg" height="42" class="rounded-circle shadow">
                    @endif
                </div>
                <div class="flex-grow-1 ms-2">
                    <span class="fw-semibold fs-15 d-block"> {{ Auth::user()->name }}</span>
                    <span class="fs-13">{{ Auth::user()->tipo }}</span>
                </div>
                <div class="ms-auto">
                    <i class="ri-arrow-right-s-fill fs-20"></i>
                </div>
            </a>
        </div>

        <!--- Sidemenu -->
        <ul class="side-nav" id="step4">

            <li class="side-nav-title mt-1"> Menu</li>

            <li class="side-nav-item">
                <a href="{{ route('home') }}" class="side-nav-link">
                    <i class="ri-dashboard-2-fill"></i>
                    <span class="badge bg-success float-end"></span>
                    <span> Home </span>
                </a>
            </li>

            @if(__isSuporte())
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPages2" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-broadcast-line"></i>
                    <span> Suporte </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPages2">
                    <ul class="side-nav-second-level">

                        <li>
                            <a href="{{ route('empresas.index') }}">Empresas</a>
                        </li>

                        <li>
                            <a href="{{ route('segmentos.index') }}">Segmentos</a>
                        </li>
                        <li>
                            <a href="{{ route('cidades.index') }}">Cidades</a>
                        </li>
                        <li>
                            <a href="{{ route('usuario-super.index') }}">Usuários</a>
                        </li>
                        <li>
                            <a href="{{ route('ncm.index') }}">NCM</a>
                        </li>

                        <li>
                            <a href="{{ route('logs.index') }}">Logs</a>
                        </li>

                        <li>
                            <a href="{{ route('ibpt.index') }}">IBPT</a>
                        </li>

                        <li>
                            <a href="{{ route('ticket-super.index') }}">Ticket</a>
                        </li>
                        <li>
                            <a href="{{ route('configuracao-super.index') }}">Configuração</a>
                        </li>
                        <li>
                            <a href="{{ route('notificacao-super.index') }}">Notificações</a>
                        </li>
                        <li>
                            <a href="{{ route('padroes-etiqueta.index') }}">Padrões para etiqueta</a>
                        </li>

                        <li>
                            <a href="{{ route('video-suporte.index') }}">Videos de suporte</a>
                        </li>
                        <li>
                            <a href="{{ route('relatorios-adm.index') }}">Relatórios</a>
                        </li>
                        
                    </ul>

                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPermissao" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-rotate-lock-line"></i>
                    <span> Controle de acesso </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPermissao">
                    <ul class="side-nav-second-level">

                        <li>
                            <a href="{{ route('permissions.index') }}">Permissões</a>
                        </li>
                        <li>
                            <a href="{{ route('roles.index') }}">Atribuições</a>
                        </li>

                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPages1" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-file-mark-fill"></i>
                    <span> Emissões </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPages1">
                    <ul class="side-nav-second-level">

                        <li>
                            <a href="{{ route('nfe-all') }}">NFe</a>
                        </li>
                        <li>
                            <a href="{{ route('nfce-all') }}">NFCe</a>
                        </li>
                        <li>
                            <a href="{{ route('cte-all') }}">CTe</a>
                        </li>
                        <li>
                            <a href="{{ route('mdfe-all') }}">MDFe</a>
                        </li>
                    </ul>
                </div>
            </li>

            @endif
            @if(__isMaster())

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPages2" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-stack-fill"></i>
                    <span> SuperAdmin </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPages2">
                    <ul class="side-nav-second-level">

                        <li>
                            <a href="{{ route('empresas.index') }}">Empresas</a>
                        </li>

                        @if(env("CONTADOR") == 1)
                        <li>
                            <a href="{{ route('contadores.index') }}">Representante/Contador</a>
                        </li>
                        @endif

                        <li>
                            <a href="{{ route('planos.index') }}">Planos</a>
                        </li>
                        <li>
                            <a href="{{ route('segmentos.index') }}">Segmentos</a>
                        </li>
                        <li>
                            <a href="{{ route('cidades.index') }}">Cidades</a>
                        </li>
                        <li>
                            <a href="{{ route('usuario-super.index') }}">Usuários</a>
                        </li>
                        <li>
                            <a href="{{ route('gerenciar-planos.index') }}">Gerenciar planos</a>
                        </li>
                        <li>
                            <a href="{{ route('financeiro-plano.index') }}">Financeiro planos</a>
                        </li>
                        <li>
                            <a href="{{ route('planos-pendentes.index') }}">Planos pendentes</a>
                        </li>
                        <li>
                            <a href="{{ route('ncm.index') }}">NCM</a>
                        </li>
                        <li>
                            <a href="{{ route('natureza-operacao-super.index') }}">Naturezas de operação</a>
                        </li>
                        <li>
                            <a href="{{ route('padrao-tributacao-produto-super.index') }}">Padrões de tributação</a>
                        </li>
                        <li>
                            <a href="{{ route('logs.index') }}">Logs</a>
                        </li>

                        <li>
                            <a href="{{ route('ibpt.index') }}">IBPT</a>
                        </li>

                        <li>
                            <a href="{{ route('ticket-super.index') }}">Ticket</a>
                        </li>
                        <li>
                            <a href="{{ route('configuracao-super.index') }}">Configuração</a>
                        </li>
                        <li>
                            <a href="{{ route('notificacao-super.index') }}">Notificações</a>
                        </li>
                        <li>
                            <a href="{{ route('padroes-etiqueta.index') }}">Padrões para etiqueta</a>
                        </li>

                        <li>
                            <a href="{{ route('video-suporte.index') }}">Videos de suporte</a>
                        </li>
                        <li>
                            <a href="{{ route('relatorios-adm.index') }}">Relatórios</a>
                        </li>
                        <li>
                            <a href="{{ route('contrato-config.index') }}">Configuração de contrato</a>
                        </li>

                        <li>
                            <a href="{{ route('contrato-config.list') }}">Lista de contratos</a>
                        </li>

                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPermissao" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-rotate-lock-line"></i>
                    <span> Controle de acesso </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPermissao">
                    <ul class="side-nav-second-level">

                        <li>
                            <a href="{{ route('permissions.index') }}">Permissões</a>
                        </li>
                        <li>
                            <a href="{{ route('roles.index') }}">Atribuições</a>
                        </li>

                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPages1" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-file-mark-fill"></i>
                    <span> Emissões </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPages1">
                    <ul class="side-nav-second-level">

                        <li>
                            <a href="{{ route('nfe-all') }}">NFe</a>
                        </li>
                        <li>
                            <a href="{{ route('nfce-all') }}">NFCe</a>
                        </li>
                        <li>
                            <a href="{{ route('cte-all') }}">CTe</a>
                        </li>
                        <li>
                            <a href="{{ route('mdfe-all') }}">MDFe</a>
                        </li>
                    </ul>
                </div>
            </li>

            @if(env("MARKETPLACE") == 1)
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarMarketPlace" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-store-2-line"></i>
                    <span>Delivery/Marketplace</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarMarketPlace">
                    <ul class="side-nav-second-level">

                        <li>
                            <a href="{{ route('bairros-super.index') }}">Bairros</a>
                        </li>

                    </ul>
                </div>
            </li>
            @endif

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarFinanceiroBoleto" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-coins-line"></i>
                    <span>Financeiro Boletos</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarFinanceiroBoleto">
                    <ul class="side-nav-second-level">

                        <li>
                            <a href="{{ route('financeiro-boleto.index') }}">Listar</a>
                        </li>

                        <li>
                            <a href="{{ route('financeiro-boleto.gerar') }}">Gerador</a>
                        </li>

                        <li>
                            <a href="{{ route('financeiro-boleto.logs') }}">Logs</a>
                        </li>

                    </ul>
                </div>
            </li>

            @if(env("APP_ENV") != "demo")
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarAtualizacao" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-refresh-fill"></i>
                    <span>Atualização </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarAtualizacao">
                    <ul class="side-nav-second-level">

                        <li>
                            <a href="{{ route('update-sql.index') }}">Banco de dados</a>
                        </li>
                        <li>
                            <a href="{{ route('update-file.index') }}">Diretórios</a>
                        </li>
                    </ul>
                </div>
            </li>

            @endif

            <li class="side-nav-item">
                <a href="{{ route('registro') }}" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-bookmark-3-fill"></i>
                    <span>Registro de Software</span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('sugestao.index') }}" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-code-s-slash-line"></i>
                    <span>Susgestão e Desenvolvimento</span>
                </a>
            </li>

            @endif

            @if(!__isMaster())

            @if(__isActivePlan(Auth::user()->empresa, 'Produtos'))
            @canany(['produtos_view', 'categoria_produtos_view', 'inventario_view', 'lista_preco_view'])

            <li class="side-nav-item" id="step7">
                <a data-bs-toggle="collapse" href="#sidebarExtendedProd" aria-expanded="false" aria-controls="sidebarExtendedUI" class="side-nav-link">
                    <i class="ri-box-2-line"></i>
                    <span> Produtos </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarExtendedProd">
                    <ul class="side-nav-second-level">

                        @can('categoria_produtos_view')
                        <li>
                            <a href="{{ route('categoria-produtos.index') }}">Categorias</a>
                        </li>
                        @endcan

                        @can('produtos_view')
                        <li>
                            <a href="{{ route('produtos.index', ['status=1']) }}">Listar</a>
                        </li>
                        @endcan
                        @can('produtos_create')
                        <li>
                            <a href="{{ route('produtos.create') }}">Novo Produto</a>
                        </li>
                        @endcan

                        @can('estoque_view')
                        <li>
                            <a href="{{ route('estoque.index') }}">Estoque</a>
                        </li>
                        @endcan

                        @can('inventario_view')
                        <li>
                            <a href="{{ route('inventarios.index') }}">Inventário</a>
                        </li>
                        @endcan

                        @can('variacao_view')
                        <li>
                            <a href="{{ route('variacoes.index') }}">Variações</a>
                        </li>
                        @endcan

                        @can('lista_preco_view')
                        <li>
                            <a href="{{ route('lista-preco.index') }}">Lista de preços</a>
                        </li>
                        @endcan

                        @can('promocao_produtos_view')
                        <li>
                            <a href="{{ route('promocao-produtos.index') }}">Promoção</a>
                        </li>
                        @endcan

                        @if(__isPlanoFiscal())
                        @can('config_produto_fiscal_view')
                        <li>
                            <a href="{{ route('produtopadrao-tributacao.index') }}">Configuração Padrão Fiscal</a>
                        </li>
                        @endcan
                        @endif

                        @can('marcas_view')
                        <li>
                            <a href="{{ route('marcas.index') }}">Marcas</a>
                        </li>
                        @endcan

                        @can('garantias_view')
                        <li>
                            <a href="{{ route('garantias.index') }}">Garantias</a>
                        </li>
                        @endcan

                        <li>
                            <a href="{{ route('modelo-etiquetas.index') }}">Modelos de Etiqueta</a>
                        </li>

                        <li>
                            <a href="{{ route('produto-consulta-codigo.index') }}">Consulta código</a>
                        </li>

                        @can('transferencia_estoque_view')
                        <li>
                            <a href="{{ route('transferencia-estoque.index') }}">Transferência de estoque</a>
                        </li>
                        @endcan

                        @can('unidade_medida_view')
                        <li>
                            <a href="{{ route('unidades-medida.index') }}">Unidades de medida</a>
                        </li>
                        @endcan

                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Atendimento'))
            @canany(['atendimentos_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarAtendimento" aria-expanded="false" aria-controls="sidebarAtendimento" class="side-nav-link">
                    <i class="ri-store-2-line"></i>
                    <span> Atendimento </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarAtendimento">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('atendimentos.index') }}">Dias de Atendimento</a>
                        </li>
                        <li>
                            <a href="{{ route('interrupcoes.index') }}">Interrupções</a>
                        </li>
                        <li>
                            <a href="{{ route('funcionamentos.index') }}">Horário de Funcionamento</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Serviços'))
            @canany(['servico_view', 'categoria_servico_view', 'ordem_servico_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarExtendedServ" aria-expanded="false" aria-controls="sidebarExtendedSer" class="side-nav-link">
                    <i class="ri-tools-fill"></i>
                    <span> Serviços </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarExtendedServ">
                    <ul class="side-nav-second-level">

                        @can('categoria_servico_view')
                        <li>
                            <a href="{{ route('categoria-servico.index') }}">Categorias</a>
                        </li>
                        @endcan

                        @can('servico_view')
                        <li>
                            <a href="{{ route('servicos.index') }}">Listar</a>
                        </li>
                        @endcan

                        @can('servico_create')
                        <li>
                            <a href="{{ route('servicos.create') }}">Novo serviço</a>
                        </li>
                        @endcan

                        @can('garantias_view')
                        <li>
                            <a href="{{ route('garantias.index') }}">Garantias</a>
                        </li>
                        @endcan

                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Serviços'))
            @canany(['ordem_servico_view'])

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarOs" aria-expanded="false" aria-controls="sidebarExtendedSer" class="side-nav-link">
                    <i class="ri-ruler-2-line"></i>
                    <span> Ordem de Serviço </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarOs">
                    <ul class="side-nav-second-level">
                        @can('ordem_servico_view')
                        <li>
                            <a href="{{ route('ordem-servico.index') }}">Listar</a>
                        </li>
                        @endcan

                        @can('ordem_servico_create')
                        <li>
                            <a href="{{ route('ordem-servico.create') }}">Nova OS</a>
                        </li>
                        @endcan

                        @if(__isSegmentoPlanoOtica())
                        @can('convenio_view')
                        <li>
                            <a href="{{ route('convenios.index') }}">Convênios</a>
                        </li>
                        @endcan

                        @can('medico_view')
                        <li>
                            <a href="{{ route('medicos.index') }}">Médicos</a>
                        </li>
                        @endcan

                        @can('laboratorio_view')
                        <li>
                            <a href="{{ route('laboratorios.index') }}">Laboratórios</a>
                        </li>
                        @endcan

                        @can('tratamento_otica_view')
                        <li>
                            <a href="{{ route('tratamentos-otica.index') }}">Tratamentos ótica</a>
                        </li>
                        @endcan

                        @can('formato_armacao_view')
                        <li>
                            <a href="{{ route('formato-armacao.index') }}">Formatos de armação</a>
                        </li>
                        @endcan

                        <li>
                            <a href="{{ route('tipo-armacao.index') }}">Tipos de armação</a>
                        </li>

                        @endif

                        @can('metas_view')
                        <li>
                            <a href="{{ route('ordem-servico.metas') }}">Metas</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Ordem de Produção'))
            @canany(['ordem_producao_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarOrdemProducao" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-pencil-ruler-line"></i>

                    <span>Ordem de Produção</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarOrdemProducao">
                    <ul class="side-nav-second-level">

                        @can('ordem_producao_view')
                        <li>
                            <a href="{{ route('ordem-producao.index') }}">Listar</a>
                        </li>
                        @endcan

                        @can('ordem_producao_create')
                        <li>
                            <a href="{{ route('ordem-producao.create') }}">Nova Ordem</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Gestão de Produção'))
            @canany(['gestao_producao_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarGestaoProducao" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-compasses-fill "></i>

                    <span>Gestão de Produção</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarGestaoProducao">
                    <ul class="side-nav-second-level">

                        @can('gestao_producao_view')
                        <li>
                            <a href="{{ route('gestao-producao.index') }}">Listar</a>
                        </li>
                        @endcan

                        @can('gestao_producao_create')
                        <li>
                            <a href="{{ route('gestao-producao.create') }}">Nova Produção</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Agendamentos'))
            @canany(['agendamento_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarAgendamentos" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-calendar-event-fill"></i>

                    <span>Agendamentos</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarAgendamentos">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('agendamentos.index') }}">Listar</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Usuários'))
            @canany(['usuarios_view', 'controle_acesso_view'])
            <li class="side-nav-item" id="step6">
                <a data-bs-toggle="collapse" href="#sidebarUsuarios" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-user-fill"></i>

                    <span>Usuários</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarUsuarios">
                    <ul class="side-nav-second-level">
                        @can('usuarios_view')
                        <li>
                            <a href="{{ route('usuarios.index') }}">Listar</a>
                        </li>
                        @endcan
                        @can('controle_acesso_view')
                        <li>
                            <a href="{{ route('controle-acesso.index') }}">Controle de acesso</a>
                        </li>
                        @endcan

                        @can('config_fiscal_usuario_view')
                        <li>
                            <a href="{{ route('config-fiscal-usuario.index') }}">Configuração fiscal</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Pessoas'))
            @canany(['clientes_view', 'fornecedores_view', 'transportadoras_view'])
            <li class="side-nav-item" id="step6">
                <a data-bs-toggle="collapse" href="#sidebarPessoas" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-group-2-fill"></i>

                    <span>Pessoas</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPessoas">
                    <ul class="side-nav-second-level">
                        @can('clientes_view')
                        <li>
                            <a href="{{ route('clientes.index') }}">Clientes</a>
                        </li>
                        @endcan

                        @can('fornecedores_view')
                        <li>
                            <a href="{{ route('fornecedores.index') }}">Fornecedores</a>
                        </li>
                        @endcan

                        @can('transportadoras_view')
                        <li>
                            <a href="{{ route('transportadoras.index') }}">Transportadoras</a>
                        </li>
                        @endcan

                    </ul>
                </div>
            </li>
            @endcanany

            @canany(['funcionario_view', 'apuracao_mensal_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarGestaoPessoal" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-folder-user-line"></i>

                    <span>Gestão Pessoal</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarGestaoPessoal">
                    <ul class="side-nav-second-level">
                        @can('funcionario_view')
                        <li>
                            <a href="{{ route('funcionarios.index') }}">Funcionários</a>
                        </li>
                        @endcan

                        @can('apuracao_mensal_view')
                        <li>
                            <a href="{{ route('evento-funcionarios.index') }}">Eventos</a>
                        </li>
                        <li>
                            <a href="{{ route('funcionario-eventos.index') }}">Funcionários x Eventos</a>
                        </li>
                        <li>
                            <a href="{{ route('apuracao-mensal.index') }}">Apuração Mensal</a>
                        </li>
                        @endcan

                        @can('comissao_margem_view')
                        <li>
                            <a href="{{ route('comissao-margem.index') }}">Comissão por margem</a>
                        </li>
                        @endcan

                    </ul>
                </div>
            </li>
            @endcanany

            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Compras'))
            @canany(['compras_view', 'manifesto_view', 'cotacao_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCompra" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-logout-box-line"></i>

                    <span>Compras</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarCompra">
                    <ul class="side-nav-second-level">
                        @can('compras_view')
                        <li>
                            <a href="{{ route('compras.index') }}">Listar</a>
                        </li>
                        @endcan

                        @can('compras_create')
                        <li>
                            <a href="{{ route('compras.create')}}" data-toggle="fullscreen" class="dropdown-item">Nova
                            </a>
                        </li>
                        @endcan

                        <li>
                            <a href="{{ route('compras.xml')}}" data-toggle="fullscreen" class="dropdown-item">
                                Importar XML
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('compras.validade')}}" data-toggle="fullscreen" class="dropdown-item">
                                Produtos com validade
                            </a>
                        </li>

                        @can('manifesto_view')
                        <li>
                            <a href="{{ route('manifesto.index')}}" data-toggle="fullscreen" class="dropdown-item">
                                Manifesto
                            </a>
                        </li>
                        @endcan

                        @can('cotacao_view')
                        <li>
                            <a href="{{ route('cotacoes.index')}}" data-toggle="fullscreen" class="dropdown-item">
                                Cotação
                            </a>
                        </li>
                        @endcan

                        @if(__isPlanoFiscal())
                        @can('arquivos_xml_view')
                        <li>
                            <a href="{{ route('nfe-entrada-xml.index') }}">Arquivos XML Emitidos</a>
                        </li>
                        @endcan
                        @endif

                        @if(__isPlanoFiscal())
                        @can('arquivos_xml_view')
                        <li>
                            <a href="{{ route('nfe-importa-xml.index') }}">Arquivos XML Importados</a>
                        </li>
                        @endcan
                        @endif

                        @can('relacao_dados_fornecedor_view')
                        <li>
                            <a href="{{ route('relacao-dados-fornecedor.index')}}" data-toggle="fullscreen" class="dropdown-item">
                                Relação dados fornecedor
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(!__isContador())
            @if(__isPlanoFiscal())
            @canany(['devolucao_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarDevolucao" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-arrow-go-back-fill"></i>

                    <span>Devolução</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarDevolucao">
                    <ul class="side-nav-second-level">

                        @can('devolucao_view')
                        <li>
                            <a href="{{ route('devolucao.index')}}" data-toggle="fullscreen" class="dropdown-item">
                                Lista devolução XML
                            </a>
                        </li>
                        @endcan

                        @can('devolucao_create')
                        <li>
                            <a href="{{ route('devolucao.xml')}}" data-toggle="fullscreen" class="dropdown-item">
                                Nova devolução XML
                            </a>
                        </li>
                        @endcan

                        @can('troca_view')
                        <li>
                            <a href="{{ route('trocas.index')}}" data-toggle="fullscreen" class="dropdown-item">Trocas/Devolução</a>
                        </li>
                        @endcan

                    </ul>
                </div>
            </li>
            @endcanany
            @endif
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'PDV'))
            @canany(['pdv_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPDV" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-shopping-cart-fill"></i>

                    <span>PDV</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPDV">
                    <ul class="side-nav-second-level">
                        @can('pdv_view')
                        <li>
                            <a href="{{ route('frontbox.index') }}">Listar</a>
                        </li>
                        @endcan

                        @can('pdv_create')
                        <li class="d-none d-sm-inline-block">
                            <a href="{{ route('frontbox.create')}}" data-toggle="fullscreen" class="dropdown-item">PDV</a>
                        </li>
                        @endcan

                        
                        @if(env("PDVCOMANDA") == 1)
                        @can('pdv_create')
                        <li>
                            <a href="{{ route('frontbox.mesas')}}" data-toggle="fullscreen" class="dropdown-item">Mesas/Comandas</a>
                        </li>
                        @endcan
                        @endif

                        @can('config_tef_view')
                        <li>
                            <a href="{{ route('tef-registros.index') }}">Registros de TEF</a>
                        </li>
                        @endcan

                        @if(__isAdmin())
                        <li>
                            <a href="{{ route('frontbox.logs') }}">Logs de PDV</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Vendas'))
            @canany(['nfe_view', 'orcamento_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarNfe" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-file-list-fill"></i>

                    <span>Vendas</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarNfe">
                    <ul class="side-nav-second-level">
                        @can('nfe_view')
                        <li>
                            <a href="{{ route('vendas.index') }}">Todas as Vendas</a>
                        </li>
                        @endcan

                        @can('nfe_view')
                        <li>
                            <a href="{{ route('nfe.index') }}">Vendas pedido</a>
                        </li>
                        @endcan

                        @can('nfe_create')
                        <li>
                            <a href="{{ route('nfe.create') }}">Nova</a>
                        </li>
                        @endcan

                        @if(__isPlanoFiscal())
                        @can('nfe_inutiliza')
                        <li>
                            <a href="{{ route('nfe.inutilizar') }}">Inutilizar NFe</a>
                        </li>
                        @endcan
                        @endif

                        @can('orcamento_view')
                        <li>
                            <a href="{{ route('orcamentos.index') }}">Orçamentos</a>
                        </li>
                        @endcan

                        @if(__isPlanoFiscal())
                        @can('arquivos_xml_view')
                        <li>
                            <a href="{{ route('nfe-xml.index') }}">Arquivos XML</a>
                        </li>
                        @endcan
                        @endif

                        <li>
                            <a href="{{ route('nfe.import-zip') }}">Importar XML</a>
                        </li>

                        @can('metas_view')
                        <li>
                            <a href="{{ route('nfe.metas') }}">Metas</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Financeiro'))
            @canany(['conta_pagar_view', 'conta_receber_view', 'relatorio_view', 'caixa_view', 'contas_empresa_view', 'contas_boleto_view', 'boleto_view', 'taxa_pagamento_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPagar" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-money-dollar-box-fill"></i>
                    <span>Financeiro</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPagar">
                    <ul class="side-nav-second-level">
                        @can('caixa_view')
                        <li>
                            <a href="{{ route('financeiro.dashboard') }}">Dashboard</a>
                        </li>
                        <li>
                            <a data-bs-toggle="collapse" href="#caixa" aria-expanded="false" aria-controls="caixa">
                                <span> Caixa </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="caixa">
                                <ul class="side-nav-third-level">
                                    <li>
                                        <a href="{{ route('caixa.index') }}">Movimentação</a>
                                    </li>

                                    <li>
                                        <a href="{{ route('caixa.create') }}">Abrir caixa</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('caixa.list') }}">Listar</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endcan

                        @canany(['conta_pagar_view', 'conta_pagar_create'])
                        <li>
                            <a data-bs-toggle="collapse" href="#pagar" aria-expanded="false" aria-controls="pagar">
                                <span> Contas a pagar </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="pagar">
                                <ul class="side-nav-third-level">
                                    @can('conta_pagar_view')
                                    <li>
                                        <a href="{{ route('conta-pagar.index') }}">Listar</a>
                                    </li>
                                    @endcan

                                    @can('conta_pagar_create')
                                    <li>
                                        <a href="{{ route('conta-pagar.create') }}">Nova conta</a>
                                    </li>
                                    @endcan

                                </ul>
                            </div>
                        </li>
                        @endcanany

                        @canany(['conta_receber_view', 'conta_receber_create'])
                        <li>
                            <a data-bs-toggle="collapse" href="#receber" aria-expanded="false" aria-controls="receber">
                                <span> Contas a receber </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="receber">
                                <ul class="side-nav-third-level">
                                    @can('conta_receber_view')
                                    <li>
                                        <a href="{{ route('conta-receber.index') }}">Listar</a>
                                    </li>
                                    @endcan

                                    @can('conta_receber_create')
                                    <li>
                                        <a href="{{ route('conta-receber.create') }}">Nova conta</a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                        @endcanany

                        @can('categoria_conta_view')
                        <li>
                            <a href="{{ route('categoria-conta.index') }}">Categorias de conta</a>
                        </li>
                        @endcan

                        @can('relatorio_view')
                        <li>
                            <a href="{{ route('relatorios.index') }}">Relatórios</a>
                        </li>
                        @endcan

                        @can('taxa_pagamento_view')
                        <li>
                            <a href="{{ route('taxa-cartao.index') }}">Taxas de pagamento</a>
                        </li>
                        @endcan

                        @can('plano_contas_view')
                        <li>
                            <a href="{{ route('plano-contas.index') }}">Plano de contas</a>
                        </li>
                        @endcan

                        @can('contas_empresa_view')
                        <li>
                            <a href="{{ route('contas-empresa.index') }}">Contas da empresa</a>
                        </li>
                        @endcan

                        @can('contas_boleto_view')
                        <li>
                            <a href="{{ route('contas-boleto.index') }}">Contas para boleto</a>
                        </li>
                        @endcan

                        @can('boleto_view')
                        <li>
                            <a href="{{ route('boleto.index') }}">Boletos</a>
                        </li>
                        @endcan

                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'NFCe'))
            @canany(['nfce_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarNfce" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-bill-line"></i>
                    <span>NFCe</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarNfce">
                    <ul class="side-nav-second-level">
                        @can('nfce_view')
                        <li>
                            <a href="{{ route('nfce.index') }}">Listar</a>
                        </li>
                        @endcan

                        @can('nfce_create')
                        <li>
                            <a href="{{ route('nfce.create') }}">Nova</a>
                        </li>
                        @endcan

                        @if(__isPlanoFiscal())
                        @can('nfce_inutiliza')
                        <li>
                            <a href="{{ route('nfce.inutilizar') }}">Inutilizar</a>
                        </li>
                        @endcan

                        @can('arquivos_xml_view')
                        <li>
                            <a href="{{ route('nfce-xml.index') }}">Arquivos XML</a>
                        </li>
                        @endcan
                        @endif

                        <li>
                            <a href="{{ route('nfce.import-zip') }}">Importar XML</a>
                        </li>

                        <li>
                            <a href="{{ route('nfce-contigencia.index') }}">Envio Contingência</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Pré venda'))
            @canany(['pre_venda_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPreVenda" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class=" ri-list-ordered"></i>
                    <span>Pré Venda</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPreVenda">
                    <ul class="side-nav-second-level">
                        @can('pre_venda_view')
                        <li>
                            <a href="{{ route('pre-venda.index') }}">Listar</a>
                        </li>
                        @endcan

                        @can('pre_venda_create')
                        <li>
                            <a href="{{ route('pre-venda.create') }}">Nova</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Cardapio'))
            @can('cardapio_view')
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCardapio" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-restaurant-2-line"></i>
                    <span>Cardápio</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarCardapio">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('config-cardapio.index') }}">Configuração</a>
                        </li>
                        <li>
                            <a href="{{ route('produtos-cardapio.categorias') }}">Categorias</a>
                        </li>

                        <li>
                            <a href="{{ route('produtos-cardapio.index') }}">Produtos</a>
                        </li>

                        <li>
                            <a href="{{ route('categoria-adicional.index') }}">Categorias de adicional</a>
                        </li>
                        <li>
                            <a href="{{ route('adicionais.index') }}">Adicionais</a>
                        </li>

                        <li>
                            <a href="{{ route('pedidos-cardapio.index') }}">Comandas</a>
                        </li>
                        
                        <li>
                            <a href="{{ route('mesas.index') }}">Cadastrar mesas</a>
                        </li>

                        <li>
                            <a href="{{ route('pedido-cozinha.index') }}">Controle de pedidos</a>
                        </li>

                        <li>
                            <a href="{{ route('impressao-pedido.index') }}">Controle de impressão</a>
                        </li>

                        <li>
                            <a href="{{ route('carrossel.index') }}">Carrossel destaque</a>
                        </li>

                        <li>
                            <a href="{{ route('avaliacao-cardapio.index') }}">Avaliações</a>
                        </li>

                        <li>
                            <a href="{{ route('tamanhos-pizza.index') }}">Tamanhos de pizza</a>
                        </li>

                        <li>
                            <a href="{{ route('atendimento-garcom.index') }}">Atendimentos garçom</a>
                        </li>
                        <li>
                            <a href="{{ route('pedidos-cardapio.historico') }}">Histórico</a>
                        </li>


                        @if(file_exists(public_path('app.apk')))
                        <li>
                            <a href="{{ route('config-cardapio.download') }}">Download APP</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endcan
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'NFSe'))
            @canany(['nfse_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarNfse" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-file-code-line"></i>

                    <span>NFSe</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarNfse">
                    <ul class="side-nav-second-level">
                        @can('nfse_view')
                        <li>
                            <a href="{{ route('nota-servico.index') }}">Listar</a>
                        </li>
                        @endcan

                        @can('nfse_create')
                        <li>
                            <a href="{{ route('nota-servico.create') }}">Nova</a>
                        </li>
                        @endcan

                        <li>
                            <a href="{{ route('nota-servico-config.index') }}">Emitente</a>
                        </li>

                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Veiculos'))
            @canany(['veiculos_view', 'veiculos_create'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarVeiculos" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-roadster-line"></i>
                    <span> Veículos </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarVeiculos">
                    <ul class="side-nav-second-level">
                        @can('veiculos_view')
                        <li>
                            <a href="{{ route('veiculos.index') }}">Listar</a>
                        </li>
                        @endcan
                        @can('veiculos_create')
                        <li>
                            <a href="{{ route('veiculos.create') }}">Novo Veículo</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Planejamento de Custos'))
            @canany(['planejamento_custo_view', 'projeto_custo_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPlanejamentoCustos" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-dashboard-fill"></i>
                    <span>Planejamento de Custos</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPlanejamentoCustos">
                    <ul class="side-nav-second-level">

                        @can('projeto_custo_view')
                        <li>
                            <a href="{{ route('projeto-custo.index') }}">Projetos</a>
                        </li>
                        @endcan

                        @can('planejamento_custo_view')
                        <li>
                            <a href="{{ route('planejamento-custo.index') }}">Planejamentos</a>
                        </li>
                        @endcan
                        
                    </ul>
                </div>
            </li>
            @endcan
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Controle de Fretes'))
            @canany(['tipo_despesa_frete_view', 'frete_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarControleFrete" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-road-map-line"></i>
                    <span>Controle de Fretes</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarControleFrete">
                    <ul class="side-nav-second-level">
                        @can('tipo_despesa_frete_view')
                        <li>
                            <a href="{{ route('tipo-despesa-frete.index') }}">Tipos de despesa de frete</a>
                        </li>
                        @endcan
                        @can('frete_view')
                        <li>
                            <a href="{{ route('fretes.index') }}">Fretes</a>
                        </li>
                        @endcan
                        @can('manutencao_veiculo_view')
                        <li>
                            <a href="{{ route('manutencao-veiculos.index') }}">Manutenção de veículos</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'CTe'))
            @canany(['cte_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCte" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-truck-fill"></i>
                    <span>CTe</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarCte">
                    <ul class="side-nav-second-level">
                        @can('cte_view')
                        <li>
                            <a href="{{ route('cte.index') }}">Listar</a>
                        </li>
                        @endcan

                        @can('cte_create')
                        <li>
                            <a href="{{ route('cte.create') }}">Nova</a>
                        </li>
                        @endcan

                        @can('arquivos_xml_view')
                        <li>
                            <a href="{{ route('cte-xml.index') }}">Arquivos XML</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            @canany(['cte_os_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCteOs" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-focus-3-line"></i>
                    <span>CTe Os</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarCteOs">
                    <ul class="side-nav-second-level">
                        @can('cte_os_view')
                        <li>
                            <a href="{{ route('cte-os.index') }}">Listar</a>
                        </li>
                        @endcan

                        @can('cte_os_create')
                        <li>
                            <a href="{{ route('cte-os.create') }}">Nova</a>
                        </li>
                        @endcan

                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'MDFe'))
            @canany(['mdfe_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarMdfe" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-file-lock-line"></i>
                    <span>MDFe</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarMdfe">
                    <ul class="side-nav-second-level">
                        @can('mdfe_view')
                        <li>
                            <a href="{{ route('mdfe.index') }}">Listar</a>
                        </li>
                        @endcan

                        @can('mdfe_create')
                        <li>
                            <a href="{{ route('mdfe.create') }}">Nova</a>
                        </li>
                        @endcan

                        @can('arquivos_xml_view')
                        <li>
                            <a href="{{ route('mdfe-xml.index') }}">Arquivos XML</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(env("ECOMMERCE") == 1)
            @if(__isActivePlan(Auth::user()->empresa, 'Ecommerce'))
            @can('ecommerce_view')
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarEcommerce" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-store-3-line"></i>
                    <span> Ecommerce </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarEcommerce">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('config-ecommerce.index') }}">Configuração</a>
                        </li>
                        <li>
                            <a href="{{ route('produtos-ecommerce.categorias') }}">Categorias de produtos</a>
                        </li>
                        <li>
                            <a href="{{ route('produtos-ecommerce.index') }}">Produtos</a>
                        </li>

                        <li>
                            <a href="{{ route('pedidos-ecommerce.index') }}">Pedidos</a>
                        </li>

                        <li>
                            <a target="_blank" href="{{ route('config-ecommerce.site') }}">Ver site</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan
            @endif
            @endif

            @if(env("MERCADOLIVRE") == 1)
            @if(__isActivePlan(Auth::user()->empresa, 'Mercado Livre'))
            @can('mercado_livre_view')
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarMercadoLivre" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-box-1-line"></i>
                    <span> Mercado Livre </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarMercadoLivre">
                    <ul class="side-nav-second-level">

                        <li>
                            <a href="{{ route('mercado-livre-config.index') }}">Configuração</a>
                        </li>
                        <li>
                            <a href="{{ route('mercado-livre.produtos-news') }}">Produtos</a>
                        </li>
                        <li>
                            <a href="{{ route('mercado-livre-perguntas.index') }}">Perguntas</a>
                        </li>

                        <li>
                            <a href="{{ route('mercado-livre-pedidos.index') }}">Pedidos</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan
            @endif
            @endif

            @if(env("WOOCOMMERCE") == 1)
            @if(__isActivePlan(Auth::user()->empresa, 'Woocommerce'))
            @can('woocommerce_view')
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarWoocommerce" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-wordpress-fill"></i>
                    <span> Woocommerce </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarWoocommerce">
                    <ul class="side-nav-second-level">

                        <li>
                            <a href="{{ route('woocommerce-config.index') }}">Configuração</a>
                        </li>
                        <li>
                            <a href="{{ route('woocommerce-categorias.index') }}">Categorias</a>
                        </li>
                        <li>
                            <a href="{{ route('woocommerce-produtos.index') }}">Produtos</a>
                        </li>
                        <li>
                            <a href="{{ route('woocommerce-pedidos.index') }}">Pedidos</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan
            @endif
            @endif

            @if(env("NUVEMSHOP") == 1)
            @if(__isActivePlan(Auth::user()->empresa, 'Nuvem Shop'))
            @can('nuvem_shop_view')
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarNuvemShop" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-cloud-line"></i>
                    <span> Nuvem Shop </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarNuvemShop">
                    <ul class="side-nav-second-level">

                        <li>
                            <a href="{{ route('nuvem-shop-config.index') }}">Configuração</a>
                        </li>
                        <li>
                            <a href="{{ route('nuvem-shop-categorias.index') }}">Categorias</a>
                        </li>
                        <li>
                            <a href="{{ route('nuvem-shop-produtos.index') }}">Produtos</a>
                        </li>
                        <li>
                            <a href="{{ route('nuvem-shop-pedidos.index') }}">Pedidos</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan
            @endif
            @endif

            @if(env("MARKETPLACE") == 1)
            @if(__isActivePlan(Auth::user()->empresa, 'Delivery'))
            @can('delivery_view')
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarMarketPlace" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-store-2-line"></i>
                    <span>Delivery/Marketplace</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarMarketPlace">
                    <ul class="side-nav-second-level">

                        <li>
                            <a href="{{ route('config-marketplace.index') }}">Configuração</a>
                        </li>

                        <li>
                            <a href="{{ route('pedidos-delivery.index') }}">Pedidos</a>
                        </li>
                        <li>
                            <a href="{{ route('produtos-delivery.categorias') }}">Categorias de produto</a>
                        </li>
                        <li>
                            <a href="{{ route('servico-marketplace.categorias') }}">Categorias de serviço</a>
                        </li>
                        <li>
                            <a href="{{ route('produtos-delivery.index') }}">Produtos</a>
                        </li>
                        <li>
                            <a href="{{ route('servicos-marketplace.index') }}">Serviços</a>
                        </li>
                        <li>
                            <a href="{{ route('funcionamento-delivery.index') }}">Funcionamento</a>
                        </li>

                        <li>
                            <a href="{{ route('bairros-empresa.index') }}">Bairros</a>
                        </li>
                        <li>
                            <a href="{{ route('categoria-adicional.index') }}">Categorias de adicional</a>
                        </li>
                        <li>
                            <a href="{{ route('adicionais.index') }}">Adicionais</a>
                        </li>
                        <li>
                            <a href="{{ route('destaque-marketplace.index') }}">Destaques</a>
                        </li>
                        <li>
                            <a href="{{ route('cupom-desconto.index') }}">Cupom de desconto</a>
                        </li>
                        <li>
                            <a href="{{ route('tamanhos-pizza.index') }}">Tamanhos de pizza</a>
                        </li>
                        <li>
                            <a href="{{ route('motoboys.index') }}">Motoboys</a>
                        </li>

                        <li>
                            <a href="{{ route('pedido-cozinha.index') }}">Controle de pedidos</a>
                        </li>

                        <li>
                            <a href="{{ route('impressao-pedido.index') }}">Controle de impressão</a>
                        </li>

                        <li>
                            <a href="{{ route('clientes-delivery.index') }}">Clientes</a>
                        </li>
                        <li>
                            <a href="{{ route('config-agendamento.index') }}">Configuração de agendamento</a>
                        </li>
                        <li>
                            <a target="_blank" href="{{ route('config-marketplace.loja') }}">Ver loja</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan
            @endif
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Localizações'))
            @canany(['localizacao_view'])
            <li class="side-nav-item" id="step5">
                <a data-bs-toggle="collapse" href="#sidebarLocalizacao" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-building-4-line"></i>
                    <span>Localizações</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarLocalizacao">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('localizacao.index') }}">Listar</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcanany
            @endif

            @if(env("RESERVAS") == 1)
            @if(__isActivePlan(Auth::user()->empresa, 'Reservas'))
            @canany(['categoria_acomodacao_view', 'config_reserva_view', 'reserva_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarReservas" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-hotel-line"></i>
                    <span> Reservas </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarReservas">
                    <ul class="side-nav-second-level">
                        @can('config_reserva_view')
                        <li>
                            <a href="{{ route('config-reserva.index') }}">Configuração</a>
                        </li>
                        @endcan
                        @can('categoria_acomodacao_view')
                        <li>
                            <a href="{{ route('categoria-acomodacao.index') }}">Categorias de acomodação</a>
                        </li>
                        @endcan
                        @can('acomodacao_view')
                        <li>
                            <a href="{{ route('acomodacao.index') }}">Acomodações</a>
                        </li>
                        @endcan
                        @can('frigobar_view')
                        <li>
                            <a href="{{ route('frigobar.index') }}">Frigobares</a>
                        </li>
                        @endcan
                        @can('reserva_view')
                        <li>
                            <a href="{{ route('reservas.index') }}">Reservas</a>
                        </li>
                        @endcan
                        <li>
                            <a href="{{ route('produtos-reserva.index') }}">Produtos</a>
                        </li>

                    </ul>
                </div>
            </li>
            @endcan
            @endif
            @endif

            @if(env("IFOOD") == 1)
            @if(__isActivePlan(Auth::user()->empresa, 'IFood'))
            @canany(['ifood_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarIfood" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <img src="/icons/ifood.png" class="icon-menu">
                    <span> IFood </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarIfood">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('ifood-config.index') }}">Configuração</a>
                        </li>

                        <li>
                            <a href="{{ route('ifood-config-loja.index') }}">Configuração da loja</a>
                        </li>
                        <li>
                            <a href="{{ route('ifood-catalogos.index') }}">Catálogos</a>
                        </li>

                        <li>
                            <a href="{{ route('ifood-categoria-produtos.index') }}">Categorias de Produto</a>
                        </li>
                        <li>
                            <a href="{{ route('ifood-produtos.index') }}">Produtos</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan
            @endif
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'CRM'))
            @canany(['crm_view'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCrm" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-user-voice-fill"></i>
                    <span> CRM </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarCrm">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('crm.index') }}">Listar</a>
                        </li>

                        <li>
                            <a href="{{ route('mensagem-padrao-crm.index') }}">Mensagem padrão</a>
                        </li>

                        <li>
                            <a href="{{ route('mensagem-crm-logs.index') }}">Logs de Mensagem</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'Sped'))
            @canany(['sped_config_view', 'sped_create'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarSped" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-book-fill"></i>
                    <span> Sped </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarSped">
                    <ul class="side-nav-second-level">
                        @can('sped_config_view')
                        <li>
                            <a href="{{ route('sped-config.index') }}">Configuração</a>
                        </li>
                        @endcan
                        @can('sped_create')
                        <li>
                            <a href="{{ route('sped.index') }}">Arquivo</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan
            @endif

            @if(__isActivePlan(Auth::user()->empresa, 'VendiZap'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarVendiZap" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link">
                    <i class="ri-whatsapp-fill"></i>
                    <span> VendiZap </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarVendiZap">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('vendizap-config.index') }}">Configuração</a>
                        </li>
                        <li>
                            <a href="{{ route('vendizap-categorias.index') }}">Categorias</a>
                        </li>
                        @can('variacao_view')
                        <li>
                            <a href="{{ route('variacoes.index') }}">Variações</a>
                        </li>
                        @endcan
                        <li>
                            <a href="{{ route('vendizap-produtos.index') }}">Produtos</a>
                        </li>
                        <li>
                            <a href="{{ route('vendizap-pedidos.index') }}">Pedidos</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif

            @if(!__isContador())
            @canany(['natureza_operacao_view', 'emitente_view'])
            <li class="side-nav-item" id="step5">
                <a data-bs-toggle="collapse" href="#sidebarConfig" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-settings-4-fill"></i>
                    <span>Configuração</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarConfig">
                    <ul class="side-nav-second-level">
                        @can('emitente_view')
                        <li>
                            <a href="{{ route('config.index') }}">Emitente</a>
                        </li>
                        @endcan

                        @can('natureza_operacao_view')
                        <li>
                            <a href="{{ route('natureza-operacao.index') }}">Natureza de operação</a>
                        </li>
                        @endcan

                        @if(Auth::user()->empresa && Auth::user()->empresa->empresa->receber_com_boleto)
                        <li>
                            <a href="{{ route('minhas-faturas.index') }}">Minhas faturas</a>
                        </li>
                        @endif

                        @can('email_config_view')
                        <li>
                            <a href="{{ route('email-config.index') }}">Configuração de email</a>
                        </li>
                        @endcan

                        @can('escritorio_contabil_view')
                        <li>
                            <a href="{{ route('escritorio-contabil.index') }}">Escritório contábil</a>
                        </li>
                        @endcan

                        @can('emitente_view')
                        <li>
                            <a href="{{ route('config-geral.create') }}">Geral</a>
                        </li>
                        @endcan

                        @can('difal_view')
                        <li>
                            <a href="{{ route('difal.index') }}">Op. Interestadual - Difal</a>
                        </li>
                        @endcan

                        @can('cashback_config_view')
                        <li>
                            <a href="{{ route('cash-back-config.index') }}">CashBack</a>
                        </li>
                        @endcan

                        @can('contigencia_view')
                        <li>
                            <a href="{{ route('contigencia.index') }}">Contingência</a>
                        </li>
                        @endcan

                        @can('config_tef_view')
                        <li>
                            <a href="{{ route('tef-config.index') }}">TEF</a>
                        </li>
                        @endcan

                        @can('config_api')
                        <li>
                            <a href="{{ route('config-api.index') }}">API</a>
                        </li>
                        @endcan

                        <li>
                            <a href="{{ route('sintegra.index') }}">Sintegra</a>
                        </li>

                        @can('metas_view')
                        <li>
                            <a href="{{ route('metas.index') }}">Configuração de metas</a>
                        </li>
                        @endcan

                        @can('impressora_pedido_view')
                        <li>
                            <a href="{{ route('impressoras-pedido.index') }}">Impressoras de pedido</a>
                        </li>
                        @endcan
                        
                    </ul>
                </div>
            </li>
            @endcanany

            @endif
            @endif

            @if(Auth::user()->empresa && __isContador())
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCad" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-draft-fill"></i>
                    <span>Cadastros da empresa</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarCad">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('contador-empresa.produtos') }}">Produtos</a>
                        </li>
                        <li>
                            <a href="{{ route('contador-empresa.clientes') }}">Clientes</a>
                        </li>
                        <li>
                            <a href="{{ route('contador-empresa.fornecedores') }}">Fornecedores</a>
                        </li>

                        <li>
                            <a href="{{ route('contador-natureza-operacao.index') }}">Natureza de Operação</a>
                        </li>

                        <li>
                            <a href="{{ route('contador-produto-tributacao.index') }}">Configuração Padrão Fiscal</a>
                        </li>

                        <li>
                            <a href="{{ route('contador.show') }}">Configuração</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarDoc" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-clipboard-fill"></i>
                    <span>Documentos</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarDoc">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('contador-empresa.nfe') }}">NFe</a>
                        </li>

                        <li>
                            <a href="{{ route('contador-empresa.nfce') }}">NFCe</a>
                        </li>
                        <li>
                            <a href="{{ route('contador-empresa.cte') }}">CTe</a>
                        </li>
                        <li>
                            <a href="{{ route('contador-empresa.mdfe') }}">MDFe</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarGerenciamento" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-briefcase-line"></i>
                    <span>Gerenciamento</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarGerenciamento">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('contador-empresas.index') }}">Empresas</a>
                        </li>

                        @if(__isContadorPlano())
                        <li>
                            <a href="{{ route('contador-planos.index') }}">Planos</a>
                        </li>
                        @endif

                        <li>
                            <a href="{{ route('contador-gerencia.index') }}">Gerenciar planos</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif
        </ul>
    </div>
</div>
