<script>

const CONFIG = {

    simbolo: <?= json_encode($config['simbolo_moeda']) ?>,

    codigoMoeda: <?= json_encode($config['codigo_moeda'] ?? 'BRL') ?>,

    casas: <?= (int)$config['casas_decimais'] ?>,

    formatoData: <?= json_encode($config['formato_data']) ?>

};

</script>

<script src="../../js/formata.js"></script>