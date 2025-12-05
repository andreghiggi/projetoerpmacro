Chart.register(ChartDataLabels);
function formatBrl(v) {
    return 'R$ ' + convertFloatToMoeda(v)
}

function getData(el, key) {
    return JSON.parse(el.dataset[key] || '[]')
}

function gerarCores(qtd) {
    const paleta = [
    '#4254BA',
    '#2196F3',
    '#FFC107',
    '#F44336',
    '#9C27B0',
    '#00BCD4',
    '#FF5722',
    '#795548',
    '#3F51B5',
    '#8BC34A',
    '#E91E63',
    '#009688'
    ]

    return paleta.slice(0, qtd)
}


function criarPizza(id) {
    const el = document.getElementById(id)
    if (!el) return

        const existing = Chart.getChart(el)
    if (existing) existing.destroy()

        const labels = getData(el, 'labels')
    const valores = getData(el, 'valores')
    const cores = gerarCores(valores.length)

    const total = valores.reduce((a, b) => a + b, 0)

    new Chart(el, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: valores,
                backgroundColor: cores,
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            let nome = ctx.label
                            let valor = ctx.raw
                            let percentual = total > 0 
                            ? ((valor / total) * 100).toFixed(1).replace('.', ',')
                            : '0,0'

                            return `${nome}: ${formatBrl(valor)} (${percentual}%)`
                        }
                    }
                },

                datalabels: {
                    color: '#fff',
                    font: { weight: 'bold', size: 12 },
                    formatter: (value, ctx) => {
                        const sum = ctx.chart.data.datasets[0].data
                        .reduce((a, b) => a + b, 0)

                        if (sum === 0) return ''

                            const pct = ((value / sum) * 100)
                        .toFixed(1)
                        .replace('.', ',')

                        return pct + '%'
                    }
                },

                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true }
                }
            }
        }

    })
}

function criarFluxo() {
    const el = document.getElementById('graficoFluxo')
    if (!el) return

        const ctx = el.getContext('2d')
    const labels = getData(el, 'labels')
    const pagar = getData(el, 'pagar')
    const receber = getData(el, 'receber')

    const gPagar = ctx.createLinearGradient(0, 0, 0, 300)
    gPagar.addColorStop(0, 'rgba(255,99,132,0.35)')
    gPagar.addColorStop(1, 'rgba(255,99,132,0)')

    const gReceber = ctx.createLinearGradient(0, 0, 0, 300)
    gReceber.addColorStop(0, 'rgba(75,192,192,0.35)')
    gReceber.addColorStop(1, 'rgba(75,192,192,0)')

    new Chart(el, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
            {
                label: 'Contas a Pagar',
                data: pagar,
                borderColor: 'rgb(255,99,132)',
                backgroundColor: gPagar,
                tension: 0.35,
                borderWidth: 3,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb(255,99,132)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                fill: true
            },
            {
                label: 'Contas a Receber',
                data: receber,
                borderColor: 'rgb(75,192,192)',
                backgroundColor: gReceber,
                tension: 0.35,
                borderWidth: 3,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb(75,192,192)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                fill: true
            }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${formatBrl(context.raw ?? 0)}`
                        }
                    }
                },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    offset: 10,

                    color: '#ffffff',
                    textStrokeColor: '#000000',
                    textStrokeWidth: 3,

                    shadowBlur: 4,
                    shadowColor: 'rgba(0,0,0,0.7)',

                    font: {
                        size: 13,
                        weight: 'bold'
                    },

                    formatter: function (value) {
                        return formatBrl(value)
                    }
                }

            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => formatBrl(v)
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    })
}

function criarLucro() {
    const el = document.getElementById('graficoLucro')
    if (!el) return

        const existing = Chart.getChart(el)
    if (existing) existing.destroy()

        const labels = getData(el, 'labels')
    const valores = getData(el, 'valores')

    const ctx = el.getContext('2d')

    const gradient = ctx.createLinearGradient(0, 0, 0, 250)
    gradient.addColorStop(0, 'rgba(76, 175, 80, 0.5)')   // verde
    gradient.addColorStop(1, 'rgba(76, 175, 80, 0.05)')

    new Chart(el, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Lucro Mensal',
                data: valores,
                backgroundColor: gradient,
                borderColor: '#4CAF50',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.dataset.label}: ${formatBrl(ctx.raw)}`
                    }
                },
                legend: {
                    display: false
                }
            },

            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: v => formatBrl(v)
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    })
}


document.addEventListener('DOMContentLoaded', () => {
    criarFluxo()
    criarPizza('graficoPagarCategoria')
    criarPizza('graficoReceberCategoria')
    criarLucro()
})
