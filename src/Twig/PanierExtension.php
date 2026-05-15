<?php
namespace App\Twig;

use App\Service\PanierService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PanierExtension extends AbstractExtension
{
    public function __construct(private PanierService $panierService) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('panier_count', [$this->panierService, 'getNombreArticles']),
        ];
    }
}