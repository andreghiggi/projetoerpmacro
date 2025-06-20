$(".inp-vendas").select2({
    minimumInputLength: 2,
    language: "pt-BR",
    placeholder: "",
    multiple:true,
    ajax: {
        cache: true,
        url: path_url + "api/vendas/pesquisa",
        dataType: "json",
        data: function (params) {
            console.clear();
            var query = {
                pesquisa: params.term,
                empresa_id: $("#empresa_id").val(),
            };
            return query;
        },
        processResults: function (response) {
            var results = [];
            console.log(response)
            $.each(response, function (i, v) {
                var o = {};
                o.id = (v.tipo == 'pdv' ? 'pdv_' : 'pedido_' ) + v.id;
                o.text = (v.tipo == 'pdv' ? 'PDV ' : 'Pedido ' ) +  "[" +v.numero_sequencial + "] ";
                if(v.cliente){
                o.text += " " + v.cliente.info;
                }
                o.value = v.id;
                results.push(o);
            });
            return {
                results: results,
            };
            
        },
    },
});