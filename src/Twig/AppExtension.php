<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('truncate',       [$this, 'truncate']),
            new TwigFilter('human_filesize',  [$this, 'humanFilesize']),
            new TwigFilter('initials',        [$this, 'initials']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_current_route', [$this, 'isCurrentRoute'], ['needs_context' => true]),
        ];
    }

    /**
     * Truncate a string to a maximum length, appending an ellipsis.
     */
    public function truncate(string $text, int $length = 100, string $suffix = '…'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $length)) . $suffix;
    }

    /**
     * Format a file size in bytes to a human-readable string.
     */
    public function humanFilesize(int $bytes, int $decimals = 1): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i     = 0;
        $size  = (float) $bytes;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            ++$i;
        }

        return round($size, $decimals) . ' ' . $units[$i];
    }

    /**
     * Extract initials from a full name (e.g. "John Doe" → "JD").
     */
    public function initials(string $name, int $max = 2): string
    {
        $words    = preg_split('/\s+/', trim($name));
        $initials = array_map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)), $words);

        return implode('', array_slice($initials, 0, $max));
    }

    /**
     * Returns true if the current route matches the given route name (or prefix).
     */
    public function isCurrentRoute(array $context, string $routeName, bool $prefix = false): bool
    {
        $currentRoute = $context['app']->getRequest()->attributes->get('_route', '');

        return $prefix
            ? str_starts_with($currentRoute, $routeName)
            : $currentRoute === $routeName;
    }
}
