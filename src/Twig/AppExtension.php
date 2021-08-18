<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /**
     * Fonction servant à enregistrer les nouveaux filtres que nous avons créé auprès de Twig
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt']),    // Création d'un nouveau filtre appelé "size" dans twig, qui appelera la méthode "size" plus bas dans la page
        ];
    }

    /**
     * Filtre pour compter le nombre de caractère d'une chaîne
     */
    public function size(string $value) : int
    {
        return mb_strlen($value);
    }

    /**
     * Filtre qui retournera la chaîne de texte donnée tronquée à "$nbWords" mots. Si trop petite le filtre retourne juste la chaîne sans y toucher
     */
    public function excerpt(string $text, int $nbWords): string
    {

        $arrayText = explode(' ', $text, ($nbWords + 1));

        if( count($arrayText) > $nbWords ){
            array_pop($arrayText);
            return implode(' ', $arrayText) . '...';
        }

        return $text;

    }
}
