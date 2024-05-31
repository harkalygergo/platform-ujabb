<?php

namespace App\Controller\Platform;

use App\Entity\Platform\InstanceFile;
use App\Entity\Platform\User;
use App\Form\Platform\FileUploadType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

// grant access at least ROLE_USER
#[IsGranted(User::ROLE_USER)]
class InstanceFileController extends _PlatformAbstractController
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }

    // create a function to list all files uploaded to the instance as InstanceStorage
    #[Route('/{_locale}/admin/instance/storage/', name: 'admin_instance_storage')]
    public function listFiles(): Response
    {
        $instanceStorageRepository = $this->doctrine->getRepository(InstanceFile::class);
        $dataList = $instanceStorageRepository->findBy(['instance' => $this->getUser()->getDefaultInstance()]);

        $attributes = [
            'originalName' => 'Fájlnév',
            'type' => 'Típus',
            'size' => 'Méret (byte)',
            'public' => 'Publikus',
        ];

        $data = [
            'title' => 'Instance tárhely',
            'dataList'  => $dataList,
            'attributes' => $attributes,
            'edit'      => false,
            'duplicate' => false,
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }

    // create a form to upload files to the instance as InstanceStorage
    #[Route('/{_locale}/admin/instance/storage/new', name: 'admin_instance_storage_new')]
    public function upload(Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(FileUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFiles = $form->get('file')->getData();

            if ($uploadedFiles) {

                foreach ($uploadedFiles as $uploadedFile) {
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

                    try {
                        // get uploaded file mime type
                        $mimeType = $uploadedFile->getMimeType();
                        $size = $uploadedFile->getSize();

                        $uploadedFile->move(
                        //$this->getParameter('uploads_directory'),
                            $this->getParameter('storage_directory').'/instance/'.$this->getUser()->getDefaultInstance()->getId(),
                            $newFilename
                        );

                        // save the file to the database as InstanceStorage
                        $instanceStorage = new InstanceFile();
                        $instanceStorage->setInstance($this->getUser()->getDefaultInstance());
                        $instanceStorage->setPath($newFilename);
                        $instanceStorage->setOriginalName($originalFilename);
                        $instanceStorage->setType($mimeType);
                        $instanceStorage->setSize($size);
                        $instanceStorage->setCreatedAt(new \DateTime());
                        $instanceStorage->setPublic(true);
                        $instanceStorage->setUser($this->getUser());
                        // save instanceStorage
                        $entityManager = $this->doctrine->getManager();
                        $entityManager->persist($instanceStorage);
                        $entityManager->flush();
                    } catch (FileException $e) {
                        // Handle exception if something happens during file upload
                        $this->addFlash('danger', 'Failed to upload file.');
                        return $this->redirectToRoute('admin_instance_storage');
                    }
                }

                $this->addFlash('success', 'File(s) uploaded successfully.');

                return $this->redirectToRoute('admin_instance_storage');
            }
        }

        return $this->render('platform/backend/v1/file_upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
