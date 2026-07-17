<?php

namespace App\Traits;

trait ResolvesImageUrl
{
    /**
     * Resolve a stored image value to a usable URL. Handles three cases:
     * - our own previously-baked absolute URL (contains "/storage/") from a
     *   possibly stale host/scheme -> rebuilt fresh against the current host
     * - a genuine external URL (admin pasted a third-party image link) -> returned as-is
     * - a bare relative storage path -> resolved against the public disk
     */
    protected function resolveImageUrl(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $path = parse_url($value, PHP_URL_PATH) ?? '';

            if (!str_contains($path, '/storage/')) {
                return $value;
            }

            $relative = preg_replace('#^.*/storage/#', '', $path);

            return $relative !== '' ? \Storage::disk('public')->url(ltrim($relative, '/')) : $value;
        }

        return \Storage::disk('public')->url($value);
    }
}
