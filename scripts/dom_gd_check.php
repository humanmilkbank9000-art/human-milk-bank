<?php
// scripts/dom_gd_check.php
// Simple Dompdf + GD sanity check. Creates storage/app/dom_pdf_test.pdf

require __DIR__ . '/../vendor/autoload.php';

$html = <<<HTML
<!doctype html>
<html><body>
<h1>GD Test</h1>
<p>Inline PNG below should render if GD is enabled.</p>
<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PqhJtwAAAABJRU5ErkJggg==" alt="dot" />
</body></html>
HTML;

$dompdf = new Dompdf\Dompdf();
$dompdf->loadHtml($html);
$dompdf->render();

$outPath = __DIR__ . '/../storage/app/dom_pdf_test.pdf';
file_put_contents($outPath, $dompdf->output());

$size = is_file($outPath) ? filesize($outPath) : 0;

echo $size > 0 ? "OK $size\n" : "FAIL\n";
