<?php

namespace App\Controller;
use Knp\Snappy\Pdf;
use App\Entity\Constat;
use App\Form\ValiderConstatType;
use App\Repository\ConstatRepository;
use App\Repository\UtilisateurRepository;
use App\Service\YousignService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
 #[Route('/admin/expert/constat')]
class ValiderConstatController extends AbstractController
{
    public function AffichageConstatsUser(Request $request, ConstatRepository $consrep , UtilisateurRepository $utilisateurRepository ): Response
    {
        $userid = $request->getSession()->get('user_id');
        $user= $utilisateurRepository->findByUserId($userid);
        $constats = $consrep->findByUserId($userid);
        return $this->render('expert/constat.html.twig', [
            'constats' => $constats,
            'user'=>$user,
        ]);
    }
    
#[Route('expert/show_details_constat/{id}', name: 'affichage_details_constat', methods: ['GET','Post'])]
    public function show(Request $request, Constat $cns, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ValiderConstatType::class, $cns);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cns->setStatus("validé");
            $entityManager->flush();

            return $this->redirectToRoute('constat', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('valider_constat/show.html.twig', [
            'cns' => $cns,
            'form' => $form->createView(), // corrected variable name here
            
             
        ]);
    }
    

    #[Route('/pdf/{id}', name: 'app_constat_pdf', methods: ['GET'])]
    public function pdf(Request $request, Constat $constat, ConstatRepository $constatRepository, Pdf $pdf)
    {
        // Nom du fichier PDF dynamique
        $pdfFileName = 'Constat_' . $constat->getId() . '.pdf';

        // Chemin vers le répertoire des fichiers PDF
        $pdfDirectory = $this->getParameter('kernel.project_dir') . '/public/pdf/';

        // Chemin absolu vers le fichier PDF
        $pdfPath = $pdfDirectory . $pdfFileName;

        // Vérifier si le fichier PDF existe déjà
        if (!file_exists($pdfPath)) {
            // Render the HTML content for the PDF
            $html = $this->renderView('valider_constat/pdf.html.twig', [
                'cns' => $constat,
            ]);

            // Generate the PDF
            $pdf->generateFromHtml($html, $pdfPath);
            $constat->setPdfSansSignature($pdfFileName);
            $constatRepository->save($constat, true);
        }

        // Redirect to the route that shows the PDF
        return $this->redirectToRoute('show_pdf', ['id' => $constat->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/pdf_show/{id}', name: 'show_pdf')]
    public function showPdf($id): Response
    {
        // Nom du fichier PDF dynamique
        $pdfFileName = 'Constat_' . $id . '.pdf';

        // Chemin vers le répertoire des fichiers PDF
        $pdfDirectory = $this->getParameter('kernel.project_dir') . '/public/pdf/';

        // Chemin absolu vers le fichier PDF
        $pdfPath = $pdfDirectory . $pdfFileName;

        // Vérifier si le fichier PDF existe
        if (!file_exists($pdfPath)) {
            throw $this->createNotFoundException('Le fichier PDF demandé n\'existe pas.');
        }

        // Créer une réponse pour le fichier PDF
        $response = new BinaryFileResponse($pdfPath);

        // Définir les en-têtes pour forcer le téléchargement ou l'affichage dans le navigateur
        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $pdfFileName);

        return $response;
    }
    #[Route('/{id}/signature', name: 'app_constat_signature', methods:['GET'])]
    
    public function signature(Request $request, Constat $constat, ConstatRepository $constatRepository, YousignService $yousignService,UtilisateurRepository $utilisateurRepository): Response
    { $userid = $userid = $request->getSession()->get('user_id');
        $user= $utilisateurRepository->find($userid);
        //1 création de la demande de signature
        $yousignSignatureRequest = $yousignService->signatureRequest();
        $constat->setSignatureId($yousignSignatureRequest['id']);
        $constatRepository->save($constat, true);

        //2 upload du document
        $uploadDocument = $yousignService->addDocumentToSignatureRequest($constat->getSignatureId(), $constat->getPdfSansSignature() );
        $constat->setDocumentId($uploadDocument['id']);
        $constatRepository->save($constat, true);

        //3 ajout des signataires
        $signerId = $yousignService->addSignerToSignatureRequest(
            $constat->getSignatureId(),
            $constat->getDocumentId(),
            $user->getEmail(),
            $user->getPrenom(),
            $user->getNom()
        );

        $constat->setSignerId($signerId['id']);
        $constatRepository->save($constat,true);
        

        //4 Envoi de la demande de signature
        $yousignService->activateSignatureRequest($constat->getSignatureId());
        
        return $this->redirectToRoute('affichage_details_constat', ['id' => $constat->getId()], Response::HTTP_SEE_OTHER);
    }
    
    


   
 
}