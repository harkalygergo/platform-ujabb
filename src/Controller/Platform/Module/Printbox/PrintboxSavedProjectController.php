<?php

namespace App\Controller\Platform\Module\Printbox;

use App\Controller\Platform\_PlatformAbstractController;
use App\Entity\Platform\Module\Printbox\SavedProjects;
use App\Repository\Platform\Module\Printbox\PrintboxSavedProjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PrintboxSavedProjectController extends _PlatformAbstractController
{
    public function __construct()
    {
    }

    #[Route('/{_locale}/module/printbox/saved-projects/', name: 'module_printbox_saved_projects_list')]
    public function index(Request $request, PrintboxSavedProjectRepository $repository): Response
    {
        $dataList = $repository->findAll();

        $attributes = [
            'site'          => 'Site',
            'customer'      => $this->translator->trans('global.user'),
            'projectHash'   => 'Project hash',
            'projectTitle'  => 'Project title',
            'product'       => 'Product',
            'variant'       => 'Variant',
            'productTitle'  => 'Product title',
            'productCategory' => 'Product category',
            'url'           => 'Url',
        ];

        $data = [
            'title' => 'Printbox mentett projektek',
            'attributes'=> $attributes,
            'dataList'  => $dataList,
            'new'       => false,
            'sidebar' => $this->getSidebarMain($request),
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }

    // create route to get saved projects by customer
    #[Route('/{_locale}/module/printbox/saved-projects/customer/{customer}', name: 'module_printbox_saved_projects_customer')]
    public function getCustomerSavedProjects(SerializerInterface $serializer, Request $request, PrintboxSavedProjectRepository $repository, string $customer): JsonResponse
    {
        $dataList = $repository->findBy(['customer' => $customer]);
        $normalizedDataList = $serializer->normalize($dataList);

        $response = new JsonResponse($normalizedDataList);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    // create route to remove saved project by id, customer and projectHash
    #[Route('/{_locale}/module/printbox/saved-projects/remove/{id}/{customer}/{projectHash}', name: 'module_printbox_saved_projects_remove')]
    public function removeCustomerSavedProject(Request $request, PrintboxSavedProjectRepository $repository, int $id, string $customer, string $projectHash): RedirectResponse
    {
        $repository->removeSavedProject($id, $customer, $projectHash);
        $referer = $request->headers->get('referer');

        // If referer is not available, set a default route to redirect to
        if (!$referer) {
            $referer = $this->generateUrl('default_route'); // Replace 'default_route' with your actual route name
        }

        // Redirect to the referrer
        return new RedirectResponse($referer);
    }

    // create route to save user printbox project
    #[Route('/{_locale}/module/printbox/saved-projects/save', name: 'module_printbox_saved_projects_save', defaults: ['_locale' => 'en'], methods: ['POST'])]
    public function saveUserPrintboxProject(Request $request, ManagerRegistry $doctrine, PrintboxSavedProjectRepository $repository): JsonResponse
    {
        $content = $request->getContent();

        if ($content==='') {
            return new JsonResponse('No content', 400);
        }

        $json = json_decode($content, true);

        if ($json===null) {
            return new JsonResponse('Invalid JSON', 400);
        }

        // check if project already exists with projectID
        $existingProject = $repository->findOneBy(['projectHash' => $json['projectId']]);
        if ($existingProject) {
            $existingProject->setUpdatedAt(new \DateTimeImmutable());
            $existingProject->setProjectTitle($json['projectTitle']);
            $em = $doctrine->getManager();
            $em->persist($existingProject);
            $em->flush();
        } else {
            $savedProject = new SavedProjects();
            $savedProject->setProjectTitle($json['projectTitle']);
            $savedProject->setProduct($json['product']);
            $savedProject->setVariant($json['variant']);
            $savedProject->setProductTitle($json['productTitle']);
            $savedProject->setProductCategory($json['productCategory']);
            $savedProject->setUrl($json['url']);
            $savedProject->setCreatedAt(new \DateTime());
            $savedProject->setCustomer($json['customer']);
            $savedProject->setSite($json['site']);
            $savedProject->setProjectHash($json['projectId']);
            $savedProject->setCreatedAt(new \DateTimeImmutable());

            $em = $doctrine->getManager();
            $em->persist($savedProject);
            $em->flush();
        }

        $response = new JsonResponse($json);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');

        return $response;
    }
}
