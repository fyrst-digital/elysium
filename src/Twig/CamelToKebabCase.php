<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CamelToKebabCase extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('camel_to_kebab_case', [$this, 'camelToKebabCase']),
        ];
    }

    /**
     * @param string $str
     * @return string
     */
    public function camelToKebabCase(string $str = ''): string
    {
        if (empty($str)) {
            return '';
        }

        $str = preg_replace('!([a-z0-9])([A-Z])!', '$1-$2', $str);

        if (is_string($str)) {
            $str = strtolower($str);
        } else {
            return '';
        }

        return $str;
    }
}
