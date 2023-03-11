<?php

class PDFGenerator
{
    private string $base;
    public TCPDF $pdf;

    public function __construct(public array $config, public int $w, public int $h)
    {
        $this->base = dirname(__FILE__, 2);

        date_default_timezone_set('UTC');
        ob_start();

        // Set font path
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->base . DS . 'fonts');
        // SEM - note: this doesn't seem to be working

        // Create new PDF document
        $this->pdf = new TCPDF('P', 'mm', [$w, $h], true, 'UTF-8', false);
        Templates::$pdf = $this->pdf;

        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetMargins(0, 0, 0);
        $this->pdf->SetAutoPageBreak(false, 0);
        $this->pdf->setFontSubsetting(true);
        
        // SEM - manually add fonts so we can specify exact location
        $fontPath = $this->base . DS . 'fonts' . DS;
        $this->pdf->AddFont(Loc::_('fonts.font1'), '', $fontPath . Loc::_('fonts.font1'));
        $this->pdf->AddFont(Loc::_('fonts.font2'), '', $fontPath . Loc::_('fonts.font2'));
    }

    public function generate(IGenerator $gen): void
    {
        $gen->generate($this->pdf, $this->config);
    }

    public function output(string $filename): void
    {
        $OUTPUT = ob_get_clean();
        if ($OUTPUT) {
            echo '<pre>', $OUTPUT, '</pre>';
            echo '<p style="color:red">Cannot output PDF due to other output.</p>';
        } else if (PHP_SAPI === 'cli') {
            $this->pdf->Output(BASE . DS . $filename, 'F');
        } else {
            $this->pdf->Output(basename($filename), 'I');
        }
    }
}
