<?php

require "../../../vendor/autoload.php";

$tipo = $_GET["tipo"] ?? "usuarios";

use Dompdf\Dompdf;
$dompdf = new Dompdf();
ob_start();
require __DIR__ . "/../../../src/view/administrativo/relatorios/conteudo-pdf.php";
$html = ob_get_clean();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4');
$dompdf->render();
$filename = 'Relatorio-'.$tipo.'-' . date('dmY') . '.pdf';
$dompdf->stream($filename, ['Attachment' => 1]);

?>

