<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Escape;

use Mendo\Mvc\View\Helper\ViewHelperInterface;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Escape implements ViewHelperInterface
{
    public function escape($string)
    {
        return $this->html($string);
    }

    public function html($string)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 | ENT_DISALLOWED, 'UTF-8');
    }

    public function url($string)
    {
        return rawurlencode($string);
    }

    public function js($string)
    {
        return preg_replace_callback(
            '/[^a-z0-9,\._]/iSu',
            function ($matches) {
                $chr = $matches[0];
                if (strlen($chr) === 1) {
                    return sprintf('\\x%02X', ord($chr));
                }

                return sprintf('\\u%04s', strtoupper(bin2hex($chr)));
            },
            $string
        );
    }

    public function css($string)
    {
        return preg_replace_callback(
            '/[^a-z0-9]/iSu',
            function ($matches) {
                $chr = $matches[0];
                if (strlen($chr) === 1) {
                    $ord = ord($chr);
                } else {
                    $ord = hexdec(bin2hex($chr));
                }

                return sprintf('\\%X', $ord);
            },
            $string
        );
    }

    public function htmlAttr($string)
    {
        return preg_replace_callback(
            '/[^a-z0-9,\.\-_]/iSu',
            function ($matches) {
                $chr = $matches[0];
                $ord = ord($chr);

                if (
                    ($ord <= 0x1f && $chr != "\t" && $chr != "\n" && $chr != "\r") ||
                    ($ord >= 0x7f && $ord <= 0x9f)
                ) {
                    return '&#xFFFD;';
                }

                $hex = bin2hex($chr);
                $ord = hexdec($hex);

                $htmlNamedEntityMap = [
                    34 => 'quot',
                    38 => 'amp',
                    60 => 'lt',
                    62 => 'gt',
                ];
                if (isset($htmlNamedEntityMap[$ord])) {
                    return '&'.$htmlNamedEntityMap[$ord].';';
                }

                if ($ord > 255) {
                    return sprintf('&#x%04X;', $ord);
                }

                return sprintf('&#x%02X;', $ord);
            },
            $string
        );
    }
}
