<?php namespace Spreadsheet\Writer\Pdf;

class Mpdf extends \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf
{
    protected function createExternalWriterInstance($config)
    {
        //  Create PDF
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $fontDirs[] = base_path('resources/fonts');
        $config['fontDir'] = $fontDirs;

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $fontData['deng-xian'] = [
            'R' => 'deng.ttf',
            'B' => 'dengb.ttf',
        ];
        $config['fontdata'] = $fontData;
        $config['default_font'] = 'deng-xian';

        $config['format'] = 'a4';
        $config['margin_left'] = 6;
        $config['margin_right'] = 6;
        $config['margin_top'] = 3;
        $config['margin_bottom'] = 3;
        $config['margin_header'] = 0;
        $config['margin_footer'] = 0;
        $config['orientation'] = 'P';
        $config['default_font_size'] = 0;

        //$pdf->SetMargins(1, 1, 1);
        //$pdf->useAdobeCJK = true;
        //$pdf->autoScriptToLang = true;
        //$pdf->autoLangToFont = true;

        $pdf = new \Mpdf\Mpdf($config);
        return $pdf;
    }
}
