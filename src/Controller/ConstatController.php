<?php

namespace App\Controller;

use App\Entity\Constat;
use App\Form\ConstatUsrType;
use App\Repository\ConstatRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Knp\Snappy\Pdf;


#[Route('/client')]
class ConstatController extends AbstractController
{

    


    #[Route('/constatadd', name: 'add_constat')]
    public function addConstat(ManagerRegistry $man, Request $request, UtilisateurRepository $utirep): Response
    {
        $em = $man->getManager();
        $constat = new Constat();

        $form = $this->createForm(ConstatUsrType::class, $constat);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photoVehaFile = $constat->getPhotoVehaFile();
            $photoVehbFile = $constat->getPhotoVehbFile();
            $photoAccidentFile = $constat->getPhotoAccidentFile();

            if ($photoVehaFile) {
                $newFilename = $this->uploadFile($photoVehaFile);

            $constat->setPhotoVeha($newFilename);
        }

            if ($photoVehbFile) {
            $newFilename2 = $this->uploadFile($photoVehbFile);

        $constat->setPhotoVehb($newFilename2);
    }

            if ($photoAccidentFile) {
            $newFilename3 = $this->uploadFile($photoAccidentFile);
            $constat->setPhotoAccident($newFilename3);
}

            $user_id = $request->getSession()->get('user_id');
            $utilisateur = $utirep->find($user_id);  // remplacée
            $constat->setIdUser($utilisateur); // remplacée
           $constat->setDateConstat(new \DateTime());
            $constat->setStatus("En cours");
            $em->persist($constat);
            $em->flush();

            return $this->redirectToRoute('affichage_constats');
        }

        return $this->render('constat/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    private function uploadFile(UploadedFile $file)
{
    $uploadsDirectory = $this->getParameter('uploads_directory');

    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $newFilename = $originalFilename . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

    $file->move($uploadsDirectory, $newFilename);

    return $newFilename;
}




#[Route('/affichage_constats', name: 'affichage_constats')]
public function AffichageConstatsUser(ConstatRepository $consrep,Request $request): Response
{
    $userid = $request->getSession()->get('user_id'); // remplacée
    $constats = $consrep->findByUserId($userid);
    return $this->render('constat/affichage.html.twig', [
        'constats' => $constats,
    ]);
}

#[Route('/delete_constatuser/{id}', name: 'constatuser_delete')]
    public function deleteConstatUser($id, ManagerRegistry $manager, ConstatRepository $consrep): Response
    {
        $em = $manager->getManager();
        $constatd = $consrep->find($id);
        $em->remove($constatd);
        $em->flush();
        return $this->redirectToRoute('affichage_constats');
    }





    #[Route('/edit_constatuser/{id}', name: 'constatuser_edit')]
    public function editConstatUser(Request $request, ManagerRegistry $manager, $id, ConstatRepository $consrep): Response
    {
        $em = $manager->getManager();
        ///$constat = new Constat();
        $consusr  = $consrep->find($id);
        $form = $this->createForm(ConstatUsrType::class, $consusr);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $photoVehaFile = $consusr->getPhotoVehaFile();
            $photoVehbFile = $consusr->getPhotoVehbFile();
            $photoAccidentFile = $consusr->getPhotoAccidentFile();

            if ($photoVehaFile) {
                $newFilename = $this->uploadFile($photoVehaFile);

            $consusr->setPhotoVeha($newFilename);
        }

            if ($photoVehbFile) {
            $newFilename2 = $this->uploadFile($photoVehbFile);

        $consusr->setPhotoVehb($newFilename2);
    }

            if ($photoAccidentFile) {
            $newFilename3 = $this->uploadFile($photoAccidentFile);
            $consusr->setPhotoAccident($newFilename3);
}
            $em->persist($consusr);
            $em->flush();
            return $this->redirectToRoute('affichage_constats');
        }
        return $this->renderForm('constat/edit.html.twig', [
            'consusr' => $consusr,
            'form' => $form,
        ]);
    }


    #[Route('/generatepdf_constatuser/{id}', name: 'generatepdfuserconstat')]
    public function generatePdf($id, Pdf $pdf,ConstatRepository $consrep)
    {
        $consusr  = $consrep->find($id);
        $html = $this->renderView('pdf/constat_pdfusr.html.twig', [
            'cns' => $consusr,
            'dt' => new \DateTime(),
        ]);

        $filename = 'constat_' . $id . '.pdf';
        // 'Content-Disposition' => 'inline; filename="' . $filename . '"',

        $response = new Response(
            $pdf->getOutputFromHtml($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
        

        return $response;
    }





    #[Route('/tri-blesses/{type}', name: 'blesse')]
    function blessesTri($type, EntityManagerInterface $em,Request $request)
    {
        switch ($type) {
            case 'blesseO':
                $query = $em->createQuery('
                SELECT b FROM App\Entity\Constat b 
                JOIN App\Entity\Utilisateur u WITH b.id_user = u
                WHERE b.blesses = true AND u.id = :x
                ');
                break;
            case 'blesseN':
                $query = $em->createQuery('
                SELECT b FROM App\Entity\Constat b 
                JOIN App\Entity\Utilisateur u WITH b.id_user = u
                WHERE b.blesses = false AND u.id = :x
                ');
                break;
        }

        $userId = $request->getSession()->get('user_id');
        $query->setParameter('x', $userId);
        $cons = $query->getResult();
        return $this->render('constat/affichage.html.twig', [
            'constats' => $cons,
        ]);

    }



    #[Route('/datedep/{type}', name: 'datedep')]
    function datedepTri($type, EntityManagerInterface $em,Request $request)
    {
        switch ($type) {
            case 'ancienne':
                $query = $em->createQuery('
                SELECT a from App\Entity\Constat a 
                JOIN App\Entity\Utilisateur u WITH a.id_user = u
                WHERE u.id = :x
                ORDER BY a.dateConstat ASC 
                ');
                break;
            case 'recente':
                $query = $em->createQuery('
                SELECT a from App\Entity\Constat a 
                JOIN App\Entity\Utilisateur u WITH a.id_user = u
                WHERE u.id = :x
                ORDER BY a.dateConstat DESC
                ');
                break;
        }
        $userId = $request->getSession()->get('user_id');
        $query->setParameter('x', $userId);
        $cons = $query->getResult();
        return $this->render('constat/affichage.html.twig', [
            'constats' => $cons,
        ]);

    }



    #[Route('/statut/{type}', name: 'statut')]
    function statutTri($type, EntityManagerInterface $em,Request $request)
    {
        switch ($type) {
            case 'valide':
                $query = $em->createQuery("
                SELECT b FROM App\Entity\Constat b 
                JOIN App\Entity\Utilisateur u WITH b.id_user = u
                WHERE b.status = 'validé' AND u.id = :x
                ");
                break;
            case 'encours':
                $query = $em->createQuery("
                SELECT b FROM App\Entity\Constat b 
                JOIN App\Entity\Utilisateur u WITH b.id_user = u
                WHERE b.status = 'En cours' AND u.id = :x
                ");
                break;
        }
        $userId = $request->getSession()->get('user_id');
        $query->setParameter('x', $userId);
        $cons = $query->getResult();
        return $this->render('constat/affichage.html.twig', [
            'constats' => $cons,
        ]);

    }

}
