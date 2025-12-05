<div class="topnav">
    <div class="nav-horizontal">
        <nav class="navbar navbar-expand parent">
            <div class="collapse navbar-collapse active" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <!-- Menu Home -->
                    <a class="click-left click-menu d-none">
                        <i class="ri-arrow-left-s-line text-primary"></i>
                    </a>
                    <a class="click-right click-menu d-none">
                        <i class="ri-arrow-right-s-line text-primary"></i>
                    </a>
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link">
                            <i class="ri-dashboard-2-fill"></i>
                        </a>
                    </li>

                    <!-- SuperAdmin -->

                    @if(__isSuporte())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="suporte-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-broadcast-line"></i> SuperAdmin <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="suporte-menu">
                            <a href="{{ route('empresas.index') }}" class="dropdown-item">Empresas</a>
                            
                            <a href="{{ route('segmentos.index') }}" class="dropdown-item">Segmentos</a>
                            <a href="{{ route('cidades.index') }}" class="dropdown-item">Cidades</a>
                            <a href="{{ route('usuario-super.index') }}" class="dropdown-item">Usuários</a>

                            <a href="{{ route('ncm.index') }}" class="dropdown-item">NCM</a>
                            <a href="{{ route('logs.index') }}" class="dropdown-item">Logs</a>
                            <a href="{{ route('ibpt.index') }}" class="dropdown-item">IBPT</a>
                            <a href="{{ route('ticket-super.index') }}" class="dropdown-item">Ticket</a>
                            <a href="{{ route('configuracao-super.index') }}" class="dropdown-item">Configuração</a>
                            <a href="{{ route('notificacao-super.index') }}" class="dropdown-item">Notificações</a>
                            <a href="{{ route('padroes-etiqueta.index') }}" class="dropdown-item">Padrões para etiqueta</a>
                            <a href="{{ route('video-suporte.index') }}" class="dropdown-item">Vídeos de suporte</a>
                            <a href="{{ route('relatorios-adm.index') }}" class="dropdown-item">Relatórios</a>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="access-control-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-rotate-lock-line"></i> Controle de Acesso <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="access-control-menu">
                            <a href="{{ route('permissions.index') }}" class="dropdown-item">Permissões</a>
                            <a href="{{ route('roles.index') }}" class="dropdown-item">Atribuições</a>
                        </div>
                    </li>

                    <!-- Emissões -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="emissions-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-file-mark-fill"></i> Emissões <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="emissions-menu">
                            <a href="{{ route('nfe-all') }}" class="dropdown-item">NFe</a>
                            <a href="{{ route('nfce-all') }}" class="dropdown-item">NFCe</a>
                            <a href="{{ route('cte-all') }}" class="dropdown-item">CTe</a>
                            <a href="{{ route('mdfe-all') }}" class="dropdown-item">MDFe</a>
                        </div>
                    </li>

                    @endif
                    @if(__isMaster())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="superadmin-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-stack-fill"></i> SuperAdmin <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="superadmin-menu">
                            <a href="{{ route('empresas.index') }}" class="dropdown-item">Empresas</a>
                            @if(env("CONTADOR") == 1)
                            <a href="{{ route('contadores.index') }}" class="dropdown-item">Contadores</a>
                            @endif
                            <a href="{{ route('planos.index') }}" class="dropdown-item">Planos</a>
                            <a href="{{ route('segmentos.index') }}" class="dropdown-item">Segmentos</a>
                            <a href="{{ route('cidades.index') }}" class="dropdown-item">Cidades</a>
                            <a href="{{ route('usuario-super.index') }}" class="dropdown-item">Usuários</a>
                            <a href="{{ route('gerenciar-planos.index') }}" class="dropdown-item">Gerenciar planos</a>
                            <a href="{{ route('financeiro-plano.index') }}" class="dropdown-item">Financeiro planos</a>
                            <a href="{{ route('planos-pendentes.index') }}" class="dropdown-item">Planos pendentes</a>
                            <a href="{{ route('ncm.index') }}" class="dropdown-item">NCM</a>
                            <a href="{{ route('logs.index') }}" class="dropdown-item">Logs</a>
                            <a href="{{ route('ibpt.index') }}" class="dropdown-item">IBPT</a>
                            <a href="{{ route('ticket-super.index') }}" class="dropdown-item">Ticket</a>
                            <a href="{{ route('configuracao-super.index') }}" class="dropdown-item">Configuração</a>
                            <a href="{{ route('notificacao-super.index') }}" class="dropdown-item">Notificações</a>
                            <a href="{{ route('padroes-etiqueta.index') }}" class="dropdown-item">Padrões para etiqueta</a>
                            <a href="{{ route('video-suporte.index') }}" class="dropdown-item">Vídeos de suporte</a>
                            <a href="{{ route('relatorios-adm.index') }}" class="dropdown-item">Relatórios</a>
                        </div>
                    </li>

                    <!-- Controle de Acesso -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="access-control-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-rotate-lock-line"></i> Controle de Acesso <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="access-control-menu">
                            <a href="{{ route('permissions.index') }}" class="dropdown-item">Permissões</a>
                            <a href="{{ route('roles.index') }}" class="dropdown-item">Atribuições</a>
                        </div>
                    </li>

                    <!-- Emissões -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="emissions-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-file-mark-fill"></i> Emissões <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="emissions-menu">
                            <a href="{{ route('nfe-all') }}" class="dropdown-item">NFe</a>
                            <a href="{{ route('nfce-all') }}" class="dropdown-item">NFCe</a>
                            <a href="{{ route('cte-all') }}" class="dropdown-item">CTe</a>
                            <a href="{{ route('mdfe-all') }}" class="dropdown-item">MDFe</a>
                        </div>
                    </li>

                    @if(env("MARKETPLACE") == 1)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="delivery-super-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-store-2-line"></i> Delivery/Marketplace<div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="delivery-super-menu">
                            <a href="{{ route('bairros-super.index') }}" class="dropdown-item">Bairros</a>

                        </div>
                    </li>
                    @endif

                    @if(env("APP_ENV") != "demo")
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="atualizacao-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-refresh-fill"></i> Atualização<div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="atualizacao-menu">
                            <a href="{{ route('update-sql.index') }}" class="dropdown-item">Banco de dados</a>
                            <a href="{{ route('update-file.index') }}" class="dropdown-item">Diretórios</a>

                        </div>
                    </li>

                    @endif

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="{{ route('registro') }}" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-bookmark-3-fill"></i> Registro de Software
                        </a>
                    </li>

                    @endif

                    @if(Auth::user()->empresa && __isContador())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="cadastro-contador-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-draft-fill"></i> Cadastros <div class="arrow-down"></div>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="cadastro-contador-menu">
                            <a href="{{ route('contador-empresa.produtos') }}" class="dropdown-item">Produtos</a>
                            <a href="{{ route('contador-empresa.clientes') }}" class="dropdown-item">Clientes</a>
                            <a href="{{ route('contador-empresa.fornecedores') }}" class="dropdown-item">Fornecedores</a>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="documentos-contador-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-clipboard-fill"></i> Documentos <div class="arrow-down"></div>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="documentos-contador-menu">
                            <a href="{{ route('contador-empresa.nfe') }}" class="dropdown-item">NFe</a>
                            <a href="{{ route('contador-empresa.nfce') }}" class="dropdown-item">NFCe</a>
                            <a href="{{ route('contador-empresa.cte') }}" class="dropdown-item">CTe</a>
                            <a href="{{ route('contador-empresa.mdfe') }}" class="dropdown-item">MDFe</a>
                        </div>
                    </li>

                    @endif

                    @if(!__isMaster())
                    @if(__isActivePlan(Auth::user()->empresa, 'Produtos'))
                    @canany(['produtos_view', 'categoria_produtos_view', 'inventario_view', 'lista_preco_view'])

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="produtos-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-product-hunt-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="produtos-menu">
                            <label>Produtos</label>

                            @can('categoria_produtos_view')
                            <a href="{{ route('categoria-produtos.index') }}" class="dropdown-item">Categorias</a>
                            @endcan

                            @can('produtos_view')
                            <a href="{{ route('produtos.index') }}" class="dropdown-item">Listar</a>
                            @endcan
                            @can('produtos_create')
                            <a href="{{ route('produtos.create') }}" class="dropdown-item">Novo Produto</a>
                            @endcan

                            @can('estoque_view')
                            <a href="{{ route('estoque.index') }}" class="dropdown-item">Estoque</a>
                            @endcan

                            @can('inventario_view')
                            <a href="{{ route('inventarios.index') }}" class="dropdown-item">Inventário</a>
                            @endcan

                            @can('variacao_view')
                            <a href="{{ route('variacoes.index') }}" class="dropdown-item">Variações</a>
                            @endcan

                            @can('lista_preco_view')
                            <a href="{{ route('lista-preco.index') }}" class="dropdown-item">Lista de preços</a>
                            @endcan

                            @if(__isPlanoFiscal())
                            @can('config_produto_fiscal_view')
                            <a href="{{ route('produtopadrao-tributacao.index') }}" class="dropdown-item">Configuração Padrão Fiscal</a>
                            @endcan
                            @endif

                            @can('marcas_view')
                            <a href="{{ route('marcas.index') }}" class="dropdown-item">Marcas</a>
                            @endcan

                            <a href="{{ route('modelo-etiquetas.index') }}" class="dropdown-item">Modelos de Etiqueta</a>

                            <a href="{{ route('produto-consulta-codigo.index') }}" class="dropdown-item">Consulta código</a>

                            @can('transferencia_estoque_view')
                            <a href="{{ route('transferencia-estoque.index') }}" class="dropdown-item">Transferência de estoque</a>
                            @endcan

                            @can('unidade_medida_view')
                            <a href="{{ route('unidades-medida.index') }}" class="dropdown-item">Unidades de medida</a>
                            @endcan

                        </div>
                    </li>

                    @endcanany
                    @endif
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'Atendimento'))
                    @canany(['atendimentos_view'])

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="atendimentos-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-store-2-line"></i> <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="atendimentos-menu">
                            <label>Atendimento</label>

                            <a href="{{ route('atendimentos.index') }}" class="dropdown-item">Dias de Atendimento</a>
                            <a href="{{ route('interrupcoes.index') }}" class="dropdown-item">Interrupções</a>
                            <a href="{{ route('funcionamentos.index') }}" class="dropdown-item">Horário de Funcionamento</a>
                        </div>
                    </li>

                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'Serviços'))
                    @canany(['servico_view', 'categoria_servico_view', 'ordem_servico_view'])

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="servicos-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <i class="ri-tools-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="servicos-menu">
                            <label>Serviços</label>

                            @can('categoria_servico_view')
                            <a href="{{ route('categoria-servico.index') }}" class="dropdown-item">Categorias</a>
                            @endcan

                            @can('servico_view')
                            <a href="{{ route('servicos.index') }}" class="dropdown-item">Listar</a>
                            @endcan

                            @can('servico_create')
                            <a href="{{ route('servicos.create') }}" class="dropdown-item">Novo serviço</a>
                            @endcan
                        </div>
                    </li>

                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'Serviços'))
                    @canany(['servico_view', 'categoria_servico_view', 'ordem_servico_view'])

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="os-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-ruler-2-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="os-menu">
                            <label>Ordem de Serviço</label>
                            @can('ordem_servico_view')
                            <a href="{{ route('ordem-servico.index') }}" class="dropdown-item">Listar</a>
                            @endcan

                            @can('ordem_servico_create')
                            <a href="{{ route('ordem-servico.create') }}" class="dropdown-item">Nova OS</a>
                            @endcan

                            @if(__isSegmentoPlanoOtica())
                            @can('convenio_view')
                            <a href="{{ route('convenios.index') }}" class="dropdown-item">Convênios</a>
                            @endcan

                            @can('medico_view')
                            <a href="{{ route('medicos.index') }}" class="dropdown-item">Médicos</a>
                            @endcan

                            @can('laboratorio_view')
                            <a href="{{ route('laboratorios.index') }}" class="dropdown-item">Laboratórios</a>
                            @endcan

                            @can('tratamento_otica_view')
                            <a href="{{ route('tratamentos-otica.index') }}" class="dropdown-item">Tratamentos ótica</a>
                            @endcan

                            @can('formato_armacao_view')
                            <a href="{{ route('formato-armacao.index') }}" class="dropdown-item">Formatos de armação</a>
                            @endcan
                            <a href="{{ route('tipo-armacao.index') }}" class="dropdown-item">Tipos de armação</a>
                            @endif

                            @can('metas_view')
                            <a href="{{ route('ordem-servico.metas') }}" class="dropdown-item">Metas</a>
                            @endcan
                            
                        </div>
                    </li>

                    @endcanany
                    @endif

                    @canany(['ordem_producao_view'])
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="agendamentos-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-pencil-ruler-line"></i> <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="agendamentos-menu">
                            <label>Ordem de Produção</label>

                            @can('ordem_producao_view')
                            <a href="{{ route('ordem-producao.index') }}" class="dropdown-item">Listar</a>
                            @endcan

                            @can('ordem_producao_create')
                            <a href="{{ route('ordem-producao.create') }}" class="dropdown-item">Nova Ordem</a>
                            @endcan
                        </div>
                    </li>
                    @endcanany

                    @if(__isActivePlan(Auth::user()->empresa, 'Agendamentos'))
                    @canany(['agendamento_view'])

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="agendamentos-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-calendar-event-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="agendamentos-menu">
                            <label>Agendamentos</label>
                            <a href="{{ route('agendamentos.index') }}" class="dropdown-item">Listar</a>
                        </div>
                    </li>

                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'Usuários'))
                    @canany(['usuarios_view', 'controle_acesso_view'])

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="usuarios-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-user-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="usuarios-menu">
                            <label>Usuários</label>
                            @can('usuarios_view')
                            <a href="{{ route('usuarios.index') }}" class="dropdown-item">Listar</a>
                            @endcan
                            @can('controle_acesso_view')
                            <a href="{{ route('controle-acesso.index') }}" class="dropdown-item">Controle de acesso</a>
                            @endcan
                            @can('config_fiscal_usuario_view')
                            <a href="{{ route('config-fiscal-usuario.index') }}" class="dropdown-item">Configuração fiscal</a>
                            @endcan
                        </div>
                    </li>

                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'Pessoas'))
                    @canany(['clientes_view', 'fornecedores_view', 'transportadoras_view'])

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="pessoas-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-group-2-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="pessoas-menu">
                            <label>Pessoas</label>
                            @can('clientes_view')
                            <a href="{{ route('clientes.index') }}" class="dropdown-item">Clientes</a>
                            @endcan

                            @can('fornecedores_view')
                            <a href="{{ route('fornecedores.index') }}" class="dropdown-item">Fornecedores</a>
                            @endcan

                            @can('transportadoras_view')
                            <a href="{{ route('transportadoras.index') }}" class="dropdown-item">Transportadoras</a>
                            @endcan
                        </div>
                    </li>

                    @endcanany

                    @canany(['funcionario_view', 'apuracao_mensal_view'])

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="apuracao-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-folder-user-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="apuracao-menu">
                            <label>Gestão Pessoal</label>
                            @can('funcionario_view')
                            <a href="{{ route('funcionarios.index') }}" class="dropdown-item">Funcionários</a>
                            @endcan

                            @can('apuracao_mensal_view')
                            <a href="{{ route('evento-funcionarios.index') }}" class="dropdown-item">Eventos</a>
                            <a href="{{ route('funcionario-eventos.index') }}" class="dropdown-item">Funcionários x Eventos</a>
                            <a href="{{ route('apuracao-mensal.index') }}" class="dropdown-item">Apuração Mensal</a>
                            @endcan

                            @can('comissao_margem_view')
                            <a href="{{ route('comissao-margem.index') }}" class="dropdown-item">Comissão por margem</a>
                            @endcan
                        </div>
                    </li>

                    @endcanany
                    @endif


                    @if(__isActivePlan(Auth::user()->empresa, 'Compras'))
                    @canany(['compras_view', 'manifesto_view', 'cotacao_view'])

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="compras-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-logout-box-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="compras-menu">
                            <label>Compras</label>
                            @can('compras_view')
                            <a href="{{ route('compras.index') }}" class="dropdown-item">Listar</a>
                            @endcan

                            @can('compras_create')
                            <a href="{{ route('compras.create')}}" class="dropdown-item">Nova
                            </a>
                            @endcan

                            <a href="{{ route('compras.xml')}}" class="dropdown-item">
                                Importar XML
                            </a>

                            @can('manifesto_view')
                            <a href="{{ route('manifesto.index')}}" class="dropdown-item">
                                Manifesto
                            </a>
                            @endcan

                            @can('cotacao_view')
                            <a href="{{ route('cotacoes.index')}}" class="dropdown-item">
                                Cotação
                            </a>
                            @endcan

                            @if(__isPlanoFiscal())
                            @can('arquivos_xml_view')
                            <a href="{{ route('nfe-entrada-xml.index') }}" class="dropdown-item">Arquivos XML Emitidos</a>
                            @endcan
                            @endif

                            @if(__isPlanoFiscal())
                            @can('arquivos_xml_view')
                            <a href="{{ route('nfe-importa-xml.index') }}" class="dropdown-item">Arquivos XML Importados</a>
                            @endcan
                            @endif

                            @can('relacao_dados_fornecedor_view')
                            <a href="{{ route('relacao-dados-fornecedor.index')}}" class="dropdown-item">
                                Relação dados fornecedor
                            </a>
                            @endcan

                        </div>
                    </li>

                    @endcanany
                    @endif

                    @if(__isPlanoFiscal())
                    @canany(['devolucao_view'])
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="agendamentos-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-arrow-go-back-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="agendamentos-menu">
                            <label>Devolução</label>
                            @can('devolucao_view')
                            <a href="{{ route('devolucao.index')}}" class="dropdown-item">
                                Lista
                            </a>
                            @endcan

                            @can('devolucao_create')
                            <a href="{{ route('devolucao.xml')}}" class="dropdown-item">
                                Nova devolução
                            </a>
                            @endcan

                        </div>
                    </li>

                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'PDV'))
                    @canany(['pdv_view'])

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="pdv-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-shopping-cart-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="pdv-menu">
                            <label>PDV</label>
                            @can('pdv_view')
                            <a href="{{ route('frontbox.index') }}" class="dropdown-item">Listar</a>
                            @endcan

                            @can('pdv_create')
                            <a href="{{ route('frontbox.create')}}" class="dropdown-item">PDV</a>
                            @endcan

                            @can('troca_view')
                            <a href="{{ route('trocas.index')}}" class="dropdown-item">Trocas</a>
                            @endcan

                            @can('config_tef_view')
                            <a href="{{ route('tef-registros.index') }}" class="dropdown-item">Registros de TEF</a>
                            @endcan

                        </div>
                    </li>

                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'Vendas'))
                    @canany(['nfe_view', 'orcamento_view'])

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="vendas-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-file-list-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="vendas-menu">
                            <label>Vendas</label>
                            @can('nfe_view')
                            <a href="{{ route('nfe.index') }}" class="dropdown-item">Listar</a>
                            @endcan
                            @can('nfe_create')
                            <a href="{{ route('nfe.create') }}" class="dropdown-item">Nova</a>
                            @endcan

                            @if(__isPlanoFiscal())
                            @can('nfe_inutiliza')
                            <a href="{{ route('nfe.inutilizar') }}" class="dropdown-item">Inutilizar NFe</a>
                            @endcan
                            @endif

                            @can('orcamento_view')
                            <a href="{{ route('orcamentos.index') }}" class="dropdown-item">Orçamentos</a>
                            @endcan

                            @if(__isPlanoFiscal())
                            @can('arquivos_xml_view')
                            <a href="{{ route('nfe-xml.index') }}" class="dropdown-item">Arquivos XML</a>
                            @endcan
                            @endif
                            <a href="{{ route('nfe.import-zip') }}" class="dropdown-item">Importar XML</a>

                            @can('metas_view')
                            <a href="{{ route('nfe.metas') }}" class="dropdown-item">Metas</a>
                            @endcan
                        </div>
                    </li>

                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'Financeiro'))
                    @canany(['conta_pagar_view', 'conta_receber_view', 'relatorio_view', 'caixa_view', 'contas_empresa_view', 'contas_boleto_view', 'boleto_view', 'taxa_pagamento_view'])

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="vendas-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-money-dollar-box-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="vendas-menu">
                            <label>Financeiro</label>

                            @can('caixa_view')

                            <a class="dropdown-item" href="{{ route('financeiro.dashboard') }}">Dashboard</a>

                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-caixa" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Caixa <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-caixa">
                                    <a href="{{ route('caixa.index') }}" class="dropdown-item">Movimentação</a>
                                    <a href="{{ route('caixa.create') }}" class="dropdown-item">Abrir caixa</a>
                                    <a href="{{ route('caixa.list') }}" class="dropdown-item">Listar</a>
                                </div>
                            </div>
                            @endcan

                            @canany(['conta_pagar_view', 'conta_pagar_create'])
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-conta-pagar" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Contas a pagar <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-conta-pagar">
                                    @can('conta_pagar_view')
                                    <a href="{{ route('conta-pagar.index') }}" class="dropdown-item">Listar</a>
                                    @endcan

                                    @can('conta_pagar_create')
                                    <a href="{{ route('conta-pagar.create') }}" class="dropdown-item">Nova conta</a>
                                    @endcan
                                </div>
                            </div>
                            @endcan

                            @canany(['conta_pagar_view', 'conta_pagar_create'])
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-conta-receber" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Contas a receber <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-conta-receber">
                                    @can('conta_receber_view')
                                    <a href="{{ route('conta-receber.index') }}" class="dropdown-item">Listar</a>
                                    @endcan

                                    @can('conta_receber_create')
                                    <a href="{{ route('conta-receber.create') }}" class="dropdown-item">Nova conta</a>
                                    @endcan
                                </div>
                            </div>
                            @endcan

                            @can('relatorio_view')
                            <a href="{{ route('relatorios.index') }}" class="dropdown-item">Relatórios</a>
                            @endcan

                            @can('taxa_pagamento_view')
                            <a href="{{ route('taxa-cartao.index') }}" class="dropdown-item">Taxas de pagamento</a>
                            @endcan
                            <a href="{{ route('plano-contas.index') }}" class="dropdown-item">Plano de contas</a>
                            @can('contas_empresa_view')
                            <a href="{{ route('contas-empresa.index') }}" class="dropdown-item">Contas da empresa</a>
                            @endcan

                            @can('contas_boleto_view')
                            <a href="{{ route('contas-boleto.index') }}" class="dropdown-item">Contas para boleto</a>
                            @endcan

                            @can('boleto_view')
                            <a href="{{ route('boleto.index') }}" class="dropdown-item">Boletos</a>
                            @endcan
                        </div>
                    </li>
                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'NFCe'))
                    @canany(['nfce_view'])

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="nfce-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-bill-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="nfce-menu">
                            <label>NFCe</label>
                            @can('nfce_view')
                            <a href="{{ route('nfce.index') }}" class="dropdown-item">Listar</a>
                            @endcan

                            @can('nfce_create')
                            <a href="{{ route('nfce.create') }}" class="dropdown-item">Nova</a>
                            @endcan

                            @if(__isPlanoFiscal())
                            @can('nfce_inutiliza')
                            <a href="{{ route('nfce.inutilizar') }}" class="dropdown-item">Inutilizar</a>
                            @endcan

                            @can('arquivos_xml_view')
                            <a href="{{ route('nfce-xml.index') }}" class="dropdown-item">Arquivos XML</a>
                            @endcan
                            @endif
                            <a href="{{ route('nfce.import-zip') }}" class="dropdown-item">Importar XML</a>
                            <a href="{{ route('nfce-contigencia.index') }}" class="dropdown-item">Envio Contigência</a>
                        </div>
                    </li>

                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'Pré venda'))
                    @canany(['pre_venda_view'])
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="pre-venda-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-list-ordered"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="pre-venda-menu">
                            <label>Pré Venda</label>
                            @can('pre_venda_view')
                            <a href="{{ route('pre-venda.index') }}" class="dropdown-item">Listar</a>
                            @endcan

                            @can('pre_venda_create')
                            <a href="{{ route('pre-venda.create') }}" class="dropdown-item">Nova</a>
                            @endcan
                        </div>
                    </li>
                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'NFSe'))
                    @canany(['nfse_view'])
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="nfse-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-file-code-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="nfse-menu">
                            <label>NFSe</label>
                            @can('nfse_view')
                            <a href="{{ route('nota-servico.index') }}" class="dropdown-item">Listar</a>
                            @endcan

                            @can('nfse_create')
                            <a href="{{ route('nota-servico.create') }}" class="dropdown-item">Nova</a>
                            @endcan
                            <a href="{{ route('nota-servico-config.index') }}" class="dropdown-item">Emitente</a>
                        </div>
                    </li>
                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'Veiculos'))
                    @canany(['veiculos_view', 'veiculos_create'])
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="veiculos-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-roadster-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="veiculos-menu">
                            <label>Veículos</label>
                            @can('veiculos_view')
                            <a href="{{ route('veiculos.index') }}" class="dropdown-item">Listar</a>
                            @endcan
                            @can('veiculos_create')
                            <a href="{{ route('veiculos.create') }}" class="dropdown-item">Novo Veículo</a>
                            @endcan
                        </div>
                    </li>
                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'Planejamento de Custos'))
                    @canany(['planejamento_custo_view'])

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="veiculos-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-dashboard-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="veiculos-menu">
                            <label>Planejamento de Custos</label>
                            @can('veiculos_view')
                            <a href="{{ route('veiculos.index') }}" class="dropdown-item">Listar</a>
                            @endcan
                            @can('veiculos_create')
                            <a href="{{ route('veiculos.create') }}" class="dropdown-item">Novo Veículo</a>
                            @endcan

                            @can('planejamento_custo_view')
                            <a href="{{ route('planejamento-custo.index') }}" class="dropdown-item">Listar</a>
                            @endcan
                            @can('planejamento_custo_create')
                            <a href="{{ route('planejamento-custo.create') }}" class="dropdown-item">Novo</a>
                            @endcan
                        </div>
                    </li>

                    @endcan
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'Controle de Fretes'))
                    @canany(['tipo_despesa_frete_view', 'frete_view'])
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="controle-fretes-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-road-map-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="controle-fretes-menu">
                            <label>Controle de Fretes</label>
                            @can('tipo_despesa_frete_view')
                            <a href="{{ route('tipo-despesa-frete.index') }}" class="dropdown-item">Tipos de despesa de frete</a>
                            @endcan
                            @can('frete_view')
                            <a href="{{ route('fretes.index') }}" class="dropdown-item">Fretes</a>
                            @endcan
                            @can('manutencao_veiculo_view')
                            <a href="{{ route('manutencao-veiculos.index') }}" class="dropdown-item">Manutenção de veículos</a>
                            @endcan
                        </div>
                    </li>
                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'CTe'))
                    @canany(['cte_view'])
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="cte-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-truck-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="cte-menu">
                            <label>CTe</label>
                            @can('cte_view')
                            <a href="{{ route('cte.index') }}" class="dropdown-item">Listar</a>
                            @endcan

                            @can('cte_create')
                            <a href="{{ route('cte.create') }}" class="dropdown-item">Nova</a>
                            @endcan

                            @can('arquivos_xml_view')
                            <a href="{{ route('cte-xml.index') }}" class="dropdown-item">Arquivos XML</a>
                            @endcan
                        </div>
                    </li>
                    @endcanany

                    @canany(['cte_os_view'])

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="cteos-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-focus-3-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="cteos-menu">
                            <label>CTe Os</label>
                            @can('cte_os_view')
                            <a href="{{ route('cte-os.index') }}" class="dropdown-item">Listar</a>
                            @endcan

                            @can('cte_os_create')
                            <a href="{{ route('cte-os.create') }}" class="dropdown-item">Nova</a>
                            @endcan
                        </div>
                    </li>
                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'MDFe'))
                    @canany(['mdfe_view'])
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="mdfe-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-file-lock-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="mdfe-menu">
                            <label>MDFe</label>
                            @can('mdfe_view')
                            <a href="{{ route('mdfe.index') }}" class="dropdown-item">Listar</a>
                            @endcan

                            @can('mdfe_create')
                            <a href="{{ route('mdfe.create') }}" class="dropdown-item">Nova</a>
                            @endcan

                            @can('arquivos_xml_view')
                            <a href="{{ route('mdfe-xml.index') }}" class="dropdown-item">Arquivos XML</a>
                            @endcan
                        </div>
                    </li>
                    @endcanany
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'Cardapio'))
                    @can('cardapio_view')
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="cardapio-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-restaurant-2-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="cardapio-menu">
                            <label>Cardápio</label>
                            <a href="{{ route('config-cardapio.index') }}" class="dropdown-item">Configuração</a>
                            <a href="{{ route('produtos-cardapio.categorias') }}" class="dropdown-item">Categorias</a>
                            <a href="{{ route('produtos-cardapio.index') }}" class="dropdown-item">Produtos</a>

                            <a href="{{ route('categoria-adicional.index') }}" class="dropdown-item">Categorias de adicional</a>
                            <a href="{{ route('adicionais.index') }}" class="dropdown-item">Adicionais</a>
                            <a href="{{ route('pedidos-cardapio.index') }}" class="dropdown-item">Comandas</a>
                            <a href="{{ route('pedido-cozinha.index') }}" class="dropdown-item">Controle de pedidos</a>
                            <a href="{{ route('impressao-pedido.index') }}" class="dropdown-item">>Controle de impressão</a>
                            <a href="{{ route('carrossel.index') }}" class="dropdown-item">Carrossel destaque</a>
                            <a href="{{ route('avaliacao-cardapio.index') }}" class="dropdown-item">Avaliações</a>
                            <a href="{{ route('tamanhos-pizza.index') }}" class="dropdown-item">Tamanhos de pizza</a>
                            <a href="{{ route('atendimento-garcom.index') }}" class="dropdown-item">Atendimentos garçom</a>
                            <a href="{{ route('pedidos-cardapio.historico') }}" class="dropdown-item">Histórico</a>
                            @if(file_exists(public_path('app.apk')))
                            <a href="{{ route('config-cardapio.download') }}" class="dropdown-item">Download APP</a>
                            @endif

                        </div>
                    </li>
                    @endcanany
                    @endif

                    @if(env("ECOMMERCE") == 1)
                    @if(__isActivePlan(Auth::user()->empresa, 'Ecommerce'))
                    @can('ecommerce_view')
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="ecommerce-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-store-3-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="ecommerce-menu">
                            <label>Ecommerce</label>
                            <a href="{{ route('config-ecommerce.index') }}" class="dropdown-item">Configuração</a>
                            <a href="{{ route('produtos-ecommerce.categorias') }}" class="dropdown-item">Categorias de produtos</a>
                            <a href="{{ route('produtos-ecommerce.index') }}" class="dropdown-item">Produtos</a>
                            <a href="{{ route('pedidos-ecommerce.index') }}" class="dropdown-item">Pedidos</a>
                            <a target="_blank" href="{{ route('config-ecommerce.site') }}" class="dropdown-item">Ver site</a>
                        </div>
                    </li>
                    @endcan
                    @endif
                    @endif

                    @if(env("MERCADOLIVRE") == 1)
                    @if(__isActivePlan(Auth::user()->empresa, 'Mercado Livre'))
                    @can('mercado_livre_view')
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="mercado-livre-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-box-1-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="mercado-livre-menu">
                            <label>Mercado Livre</label>
                            <a href="{{ route('mercado-livre-config.index') }}" class="dropdown-item">Configuração</a>
                            <a href="{{ route('mercado-livre.produtos-news') }}" class="dropdown-item">Produtos</a>
                            <a href="{{ route('mercado-livre-perguntas.index') }}" class="dropdown-item">Perguntas</a>
                            <a href="{{ route('mercado-livre-pedidos.index') }}" class="dropdown-item">Pedidos</a>
                        </div>
                    </li>
                    @endcan
                    @endif
                    @endif

                    @if(env("WOOCOMMERCE") == 1)
                    @if(__isActivePlan(Auth::user()->empresa, 'Woocommerce'))
                    @can('woocommerce_view')
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="woocommerce-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-wordpress-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="woocommerce-menu">
                            <label>Woocommerce</label>
                            <a href="{{ route('woocommerce-config.index') }}" class="dropdown-item">Configuração</a>
                            <a href="{{ route('woocommerce-categorias.index') }}" class="dropdown-item">Categorias</a>
                            <a href="{{ route('woocommerce-produtos.index') }}" class="dropdown-item">Produtos</a>
                            <a href="{{ route('woocommerce-pedidos.index') }}" class="dropdown-item">Pedidos</a>
                        </div>
                    </li>
                    @endcan
                    @endif
                    @endif

                    @if(env("NUVEMSHOP") == 1)
                    @if(__isActivePlan(Auth::user()->empresa, 'Nuvem Shop'))
                    @can('nuvem_shop_view')
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="nuvem-shop-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-cloud-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="nuvem-shop-menu">
                            <label>Nuvem Shop</label>
                            <a href="{{ route('nuvem-shop-config.index') }}" class="dropdown-item">Configuração</a>
                            <a href="{{ route('nuvem-shop-categorias.index') }}" class="dropdown-item">Categorias</a>
                            <a href="{{ route('nuvem-shop-produtos.index') }}" class="dropdown-item">Produtos</a>
                            <a href="{{ route('nuvem-shop-pedidos.index') }}" class="dropdown-item">Pedidos</a>
                        </div>
                    </li>
                    @endcan
                    @endif
                    @endif

                    @if(env("MARKETPLACE") == 1)
                    @if(__isActivePlan(Auth::user()->empresa, 'Delivery'))
                    @can('delivery_view')
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="delivery-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-store-2-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="delivery-menu">
                            <label>Delivery/Marketplace</label>

                            <a href="{{ route('config-marketplace.index') }}" class="dropdown-item">Configuração</a>
                            <a href="{{ route('pedidos-delivery.index') }}" class="dropdown-item">Pedidos</a>
                            <a href="{{ route('produtos-delivery.categorias') }}" class="dropdown-item">Categorias de produto</a>
                            <a href="{{ route('servico-marketplace.categorias') }}" class="dropdown-item">Categorias de serviço</a>
                            <a href="{{ route('produtos-delivery.index') }}" class="dropdown-item">Produtos</a>
                            <a href="{{ route('servicos-marketplace.index') }}" class="dropdown-item">Serviços</a>
                            <a href="{{ route('funcionamento-delivery.index') }}" class="dropdown-item">Funcionamento</a>
                            <a href="{{ route('bairros-empresa.index') }}" class="dropdown-item">Bairros</a>
                            <a href="{{ route('categoria-adicional.index') }}" class="dropdown-item">Categorias de adicional</a>
                            <a href="{{ route('adicionais.index') }}" class="dropdown-item">Adicionais</a>
                            <a href="{{ route('destaque-marketplace.index') }}" class="dropdown-item">Destaques</a>
                            <a href="{{ route('cupom-desconto.index') }}" class="dropdown-item">Cupom de desconto</a>
                            <a href="{{ route('tamanhos-pizza.index') }}" class="dropdown-item">Tamanhos de pizza</a>
                            <a href="{{ route('motoboys.index') }}" class="dropdown-item">Motoboys</a>

                            <a href="{{ route('pedido-cozinha.index') }}" class="dropdown-item">Controle de pedidos</a>
                            <a href="{{ route('impressao-pedido.index') }}" class="dropdown-item">>Controle de impressão</a>

                            <a href="{{ route('clientes-delivery.index') }}" class="dropdown-item">Clientes</a>
                            <a href="{{ route('config-agendamento.index') }}" class="dropdown-item">Configuração de agendamento</a>
                            <a target="_blank" href="{{ route('config-marketplace.loja') }}" class="dropdown-item">Ver loja</a>
                        </div>
                    </li>

                    @endcan
                    @endif
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'Localizações'))
                    @canany(['localizacao_view'])
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="localizacao-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-building-4-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="localizacao-menu">
                            <label>Localizações</label>
                            <a href="{{ route('localizacao.index') }}" class="dropdown-item">Listar</a>

                        </div>
                    </li>
                    @endcan
                    @endif

                    @if(env("RESERVAS") == 1)
                    @if(__isActivePlan(Auth::user()->empresa, 'Reservas'))
                    @canany(['categoria_acomodacao_view', 'config_reserva_view', 'reserva_view'])

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="reservas-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-hotel-line"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="reservas-menu">
                            <label>Reservas</label>

                            @can('config_reserva_view')
                            <a href="{{ route('config-reserva.index') }}" class="dropdown-item">Configuração</a>
                            @endcan
                            @can('categoria_acomodacao_view')
                            <a href="{{ route('categoria-acomodacao.index') }}" class="dropdown-item">Categorias de acomodação</a>
                            @endcan
                            @can('acomodacao_view')
                            <a href="{{ route('acomodacao.index') }}" class="dropdown-item">Acomodações</a>
                            @endcan
                            @can('frigobar_view')
                            <a href="{{ route('frigobar.index') }}" class="dropdown-item">Frigobares</a>
                            @endcan
                            @can('reserva_view')
                            <a href="{{ route('reservas.index') }}" class="dropdown-item">Reservas</a>
                            @endcan
                            <a href="{{ route('produtos-reserva.index') }}" class="dropdown-item">Produtos</a>
                        </div>
                    </li>

                    @endcan
                    @endif
                    @endif

                    @canany(['crm_view'])

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="crm-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-user-voice-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="crm-menu">
                            <label>CRM</label>
                            <a href="{{ route('crm.index') }}" class="dropdown-item">Listar</a>
                        </div>
                    </li>
                    @endcan

                    @if(__isActivePlan(Auth::user()->empresa, 'Sped'))
                    @canany(['sped_config_view', 'sped_create'])

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="sped-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-book-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="sped-menu">
                            <label>Sped</label>
                            @can('sped_config_view')
                            <a href="{{ route('sped-config.index') }}" class="dropdown-item">Configuração</a>
                            @endcan
                            @can('sped_create')
                            <a href="{{ route('sped.index') }}" class="dropdown-item">Arquivo</a>
                            @endcan
                        </div>
                    </li>
                    @endcan
                    @endif

                    @if(__isActivePlan(Auth::user()->empresa, 'VendiZap'))
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="vendizap-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-whatsapp-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="vendizap-menu">
                            <label>VendiZap</label>
                            <a href="{{ route('vendizap-config.index') }}" class="dropdown-item">Configuração</a>
                            <a href="{{ route('vendizap-categorias.index') }}" class="dropdown-item">Categorias</a>
                            @can('variacao_view')
                            <a href="{{ route('variacoes.index') }}" class="dropdown-item">Variações</a>
                            @endcan
                            <a href="{{ route('vendizap-produtos.index') }}" class="dropdown-item">Produtos</a>
                            <a href="{{ route('vendizap-pedidos.index') }}" class="dropdown-item">Pedidos</a>
                        </div>
                    </li>
                    @endif

                    @canany(['natureza_operacao_view', 'emitente_view'])

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="config-menu" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-settings-4-fill"></i>  <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="config-menu">
                            <label>Configuração</label>
                            @can('emitente_view')
                            <a href="{{ route('config.index') }}" class="dropdown-item">Emitente</a>
                            @endcan

                            @can('natureza_operacao_view')
                            <a href="{{ route('natureza-operacao.index') }}" class="dropdown-item">Natureza de operação</a>
                            @endcan

                            @can('email_config_view')
                            <a href="{{ route('email-config.index') }}" class="dropdown-item">Configuração de email</a>
                            @endcan

                            @can('escritorio_contabil_view')
                            <a href="{{ route('escritorio-contabil.index') }}" class="dropdown-item">Escritório contábil</a>
                            @endcan

                            @can('emitente_view')
                            <a href="{{ route('config-geral.create') }}" class="dropdown-item">Geral</a>
                            @endcan

                            @can('difal_view')
                            <a href="{{ route('difal.index') }}" class="dropdown-item">Op. Interestadual - Difal</a>
                            @endcan

                            @can('cashback_config_view')
                            <a href="{{ route('cash-back-config.index') }}" class="dropdown-item">CashBack</a>
                            @endcan

                            @can('contigencia_view')
                            <a href="{{ route('contigencia.index') }}" class="dropdown-item">Contigência</a>
                            @endcan

                            @can('config_tef_view')
                            <a href="{{ route('tef-config.index') }}" class="dropdown-item">TEF</a>
                            @endcan

                            @can('config_api')
                            <a href="{{ route('config-api.index') }}" class="dropdown-item">API</a>
                            @endcan

                            @can('metas_view')
                            <a href="{{ route('metas.index') }}" class="dropdown-item">Configuração de metas</a>
                            @endcan

                            <a href="{{ route('sintegra.index') }}" class="dropdown-item">Sintegra</a>
                            @can('impressora_pedido_view')
                            <a href="{{ route('impressoras-pedido.index') }}" class="dropdown-item">Impressoras de pedido</a>
                            @endcan
                        </div>
                    </li>

                    @endcan


                </ul>
            </div>
        </nav>
    </div>
</div>
