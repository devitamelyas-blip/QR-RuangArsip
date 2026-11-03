<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate QR Code SVG string for a bantex slug.
     */
    public function generateForBantex(string $slug, int $size = 180): string
    {
        $appUrl = rtrim(config('app.url'), '/');
        $fullUrl = "{$appUrl}/b/{$slug}";

        return (string) QrCode::size($size)
            ->errorCorrection('M')
            ->generate($fullUrl);
    }
}

