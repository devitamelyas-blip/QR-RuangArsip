<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generateForBantex(string $slug, int $size = 180): string
    {
        $appUrl = rtrim(config('app.url'), '/');
        $fullUrl = "{$appUrl}/b/{$slug}";

        return (string) QrCode::size($size)
            ->errorCorrection('M')
            ->generate($fullUrl);
    }
}
