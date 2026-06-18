<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Administration\Controller;

use Blur\BlurElysiumSlider\Service\SlideCoverImageSwitchService;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['administration']])]
class SlideCoverImageController extends AbstractController
{
    public function __construct(
        private readonly SlideCoverImageSwitchService $switchService
    ) {
    }

    #[Route(path: '/api/_action/elysium-slides/switch-cover-images', name: 'api.action.elysium-slides.switch-cover-images', methods: ['POST'])]
    public function switchCoverImages(Context $context): JsonResponse
    {
        $affected = $this->switchService->switch($context);

        return new JsonResponse([
            'affected' => $affected,
        ]);
    }
}
