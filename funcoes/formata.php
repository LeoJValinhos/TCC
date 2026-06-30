<?php
function formatar_moeda($valor, $config) {
$decimais = $config['casas_decimais'] ?? 2;
$simbolo = $config['simbolo_moeda'] ?? 'R$';

return $simbolo . ' ' . number_format($valor, $decimais, ',', '.');

}

function formatar_data($data, $config) {
$formato = $config['formato_data'] ?? 'd/m/Y';

return date($formato, strtotime($data));

}

function formatar_numero($valor, $config) {
$decimais = $config['casas_decimais'] ?? 2;

return number_format($valor, $decimais, ',', '.');

}
?>