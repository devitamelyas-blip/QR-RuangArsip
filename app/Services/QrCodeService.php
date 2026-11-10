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

    public function generateForRoom(string $roomKey, int $size = 260): string
    {
        $fullUrl = $this->getRoomUrl($roomKey);

        return (string) QrCode::size($size)
            ->errorCorrection('H')
            ->generate($fullUrl);
    }

    public function getRoomUrl(string $roomKey): string
    {
        $appUrl = rtrim(config('app.url'), '/');

        return "{$appUrl}/ruang/{$roomKey}";
    }
}
