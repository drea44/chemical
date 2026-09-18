<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QRCodeService
{

    public function generate(string $chemicalCode, string $content): string
    {
        $filename  = 'qrcodes/' . $chemicalCode . '.svg';
        $publicPath = public_path($filename);

        if (!is_dir(public_path('qrcodes'))) {
            mkdir(public_path('qrcodes'), 0755, true);
        }

        try {
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $writer->writeFile($content, $publicPath);
        } catch (\Throwable $e) {

            $svg = $this->generatePlaceholderSvg($chemicalCode);
            file_put_contents($publicPath, $svg);
        }

        return '/' . $filename;
    }

    public function delete(string $chemicalCode): void
    {
        $path = public_path('qrcodes/' . $chemicalCode . '.svg');
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function buildContent(string $chemicalCode): string
    {
        return 'CHEM:' . $chemicalCode;
    }

    private function generatePlaceholderSvg(string $code): string
    {
        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
          <rect width="200" height="200" fill="white" stroke="#ddd"/>
          <text x="100" y="100" font-family="monospace" font-size="12" text-anchor="middle" fill="#333">{$code}</text>
          <text x="100" y="120" font-family="monospace" font-size="10" text-anchor="middle" fill="#999">QR Code</text>
        </svg>
        SVG;
    }
}

