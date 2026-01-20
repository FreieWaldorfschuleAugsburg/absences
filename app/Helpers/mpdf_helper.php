<?php

use Mpdf\Mpdf;
use Mpdf\MpdfException;

/**
 * @throws MpdfException
 */
function createMPDF(): Mpdf
{
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 19,
        'margin_right' => 19,
        'margin_top' => 14,
        'margin_bottom' => 45,
        'margin_header' => 19,
        'margin_footer' => 19,
        'orientation' => 'P']);
    $mpdf->setHTMLFooter(view('print/PrintFooter'));
    return $mpdf;
}