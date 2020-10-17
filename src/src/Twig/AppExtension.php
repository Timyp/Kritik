<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
//            new TwigFilter('filter_name', [$this, 'doSomething']),
            new TwigFilter('ellipsis', [$this, 'ellipsis'])
        ];
    }

    public function getFunctions(): array
    {
        return [
//            new TwigFunction('function_name', [$this, 'doSomething']),
        ];
    }

    public function ellipsis(?string $value, int $length = 50): string
    {
        //Il n'y a rien à afficher
        if($value === null) {
            return '';
        }

        //Le texte ne dépasse pas la limite
        if(mb_strlen($value) <= $length) {
            return $value;
        }

        $st_start = mb_substr($value, 0, $length - 4);

        return $st_start . ' ...';
    }
}
