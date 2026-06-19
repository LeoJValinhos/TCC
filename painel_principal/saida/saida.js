$(document).ready(function() {
    // Inicializa o Select2 aplicando as funções de customização visual
    $('#selectLote').select2({
        placeholder: "Pesquise por produto, lote ou marca...",
        allowClear: true,
        templateResult: formatarLayoutOpcao,   // Customiza os itens na lista aberta
        templateSelection: formatarItemSelecionado // Customiza o item após selecionado
    });

    function formatarLayoutOpcao(state) {
        // Ignora a linha padrão de placeholder
        if (!state.id) {
            return state.text;
        }

        // Recupera os atributos data mapeados do banco de dados
        var elemento = $(state.element);
        var marca = elemento.data('marca') || '';
        var lote = elemento.data('lote') || 'N/A';
        var qtd = elemento.data('qtd') || '0';
        var validade = elemento.data('validade') || '--/--/----';

        // Cria a estrutura HTML organizada da listagem
        var $html = $(
            '<div class="opcao-customizada-lote">' +
                '<div class="opcao-topo">' +
                    '<span class="opcao-produto">' + state.text + '</span>' +
                    (marca ? '<span class="opcao-marca">(' + marca + ')</span>' : '') +
                '</div>' +
                '<div class="opcao-badges">' +
                    '<span class="badge-invex badge-cinza"><i class="fas fa-barcode"></i> Lote: ' + lote + '</span>' +
                    '<span class="badge-invex badge-verde"><i class="fas fa-boxes"></i> Estoque: ' + qtd + '</span>' +
                    '<span class="badge-invex badge-rosa"><i class="fas fa-calendar-alt"></i> Validade: ' + validade + '</span>' +
                '</div>' +
            '</div>'
        );
        return $html;
    }

    function formatarItemSelecionado(state) {
        if (!state.id) return state.text;
        var elemento = $(state.element);
        var lote = elemento.data('lote') || 'N/A';
        return state.text + ' - (Lote: ' + lote + ')';
    }
});