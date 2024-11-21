<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Utilisateur;
use App\Entity\Like;
use App\Entity\Commentaire;
use App\Form\PublicationType;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicationController extends AbstractController
{
    #[Route('/client/publication', name: 'app_publication_index', methods: ['GET'])]
    public function index(PublicationRepository $publicationRepository): Response
    {
        return $this->render('service/community.html.twig', [
            'publications' => $publicationRepository->findAll(),
        ]);
    }

    #[Route('/client/publication/new', name: 'app_publication_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            /** @var UploadedFile $imageFile */
          //  $imageFile = $form->get('image')->getData();
          $tempFilePath = $form['image']->getData();
          if ($tempFilePath){
            $nameph = $publication->getDate().$publication->getTitre().md5(uniqid(rand(), true)).".png";
          $destinationPath = "uploads/".$nameph;
          $compressionQuality = 100;
  
          $this->compressImage($tempFilePath, $destinationPath, $compressionQuality);
  
            $publication->setImage($nameph);
        }
            $user_id = $request->getSession()->get('user_id');
            $utilisateur = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' =>$user_id]);  // remplacée
            $publication->setIdUser($utilisateur);
            $publication->setDate(new \DateTime());
            $entityManager->persist($publication);
            $entityManager->flush();
            toastr()->addSuccess('Your post has been created successfully !');

            return $this->redirectToRoute('app_publication_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('publication/new.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }

    #[Route('/client/publication/{id}', name: 'app_publication_show', methods: ['GET'])]
    public function show(Publication $publication, EntityManagerInterface $entityManager,Request $request): Response
    {
        $id_user = $request->getSession()->get('user_id');
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $id_user]);
        $existingLike = $entityManager->getRepository(Like::class)->findOneBy(['user' => $user, 'id_publication' => $publication]);
        return $this->render('publication/show.html.twig', [
            'publication' => $publication,
            'like' => $existingLike,
        ]);
    }

    #[Route('/client/publication/comments/{id}', name: 'app_publication_comments', methods: ['GET'])]
    public function showComments(Publication $publication, EntityManagerInterface $entityManager,Request $request): Response
    {
        $id_user = $request->getSession()->get('user_id');
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $id_user]);
        return $this->render('publication/comments.html.twig', [
            'publication' => $publication,
        ]);
    }

    #[Route('/admin/publication/{id}', name: 'app_publication_showA', methods: ['GET'])]
    public function showA(Publication $publication, EntityManagerInterface $entityManager,Request $request): Response
    {
        $id_user = $request->getSession()->get('user_id');
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $id_user]);
        $existingLike = $entityManager->getRepository(Like::class)->findOneBy(['user' => $user, 'id_publication' => $publication]);
        return $this->render('publication/showA.html.twig', [
            'publication' => $publication,
            'like' => $existingLike,
        ]);
    }

    #[Route('/client/publication/edit/{id}', name: 'app_publication_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tempFilePath = $form['image']->getData();
          if ($tempFilePath){
            if ($publication->getImage()) {
            unlink($publication->getImage());
            }
            $nameph = $publication->getDate().$publication->getTitre().md5(uniqid(rand(), true)).".png";
          $destinationPath = "uploads/".$nameph;
          $compressionQuality = 100;
  
          $this->compressImage($tempFilePath, $destinationPath, $compressionQuality);
  
            $publication->setImage($nameph);
        }
            $entityManager->flush();

            return $this->redirectToRoute('app_publication_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('publication/edit.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }

    #[Route('/client/publication/delete/{id}', name: 'app_publication_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
    {
            $likes = $publication->getLikes();
            $comments = $publication->getCommentaires();
            foreach ($likes as $like) {
                $publication->removeLike($like);
                $entityManager->remove($like);
            }
            foreach ($comments as $comment) {
                $publication->removeCommentaire($comment);
                $entityManager->remove($comment);
            }
            $entityManager->remove($publication);
            $entityManager->flush();
        return $this->redirectToRoute('app_publication_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/publication/delete/{id}', name: 'app_publication_deleteA', methods: ['GET','POST'])]
    public function deleteA(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
    {
            $likes = $publication->getLikes();
            $comments = $publication->getCommentaires();
            foreach ($likes as $like) {
                $publication->removeLike($like);
                $entityManager->remove($like);
            }
            foreach ($comments as $comment) {
                $publication->removeCommentaire($comment);
                $entityManager->remove($comment);
            }
            $entityManager->remove($publication);
            $entityManager->flush();
        return $this->redirectToRoute('app_community', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/client/publication/like/{id}', name: 'app_publication_like', methods: ['GET','POST'])]
    public function like(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
    {
        $id_user = $request->getSession()->get('user_id');
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $id_user]);
        $existingLike = $entityManager->getRepository(Like::class)->findOneBy(['user' => $user, 'id_publication' => $publication]);

    if (!$existingLike) {
        $like = new Like();
        $like->setUser($user);
        $like->setIdPublication($publication);
        $entityManager->persist($like);
        toastr()->addInfo('You liked this post !');
    } else {
        $entityManager->remove($existingLike);
    }

    $entityManager->flush();

        return $this->redirectToRoute('app_publication_show', ['id' => $publication->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/client/publication/comment/{id}', name: 'app_publication_comment', methods: ['GET','POST'])]
public function comment(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
{
    $id_user = $request->getSession()->get('user_id');
    $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $id_user]);

    $commentText = $request->request->get('commentText');

    if ($commentText) {
        $comment = new Commentaire();
        $comment->setDate(new \DateTime());
        $comment->setIdUser($user);
        $comment->setIdPublication($publication);
        $comment->setTexte($commentText);
        $entityManager->persist($comment);
        $entityManager->flush();
        toastr()->addInfo('You commented this post !');
    }
    return $this->redirectToRoute('app_publication_show', ['id' => $publication->getId()], Response::HTTP_SEE_OTHER);
}
   

    private  function compressImage($source, $destination, $quality) {
        $info = getimagesize($source);
        
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = \imagecreatefrompng($source);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        } else {
            return false;
        }    // Sauvegarder l'image compressée
        $filename = substr($destination, strpos($destination, '/') + 1);
        $destination2 = "C:/Users/hails/Pictures/Assurancy/Assurancy/src/main/resources/assets/uploads/".$filename;
        
        // Save the compressed image
        imagejpeg($image, $destination, $quality);
        imagejpeg($image, $destination2, $quality);
           
           // Libérer la mémoire
           imagedestroy($image);
           
           return true;
       }

}

