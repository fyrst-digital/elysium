<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Preview\Renderer;

use Blur\BlurElysiumSlider\Preview\PreviewFragment;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Renders preview fragments using Twig templates.
 */
class PreviewFragmentRenderer
{
    public function __construct(private readonly Environment $twig)
    {
    }

    public function render(PreviewFragment $fragment, array $context): Response
    {
        $template = $fragment->getTemplate();

        if ($template === null) {
            throw new \RuntimeException(sprintf(
                'Fragment "%s" does not define a template.',
                $fragment->getName()
            ));
        }

        $html = $this->twig->render($template, $context);

        return new Response($html);
    }
}
