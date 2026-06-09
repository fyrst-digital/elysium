<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Administration\Controller;

use Blur\BlurElysiumSlider\Service\ImportExport\SlideExportService;
use Blur\BlurElysiumSlider\Service\ImportExport\SlideImportService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\Since;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Shopware\Core\Framework\Routing\RoutingException;

#[Route(defaults: ['_routeScope' => ['administration']])]
class SlideImportExportController extends AbstractController
{
    public function __construct(
        private readonly SlideExportService $exportService,
        private readonly SlideImportService $importService
    ) {
    }

    #[Route(path: '/api/_action/elysium-slides/export', name: 'api.action.elysium-slides.export', methods: ['POST'])]
    public function export(Request $request, Context $context): Response
    {
        $ids = $request->request->all('ids');

        if (!is_array($ids) || empty($ids)) {
            throw RoutingException::missingRequestParameter('ids');
        }

        $jsonl = $this->exportService->export($ids, $context);

        $filename = sprintf('elysium-slides-export-%s.jsonl', (new \DateTime())->format('Y-m-d-His'));

        return new Response(
            $jsonl,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/jsonl',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    #[Route(path: '/api/_action/elysium-slides/import', name: 'api.action.elysium-slides.import', methods: ['POST'])]
    public function import(Request $request, Context $context): JsonResponse
    {
        $file = $request->files->get('file');

        if (!$file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            throw RoutingException::missingRequestParameter('file');
        }

        if ($file->getClientOriginalExtension() !== 'jsonl' && $file->getMimeType() !== 'application/jsonl') {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid file format. Expected a .jsonl file.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $content = file_get_contents($file->getPathname());

        if ($content === false) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to read the uploaded file.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->importService->import($content, $context);

        return new JsonResponse([
            'success' => empty($result->getErrors()),
            'imported' => $result->getImported(),
            'errors' => $result->getErrors(),
        ]);
    }
}
