var cache_width = $('#pdf').width();
var a4 = [595.28, 841.89];

function gerarArquivo() {
    // Setar o width da div no formato a4
    $("#pdf").width((a4[0] * 1.33333) - 80).css('max-width', 'none');
    // $("body").css("display", "none");
    var nomeExame = $('#nome-exame').html();
    var dataExame = $('#data-exame').html();
    dataExame.replace("/", "_");
    dataExame.replace("/", "_");
    dataExame.replace("/", "_");

    // Aqui ele cria a imagem e cria o pdf
    html2canvas($('#pdf'), {
        onrendered: function (canvas) {
            var img = canvas.toDataURL("image/png", 1.0);
            var doc = new jsPDF({ unit: 'px', format: 'a4' });
            doc.addImage(img, 'JPEG', 20, 20);
            doc.save(nomeExame + "_" + dataExame + '.pdf');
            //Retorna ao CSS normal
            $('#renderPDF').width(cache_width);

            var url = window.location.href;
            var redir = url.split("/");
            // location.href = "/agendamento/exams/" + redir[6];
        }
    });
}