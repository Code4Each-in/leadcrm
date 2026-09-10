<?php

namespace App\Support;

use Illuminate\Http\Request;

class DeviceDetector
{
    public static function isMobile(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        return preg_match(
            '/android.*mobile|iphone|ipod|windows phone|blackberry|opera mini|opera mobi/',
            $userAgent
        ) === 1;
    }

    public static function isTablet(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        // iPad
        if (str_contains($userAgent, 'ipad')) {
            return true;
        }

        // Android tablets usually contain Android but not "Mobile"
        if (
            str_contains($userAgent, 'android') &&
            !str_contains($userAgent, 'mobile')
        ) {
            return true;
        }

        return false;
    }

    public static function isDesktop(Request $request): bool
    {
        return !self::isMobile($request) && !self::isTablet($request);
    }

    public static function type(Request $request): string
    {
        if (self::isMobile($request)) {
            return 'mobile';
        }

        if (self::isTablet($request)) {
            return 'tablet';
        }

        return 'desktop';
    }
}
