<?php

namespace App\Controller\Platform\Module\Shopify;

use App\Controller\Platform\_PlatformAbstractController;
use App\Controller\Platform\Module\Printbox\PrintboxController;
use App\Entity\Platform\Module\Shopify\ECard;
use App\Repository\Platform\Module\Shopify\ECardRepository;
use Doctrine\Persistence\ManagerRegistry;
use Imagick;
use PharData;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ECardController extends _PlatformAbstractController
{
    public function __construct(public ManagerRegistry $doctrine)
    {
    }

    #[Route('/shopify/ecard/order', name: 'shopify_ecard_webhook')]
    public function webhook(): JsonResponse
    {
        // Get the raw POST data sent by Shopify
        $webhookContent = file_get_contents('php://input');
        $orderDetails = json_decode($webhookContent, true);
        $isECardProductOrdered = false;
        $eCardProjects = [];

        foreach ($orderDetails['line_items'] as $lineItem) {
            $skuFirstPart = explode('-', $lineItem['sku'])['0'];

            if (in_array($skuFirstPart, ['FSMP01', 'FSMS01'])) {
                $eCardProjects[] = $lineItem['properties']['0']['value'];
                $isECardProductOrdered = true;
            }
        }

        if ($isECardProductOrdered) {
            // Save the order JSON to the database
            $eCard = new ECard();
            $eCard->setUserId($orderDetails['customer']['id']);
            $eCard->setProjects(json_encode($eCardProjects, JSON_UNESCAPED_UNICODE));
            $eCard->setOrderJSON($webhookContent);
            $em = $this->doctrine->getManager();
            $em->persist($eCard);
            $em->flush();

            $printboxController = new PrintboxController();
            $printboxController->createECardOrder($eCard);
        }

        return new JsonResponse('Order Webhook');
    }

    #[Route('/shopify/ecard/download/{project}', name: 'shopify_ecard_download_image')]
    public function downloadImage(string $project): Response
    {
        $imagePath = '/tmp/'.$project.'.jpg';

        if (file_exists($imagePath)) {
            $imageData = file_get_contents($imagePath);
            $response = new Response($imageData);

            // Set the content type header to the appropriate image MIME type
            $response->headers->set('Content-Type', 'image/jpeg');
        }

        return $response;
    }

        // get eCard list by user ID added as URL parameter
    #[Route('/shopify/ecard/list/{userId}', name: 'shopify_ecard_list_by_user_id')]
    public function listByUserId(ECardRepository $repository, int $userId): JsonResponse
    {
        $dataList = $repository->findBy(['userId' => $userId]);
        $printboxController = new PrintboxController();
        $output = [];

        foreach ($dataList as $key => $data) {
            foreach(json_decode($data->getProjects(), true) as $project) {
                $projectPDF = json_decode($printboxController->doPrintboxAction('', '', $project, 'viewJSON'), 'true');

                if (array_key_exists('render_url', $projectPDF) && $projectPDF['render_url'] !== null) {
                    $imagePath = '/tmp/' . $project . '.jpg';

                    // if $imagePath is not exists and size is equal to 0, do
                    if (!file_exists($imagePath) || filesize($imagePath) == 0) {
                        $projectPDFURL = $projectPDF['render_url'];
                        // download .tar from $projectPDFURL to /tmp
                        $tmpTarPath = '/tmp/' . $project . '.tar';
                        file_put_contents($tmpTarPath, file_get_contents($projectPDFURL));
                        $phar = new PharData('/tmp/' . $project . '.tar');
                        if (!is_dir('/tmp/' . $project)) {
                            $phar->extractTo('/tmp/' . $project); // creates /tmp/$project folder
                        }
                        // delete .tar
                        unlink($tmpTarPath);
                        // get .pdf from /tmp/$project folder
                        $pdfFiles = glob('/tmp/' . $project . '/*.pdf');
                        $pdfFile = $pdfFiles['0'];
                        // create jpg from pdf
                        $pdf = new Imagick($pdfFile);
                        $pdf->setIteratorIndex(0);
                        // set image quality to 100 and size is 1200 pixel * 1200 pixel
                        $pdf->setImageCompressionQuality(100);
                        $pdf->resizeImage(1200, 1200, Imagick::FILTER_LANCZOS, 1);
                        $pdf->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
                        $pdf->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
                        $pdf->setImageFormat('jpg');
                        $pdf->writeImage('/tmp/' . $project . '.jpg');
                        // delete pdf
                        unlink($pdfFile);
                    }

                    // add jpg to output
                    $output[] = [
                        'name'      => $projectPDF['name'],
                        'download'  => '/shopify/ecard/download/' . $project
                    ];
                }
            }
        }

        return new JsonResponse($output);
    }

    #[Route('/{_locale}/shopify/ecard/list/', name: 'shopify_ecard_list')]
    public function list(ECardRepository $repository): Response
    {
        $dataList = $repository->findAll();

        $data = [
            'title' => '<i class="bi bi-card-list"></i> eCard',
            'dataList' => $dataList
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }
}
