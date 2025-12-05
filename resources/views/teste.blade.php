<!doctype html>
  <html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js 4 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <style>
      :root{
        --bg: #f4f5f7;
        --text-muted: #6b7280;
        --ink: #111827;
        --card: #ffffff;
        --shadow: 0 8px 24px rgba(0,0,0,.06);
        --violet-50: #ede9fe;
        --violet-400: #8b5cf6;
        --indigo-600: #4f46e5;
        --slate-700: #334155;
        --success: #16a34a;
        --warning: #f59e0b;
        --teal: #10b981;
        --teal-200: rgba(16,185,129,.18);
        --blue: #3b82f6;
        --blue-200: rgba(59,130,246,.18);
        --rose: #ef4444;
        --rose-200: rgba(239,68,68,.16);
        --radius: 16px;
      }
      body{ background:var(--bg); }
      .container-xl{ max-width: 1280px; }

      /* Header topo direito (breadcrumb) */
      .crumb-top{ color:#606774; font-weight:600; }

      /* Cards base */
      .card{
        border:0; border-radius: var(--radius); box-shadow: var(--shadow);
        background: var(--card);
      }
      .mini-card .icon{
        width:44px;height:44px;border-radius:12px;display:grid;place-items:center;font-size:1.2rem;
        background:#eef2ff;color:#4338ca;
      }
      .mini-card .value{ font-size:1.6rem; font-weight:800; }

      /* Welcome card */
      .welcome-top{
        background: #cdd0ff; /* lilás suave como no print */
        background: linear-gradient(135deg,#cfd2ff 0%, #dcd6ff 100%);
        border-radius: 14px;
        padding:16px 16px 6px 16px;
        color:#1f2544;
        font-weight: 700;
      }
      .welcome-top small{ font-weight:600; opacity:.9 }
      .welcome-body .avatar{
        width:72px; height:72px; border-radius:50%; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,.15);
      }
      .btn-deep{ background:#2c3557; color:#fff; border:0; }
      .btn-deep:hover{ background:#242c4a; color:#fff; }

      /* Chart containers fixos (não descem a tela) */
      .chart-box{ position:relative; height: 360px; }
      @media (max-width: 768px){ .chart-box{ height: 260px; } }

      /* Donut */
      .ring { position:relative; height: 180px; }
      @media (max-width: 768px){ .ring{ height: 160px; } }
      .hint{ color:var(--text-muted); font-size:.95rem; }

      /* Legenda custom (cores iguais às do Chart.js) */
      .legend-dot{
        display:inline-block;width:10px;height:10px;border-radius:50%;margin-right:.4rem;
      }

      .header-card {
  background-color: #7963f2;
  background-image: url('https://cdn-icons-png.flaticon.com/512/4149/4149676.png');
  background-repeat: no-repeat;
  background-position: right 15px bottom 5px;
  background-size: 70px;
  height: 80px;
  position: relative;
}

.avatar-wrapper {
  position: absolute;
  top: 45px;
  left: 0;
  right: 0;
}

.avatar-img {
  background-color: #fff;
}

.card {
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.1);
}


      /* Títulos */
      h5.card-title{ font-weight:700; }
    </style>
  </head>
  <body>
    <div class="container-xl py-4">
      <div class="d-flex justify-content-end">
        <div class="crumb-top">Dashboard / Dashboard</div>
      </div>

      <!-- Linha 1: Welcome + 3 KPIs -->
      <div class="row g-3 align-items-stretch mt-2">
        <!-- Welcome -->
        <div class="col-12 col-lg-5">
          <!-- Card Boas-vindas -->
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="max-width: 380px; position: relative;">
  <!-- Cabeçalho -->
  <div class="p-3 text-white header-card text-start">
    <h6 class="fw-bold mb-0">Seja Bem Vindo!</h6>
    <small class="opacity-75">Área Administrativa</small>
  </div>

  <!-- Avatar -->
  <div class="avatar-wrapper text-center">
    <img src="https://i.pravatar.cc/100" class="avatar-img rounded-circle border border-3 border-white shadow" width="80" height="80" alt="Foto de perfil">
  </div>

  <!-- Corpo -->
  <div class="card-body text-center pt-4">
    <h6 class="fw-semibold mb-0 text-truncate">Cesar Henrique de...</h6>
    <small class="text-muted d-block mb-2">Desenvolvimento de sistemas</small>

    <div class="d-flex justify-content-center align-items-center mt-3">
      <div class="me-4 text-muted small">
        <span class="d-block fw-bold fs-6 text-dark">6</span>
        Produtos
      </div>
      <div class="border-start ps-4 text-muted small">
        <span class="d-block fw-bold fs-6 text-success">R$ 7.203,43</span>
        Vendas Total
      </div>
    </div>
  </div>

  <!-- Rodapé -->
  <div class="card-footer bg-white border-0 text-center pb-4">
    <button class="btn btn-primary px-4 rounded-3">Abrir</button>
  </div>
</div>


    </div>

    <!-- KPI 1 -->
    <div class="col-12 col-lg-7">
      <div class="row g-3">
        <div class="col-12 col-md-4">
          <div class="card mini-card h-100">
            <div class="card-body d-flex align-items-start justify-content-between">
              <div>
                <div class="text-muted small fw-semibold">Receita Líquida</div>
                <div class="value mt-1" id="kpi-rec-liq">R$ 4.179,16</div>
                <a class="small" href="#">Estimativa sobre as<br> vendas</a>
              </div>
              <div class="icon"><i class="bi bi-coin"></i></div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card mini-card h-100">
            <div class="card-body d-flex align-items-start justify-content-between">
              <div>
                <div class="text-muted small fw-semibold">Custo Produto</div>
                <div class="value mt-1" id="kpi-custo">R$ 3.024,27</div>
                <a class="small" href="#">Custo sobre os<br> produtos</a>
              </div>
              <div class="icon"><i class="bi bi-coin"></i></div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card mini-card h-100">
            <div class="card-body d-flex align-items-start justify-content-between">
              <div>
                <div class="text-muted small fw-semibold">Em Estoque</div>
                <div class="value mt-1" id="kpi-estoque">R$ 00</div>
                <a class="small" href="#">Valor parado sobre<br> produtos</a>
              </div>
              <div class="icon"><i class="bi bi-exclamation-circle"></i></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Linha 2: Venda Mensal + Gráfico principal -->
  <div class="row g-3 mt-1">
    <!-- Venda Mensal -->
    <div class="col-12 col-xl-5">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Venda Mensal</h5>
          <div class="text-primary fw-semibold">Este Mês</div>

          <div class="row g-3 align-items-center mt-2">
            <div class="col-6">
              <div class="fs-2 fw-bold" id="venda-mensal">R$ 0,00</div>
              <div class="text-success fw-semibold">0,00%</div>

              <button class="btn btn-outline-primary btn-sm mt-3">Ver Detalhado</button>
              <p class="hint mt-3 mb-0">Projeção calculada com base nas transações recentes.</p>
            </div>
            <div class="col-6">
              <div class="ring">
                <canvas id="donutUso"></canvas>
              </div>
              <div class="text-center hint">Uso Plano NF</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Gráfico principal -->
    <div class="col-12 col-xl-7">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Receita Bruta vs Custo por produto - Mês</h5>
          <div class="text-center hint mb-2">Desempenho Mensal</div>
          <div class="chart-box">
            <canvas id="chartArea"></canvas>
          </div>

          <!-- legenda estilo do print -->
          <div class="d-flex justify-content-center gap-4 mt-3">
            <span><i class="legend-dot" style="background: rgba(16,185,129,.8)"></i>Receitas</span>
            <span><i class="legend-dot" style="background: rgba(239,68,68,.8)"></i>Custos</span>
            <span><i class="legend-dot" style="background: rgba(59,130,246,.8)"></i>Lucro Bruto</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ===== Helpers =====
    const brl = v => v.toLocaleString('pt-BR',{style:'currency',currency:'BRL'});

    // ===== Donut (Uso Plano NF) =====
    const ctxDonut = document.getElementById('donutUso');
    const donut = new Chart(ctxDonut, {
      type: 'doughnut',
      data: {
        labels: ['Usado', 'Livre'],
        datasets: [{
          data: [0, 100],
          backgroundColor: ['#3b82f6', '#e5e7eb'],
          borderWidth: 0,
          hoverOffset: 3
        }]
      },
      options: {
        responsive:true,
        maintainAspectRatio:false,
        cutout: '70%',
        plugins: { legend: { display:false }, tooltip:{ enabled:false } }
      }
    });

    function updateDonut(perc){
      const p = Math.max(0, Math.min(100, perc|0));
      donut.data.datasets[0].data = [p, 100 - p];
      donut.update();
    }

    // ===== Área: Receita x Custo x Lucro =====
    const ctxArea = document.getElementById('chartArea');
    const area = new Chart(ctxArea, {
      data: {
        labels: [],
        datasets: [
          // Receitas (verde) - área
          { type:'line', label:'Receitas', data:[], tension:.35, pointRadius:3,
          fill:true, backgroundColor:'rgba(16,185,129,.18)', borderColor:'rgba(16,185,129,1)', borderWidth:2 },
          // Custos (vermelho) - área
          { type:'line', label:'Custos', data:[], tension:.35, pointRadius:3,
          fill:true, backgroundColor:'rgba(239,68,68,.16)', borderColor:'rgba(239,68,68,1)', borderWidth:2 },
          // Lucro (azul) - linha
          { type:'line', label:'Lucro Bruto', data:[], tension:.35, pointRadius:3,
          fill:false, borderColor:'rgba(59,130,246,1)', borderWidth:2 }
          ]
        },
        options:{
          responsive:true,
          maintainAspectRatio:false,
          scales:{
            x:{ grid:{ display:false } },
            y:{ ticks:{ callback:v=> 'R$ ' + v.toLocaleString('pt-BR') } }
          },
          plugins:{
            legend: { display:false },
            tooltip: {
              callbacks:{
                label: ctx => `${ctx.dataset.label}: ${brl(ctx.parsed.y)}`
              }
            }
          }
        }
      });

    /**
     * Atualiza o gráfico principal com arrays (formato A):
     * updateChartData(
     *  [500, 800, ...],   // receitas
     *  [300, 500, ...],   // custos
     *  [200, 300, ...],   // lucro
     *  ['mar. de 25', ...]// labels
     * )
     */
     function updateChartData(receitas=[], custos=[], lucro=[], labels=[]){
      area.data.labels = labels;
      area.data.datasets[0].data = receitas;
      area.data.datasets[1].data = custos;
      area.data.datasets[2].data = lucro;
      area.update();

      // KPIs e "Venda Mensal" com base nos arrays
      const receitaTotal = receitas.reduce((a,b)=>a+b,0);
      const custoTotal   = custos.reduce((a,b)=>a+b,0);
      const lucroTotal   = lucro.reduce((a,b)=>a+b,0);

      document.getElementById('kpi-rec-liq').textContent = brl(receitaTotal - custoTotal * 0.03); // exemplificando taxa/ajuste
      document.getElementById('kpi-custo').textContent   = brl(custoTotal);
      document.getElementById('venda-mensal').textContent= brl(receitaTotal);

      // Donut fictício: % de uso proporcional ao número de pontos válidos
      const percUso = Math.round((receitas.filter(v=>v>0).length / (labels.length||1)) * 100);
      updateDonut(percUso);
    }

    // ===== DEMO inicial (iguais ao aspecto do print) =====
    const labelsDemo = ['mar. de 25','abr. de 25','mai. de 25','jun. de 25','jul. de 25','ago. de 25'];
    const receitasDemo = [100, 800, 1600, 4800, 0, 20];
    const custosDemo   = [80, 500, 1100, 2100, 0, 10];
    const lucroDemo    = receitasDemo.map((r,i)=> Math.max(0, r - custosDemo[i])); // simples

    updateChartData(receitasDemo, custosDemo, lucroDemo, labelsDemo);
    updateDonut(0); // começa zerado
  </script>
</body>
</html>
