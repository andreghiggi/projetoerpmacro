<!doctype html>
  <html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} - Gestão Empresarial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="#">{{ env('APP_NAME') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="#features">Funcionalidades</a></li>
            <li class="nav-item"><a class="nav-link" href="#plans">Planos</a></li>
            <li class="nav-item"><a class="nav-link" href="#contact">Contato</a></li>
            <li class="nav-item"><a class="btn btn-light text-primary ms-3" href="{{ env('APP_URL') }}/login">Teste Grátis</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Hero -->
    <section class="py-5 bg-light text-center">
      <div class="container">
        <h1 class="display-4 fw-bold">Simplifique a gestão da sua empresa</h1>
        <p class="lead">ERP SaaS completo para emitir notas fiscais, controlar estoque e acompanhar suas vendas em tempo real.</p>
        <a href="{{ env('APP_URL') }}/login" class="btn btn-primary btn-lg mt-3">Experimente Gratuitamente</a>
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-5">
      <div class="container">
        <div class="row text-center mb-4">
          <h2 class="fw-bold">Funcionalidades</h2>
          <p class="text-muted">Tudo que você precisa em um único sistema</p>
        </div>
        <div class="row g-4">
          <div class="col-md-4">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <h5 class="card-title fw-bold">Notas Fiscais</h5>
                <p class="card-text">Emissão de NF-e, NFC-e, CT-e e MDF-e de forma simples e rápida.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <h5 class="card-title fw-bold">Controle de Estoque</h5>
                <p class="card-text">Gerencie entradas, saídas e inventário de forma centralizada.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <h5 class="card-title fw-bold">Relatórios Inteligentes</h5>
                <p class="card-text">Acompanhe indicadores de vendas, despesas e fluxo de caixa em tempo real.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Plans -->
    <section id="plans" class="py-5 bg-light">
      <div class="container">
        <div class="row text-center mb-4">
          <h2 class="fw-bold">Planos</h2>
          <p class="text-muted">Escolha o plano ideal para sua empresa</p>
        </div>
        <div class="row g-4">
          @foreach($planos as $p)
          <div class="col-md-4">
            <div class="card shadow-sm h-100">
              <div class="card-body d-flex flex-column text-center">
                <h5 class="card-title">{{ $p->nome }}</h5>
                <h3 class="fw-bold">R$ {{ __moeda($p->valor) }}</h3>
                <p class="flex-grow-1">{{ $p->descricao_curta }}</p>
                @if($p->dias_teste)
                <span class="text-muted">{{ $p->dias_teste }} dias para testar</span>
                @endif
                <a href="{{ env('APP_URL') }}/register?plano={{ $p->id }}" 
                 class="btn btn-outline-success mt-auto">
                 Assinar
               </a>
             </div>
           </div>
         </div>
         @endforeach

       </div>
     </div>
   </section>

   <!-- Demo -->
   <section id="demo" class="py-5 text-center">
    <div class="container">
      <h2 class="fw-bold">Teste grátis por 7 dias</h2>
      <p class="lead">Cadastre-se e conheça todos os recursos do ERP SaaS.</p>
      <a href="{{ env('APP_URL') }}/register" class="btn btn-success btn-lg">Criar Conta Grátis</a>
    </div>
  </section>

  <!-- Contact -->
  <section id="contact" class="py-5 bg-dark text-light">
    <div class="container">
      <div class="row">
        <div class="col-md-6 mb-3">
          <h4 class="fw-bold">Entre em contato</h4>
          <p>WhatsApp: {{ env('APP_FONE') }}</p>
        </div>
        <div class="col-md-6">
          <form id="whatsappForm">
            <div class="mb-3">
              <input type="text" id="nome" class="form-control" placeholder="Seu nome" required>
            </div>
            <div class="mb-3">
              <textarea id="mensagem" class="form-control" rows="3" placeholder="Sua mensagem" required></textarea>
            </div>
            <button type="submit" class="btn btn-success">Enviar via WhatsApp</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-3 bg-primary text-light text-center">
    <div class="container">
      <p class="mb-0">© {{ date('Y') }} {{ env('APP_NAME') }} - Todos os direitos reservados</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.getElementById("whatsappForm").addEventListener("submit", function(e) {
      e.preventDefault();

      let nome = document.getElementById("nome").value;
      let mensagem = document.getElementById("mensagem").value;

      let numero = "{{ env('APP_FONE') }}";

      let texto = `Olá, meu nome é ${nome}.%0A%0A${mensagem}`;
      window.open(`https://wa.me/${numero}?text=${texto}`, "_blank");
    });
  </script>
</body>
</html>
