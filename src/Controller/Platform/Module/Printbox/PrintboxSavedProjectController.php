<?php

namespace App\Controller\Platform\Module\Printbox;

use App\Controller\Platform\_PlatformAbstractController;
use App\Entity\Platform\Module\Printbox\SavedProjects;
use App\Repository\Platform\Module\Printbox\PrintboxSavedProjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    /*
     *             case 'getCustomerSavedProjects': {
                $database = (new Database())->getConnection();
                $stmt = $database->prepare("SELECT * FROM printboxProjects WHERE customer = :customer");
                $stmt->bindParam(':customer', $_GET['customer']);
                $result = $stmt->execute();
                return json_encode($this->sqliteFetchAll($result), JSON_UNESCAPED_UNICODE);
                break;
            }
     */

    // create route to get saved projects by customer
    #[Route('/{_locale}/module/printbox/saved-projects/customer/{customer}', name: 'module_printbox_saved_projects_customer')]
    public function getCustomerSavedProjects(SerializerInterface $serializer, Request $request, PrintboxSavedProjectRepository $repository, string $customer): JsonResponse
    {
        $dataList = $repository->findBy(['customer' => $customer]);
        $normalizedDataList = $serializer->normalize($dataList);

        // return with PrintboxSavedProjects from datalist as JSON
        return new JsonResponse($normalizedDataList);
    }

    /*
     *             case 'removeCustomerSavedProject': {
                $id = $_GET['id'];
                $customer = $_GET['customer'];
                $projectHash = $_GET['projectHash'];

                $database = (new Database())->getConnection();
                $stmt = $database->prepare("DELETE FROM printboxProjects WHERE id = :id AND customer = :customer AND projectHash = :projectHash");
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':customer', $customer);
                $stmt->bindParam(':projectHash', $projectHash);
                $result = $stmt->execute();

                header('Location:'.$_SERVER['HTTP_REFERER'].'account');
                exit;
                return true;
                break;
            }*/
    // create route to remove saved project by id, customer and projectHash
    #[Route('/{_locale}/module/printbox/saved-projects/remove/{id}/{customer}/{projectHash}', name: 'module_printbox_saved_projects_remove')]
    public function removeCustomerSavedProject(Request $request, PrintboxSavedProjectRepository $repository, int $id, string $customer, string $projectHash): Response
    {
        $result = $repository->removeSavedProject($id, $customer, $projectHash);

        if ($result) {
            return $this->redirectToRoute('module_printbox_saved_projects_list');
        }

        return $this->redirectToRoute('module_printbox_saved_projects_list');
    }

    /*
     *             case 'saveUserPrintboxProject': {
                if( empty($_POST) ){ $_POST = json_decode(file_get_contents('php://input'), true); }
                $database = new Database();
                $database = $database->getConnection();

                $projectHash = $_POST["projectId"];
                $timestamp = time();

                // check if in database
                $stmt = $database->prepare("SELECT * FROM printboxProjects WHERE projectHash = :projectHash");
                $stmt->bindParam(':projectHash', $projectHash);
                $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

                // if false, not exists, insert, else update updatedAt
                if ($result===false) {
                    $stmt = $database->prepare('INSERT INTO printboxProjects (site, createdAt, customer, projectHash, projectTitle, product, variant, productTitle, productCategory, url) VALUES (:site, :createdAt, :customer, :projectHash, :projectTitle, :product, :variant, :productTitle, :productCategory, :url)');
                    $stmt->bindValue(':site', $_POST['site'], SQLITE3_TEXT);
                    $stmt->bindValue(':createdAt', $timestamp, SQLITE3_INTEGER);
                    $stmt->bindValue(':customer', $_POST['customer'], SQLITE3_INTEGER);
                    $stmt->bindValue(':projectHash', $projectHash, SQLITE3_TEXT);
                    $stmt->bindValue(':projectTitle', $_POST['projectTitle'], SQLITE3_TEXT);
                    $stmt->bindValue(':product', $_POST['product'], SQLITE3_INTEGER);
                    $stmt->bindValue(':variant', $_POST['variant'], SQLITE3_INTEGER);
                    $stmt->bindValue(':productTitle', $_POST['productTitle'], SQLITE3_TEXT);
                    $stmt->bindValue(':productCategory', $_POST['productCategory'], SQLITE3_TEXT);
                    $stmt->bindValue(':url', $_POST['url'], SQLITE3_TEXT);

                    $result = $stmt->execute();

                    if ($result === false) {
                        throw new Exception('Failed to insert data: ' . $database->lastErrorMsg());
                    } else {
                        return "New task inserted successfully!";
                    }
                } else {
                    $stmt = $database->prepare("UPDATE printboxProjects SET updatedAt = :updatedAt, projectTitle = :projectTitle WHERE id = :id");
                    $stmt->bindParam(':updatedAt', $timestamp);
                    $stmt->bindParam(':projectTitle', $_POST['projectTitle']);
                    $stmt->bindParam(':id', $result['ID']);
                    $stmt->execute();
                }

                return json_encode($result);
                break;
            }
     * */
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

        $response = new JsonResponse($json);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
