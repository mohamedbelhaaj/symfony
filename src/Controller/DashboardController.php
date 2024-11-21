<?php

namespace App\Controller;

use App\Entity\Assurance;
use App\Entity\Constat;
use App\Entity\Demande;
use App\Entity\Evaluation;
use App\Entity\Immobilier;
use App\Entity\Publication;
use App\Entity\Rating;
use App\Entity\Reclamation;
use App\Entity\Utilisateur;
use App\Entity\Vehicule;
use App\Form\ResetPasswordDType;
use App\Form\UtilisateurLType;
use App\Form\UtilisateurPType;
use App\Repository\AssuranceRepository;
use App\Repository\ConstatRepository;
use App\Repository\DemandeRepository;
use App\Repository\ImmobilierRepository;
use App\Repository\MechanoRepository;
use App\Repository\RDVRepository;
use App\Repository\ReclamationRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use GeoIp2\Database\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Label\Font\NotoSans;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    private function getGeoLocationData($ipAddress)
    {
        try {
            $databasePath = realpath($this->getParameter('kernel.project_dir') . '/geolocalisation/GeoLite2-City.mmdb');
            dump($databasePath);
            $reader = new Reader($databasePath);
            $record = $reader->city($ipAddress);
            $country = $record->country->name;
            $city = $record->city->name;
            dump($country);
            dump($city);
            return ['country' => $country, 'city' => $city];
        } catch (\Exception $e) {
            return ['country' => "Tunisia", 'city' => "Ariana"];
        }
    }

    private function getSiteData()
    {
        $NBClients = count($this->entityManager->getRepository(Utilisateur::class)->findBy(['role' => 'Client']));
        $NBSOS = count($this->entityManager->getRepository(Utilisateur::class)->findBy(['role' => 'Agent SOS'])); 
        $NBExperts = count($this->entityManager->getRepository(Utilisateur::class)->findBy(['role' => 'Expert']));  
        $NBEnsurances = count($this->entityManager->getRepository(Assurance::class)->findAll());
        $NBEnsurancesVehicule = count($this->entityManager->getRepository(Assurance::class)->findBy(['type' => 'Assurance Véhicule']));
        $NBEnsurancesEstate = count($this->entityManager->getRepository(Assurance::class)->findBy(['type' => 'Assurance Immobilière']));
        $NBEnsurancesLife = count($this->entityManager->getRepository(Assurance::class)->findBy(['type' => 'Assurance Vie']));
        $NBRatings1 = count($this->entityManager->getRepository(Rating::class)->findBy(['note' => 1]));
        $NBRatings2 = count($this->entityManager->getRepository(Rating::class)->findBy(['note' => 2]));
        $NBRatings3 = count($this->entityManager->getRepository(Rating::class)->findBy(['note' => 3]));
        $NBRatings4 = count($this->entityManager->getRepository(Rating::class)->findBy(['note' => 4]));
        $NBRatings5 = count($this->entityManager->getRepository(Rating::class)->findBy(['note' => 5]));
        $NBRatingsNO = count($this->entityManager->getRepository(Demande::class)->findBy(['rating' => null]));
        $NBReclamationsTraitee = count($this->entityManager->getRepository(Reclamation::class)->findBy(['status' => 'Traitée']));
        $NBReclamationsNonT = count($this->entityManager->getRepository(Reclamation::class)->findBy(['status' => 'En Attente']));
        $NBConsVal = count($this->entityManager->getRepository(Constat::class)->findBy(['status' => 'validé']));
        $NBConsEnCours = count($this->entityManager->getRepository(Constat::class)->findBy(['status' => 'En cours']));
        $NBVEvalued = count($this->entityManager->getRepository(Vehicule::class)->createQueryBuilder('v')
            ->where('v.evaluation IS NOT NULL')
            ->getQuery()
            ->getResult());
        $NBREEvalued = count($this->entityManager->getRepository(Immobilier::class)->createQueryBuilder('re')
            ->where('re.evaluation IS NOT NULL')
            ->getQuery()
            ->getResult());
        $LabelsR = ['Rating grade 1','Rating grade 2','Rating grade 3','Rating grade 4','Rating grade 5','Rating grade None'];
        $DataR = [$NBRatings1,$NBRatings2,$NBRatings3,$NBRatings4,$NBRatings5,$NBRatingsNO];
        $LabelsE = ['Vehicule ensurance', 'Real estate ensurance', 'Life ensurance'];
        $DataE = [$NBEnsurancesVehicule, $NBEnsurancesEstate, $NBEnsurancesLife];
        $LabelsRc = ['Complaint treated', 'Complaint not treated'];
        $DataRc = [$NBReclamationsTraitee,$NBReclamationsNonT];
        $LabelsC = ['Accident report validated', 'Accident report not validated'];
        $DataC = [$NBConsVal,$NBConsEnCours];
        $BestPost = $this->entityManager->getRepository(Publication::class)->getBestPost();
        return [$NBClients,$NBSOS,$NBExperts,$NBEnsurances,$LabelsE,$DataE,$LabelsR,$DataR,$LabelsRc,$DataRc,$BestPost,$LabelsC,$DataC,$NBVEvalued,$NBREEvalued];
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(Request $request): Response
    {
        $ipAddress = $request->getClientIp();
        $geoLocationData = $this->getGeoLocationData($ipAddress);
        $data = $this->getSiteData();
        toastr()->addInfo('Switched to admin dashboard !');
        return $this->render('dashboard/index.html.twig', [
            'NBClients' => $data[0],
            'NBSOS' => $data[1],
            'NBExperts' => $data[2],
            'NBEnsurances' => $data[3],
            'LabelsE' => $data[4],
            'DataE' => $data[5],
            'LabelsR' => $data[6],
            'DataR' => $data[7],
            'geoLocationData' => $geoLocationData,
        ]);
    }

    #[Route('/Communauté', name: 'app_community')]
    public function Community(EntityManagerInterface $entityManager): Response
    {
        $data = $this->getSiteData();
        $publicationRepository = $entityManager->getRepository(Publication::class);
        $publications = $publicationRepository->findAll();
        return $this->render('dashboard/community.html.twig', [
            'publications' => $publications,
            'BestPost' => $data[10],
        ]);
    }

    #[Route('/Assurances', name: 'app_assurances')]
    public function Assurances(AssuranceRepository $assrep): Response
    {


        $writer = new PngWriter();
        $qrCode = QrCode::create('https://www.youtube.com/watch?v=dbylo4QvTwc')
            ->setEncoding(new Encoding('UTF-8'))
            // ->setSize(50)
            ->setMargin(0)
            ->setBackgroundColor(new Color(255, 255, 255,127));
        $label = Label::create('')->setFont(new NotoSans(12));
 
        $qrCodes = [];
 
        $qrCode->setForegroundColor(new Color(0, 200, 0));
        $qrCodes['changeColor'] = $writer->write(
            $qrCode,
            null,
            $label->setText("Guide d'utilisation Dashboard")
        )->getDataUri();


        return $this->render('dashboard/assurances.html.twig', [
            'assur' => $assrep->findAll(),
            'currentDate' => new \DateTime(),
            'qrCodes' => $qrCodes['changeColor'],
        ]);
    }

    #[Route('/Assistance', name: 'app_assistance', methods: ['GET'])]
    public function searchRECLAM(Request $request, ReclamationRepository $repo, RDVRepository $rDVRepository): Response
    {
        $query = $request->query->get('q');
        $data = $this->getSiteData();
        $sortBy = $request->query->get('sort_by', 'date_asc');
        $findBy= $request->query->get('find_by');
        switch ($findBy) {
                // Intégration du tri par statut
            case 'Traitée':
                $results = $repo->findByStatus('Traitée');
                break;
            case 'En Attente':
                $results = $repo->findByStatus('En Attente');
                break;
            default:
            $results = $repo->findAll();
        }
    
        
        switch ($sortBy) {
            case 'date_asc':
                $rDVs = $rDVRepository->findBy([], ['date' => 'ASC']);
                break;
            case 'date_desc':
                $rDVs = $rDVRepository->findBy([], ['date' => 'DESC']);
                break;
            default:
                $rDVs = $rDVRepository->findAll();
        }
    
        // Check if the query parameter is set and if it's a valid integer
        if ($query !== null && ctype_digit($query)) {
            // Cast the query parameter to an integer and search by ID
            $results = $repo->findById((int)$query);
        }
    
        // Search reclamations by subject
        $results2 = $query !== null ? $repo->searchBySujet($query) : [];
        $results3 = $query !== null ? $repo->searchByTEl($query) : [];
        
    
        
        return $this->render('dashboard/_search_results.html.twig', [
            'results' => $results,
            'results2' => $results2,
            'results3' => $results3,
            'r_d_vs' => $rDVs,
            'LabelsRc' => $data[8],
            'DataRc' => $data[9],
        ]);
    }

    #[Route('/SOS', name: 'app_sos')]
    public function SOS(MechanoRepository $mechanoRepository, DemandeRepository $demandeRepository): Response
    {
        $data = $this->getSiteData();
        return $this->render('dashboard/sos.html.twig', [
            'demandes' => $demandeRepository->findAll(),
            'mechanos' => $mechanoRepository->findAll(),
            'LabelsR' => $data[6],
            'DataR' => $data[7],
        ]);
    }

    #[Route('/Constats', name: 'constat')]
    public function constat(ConstatRepository $consrep , UtilisateurRepository $utilisateurRepository, Request $request ): Response
    {
        $userid = $request->getSession()->get('user_id'); // remplacée
        $user= $utilisateurRepository->find($userid);
        $data = $this->getSiteData();
        $constats = $consrep->findByUserId($userid);
        return $this->render('expert/constat.html.twig', [
            'constats' => $constats,
            'user'=>$user,
            'DataC' => $data[12],
            'LabelsC' => $data[11],
        ]);
    }
    
    #[Route('/Devis', name: 'devis_expert')]
    public function devis(VehiculeRepository $vehiculeRepository): Response
    {
        return $this->render('expert/devis.html.twig', [
            'vehicules' => $vehiculeRepository->findAll(),
        ]);
    }

    #[Route('/Evaluations', name: 'app_evaluation')]
    public function evaluation(VehiculeRepository $vehiculeRepository,ImmobilierRepository $immobilierRepository, EntityManagerInterface $entityManager): Response
    {
        $evaluations = $entityManager->getRepository(Evaluation::class)->findAll();
        $data = $this->getSiteData();
        return $this->render('expert/evaluation.html.twig', [
            'vehicules' => $vehiculeRepository->findAll(),
            'immobiliers' => $immobilierRepository->findAll(),
            'evaluations' => $evaluations,
            'DataVARE2' => $data[14],
            'DataVARE1' => $data[13],
        ]);
    }

    #[Route('/profile/{id}', name: 'profile_d', methods: ['GET', 'POST'])]
    public function profile(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, SessionInterface $session, UserPasswordEncoderInterface $passwordEncoder): Response
{
    if ($session->has('user_id')) {
    $userId = $request->getSession()->get('user_id');

    if ($userId !== $utilisateur->getId()) {
        return $this->redirectToRoute('app_dashboard');
    }
    $formRs = $this->createForm(ResetPasswordDType::class);
    $formRs->handleRequest($request);
    $form = $this->createForm(UtilisateurPType::class, $utilisateur);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $tempFilePath = $form['image']->getData();
          if ($tempFilePath){
            if ($utilisateur->getImage()) {
                unlink($utilisateur->getImage());
            }
            
            $nameph = "User". md5(uniqid(rand(), true) .$utilisateur->getId().".png");
          $destinationPath = "uploads/".$nameph;
          $compressionQuality = 100;
  
          $this->compressImage($tempFilePath, $destinationPath, $compressionQuality);
  
            $utilisateur->setImage($nameph);
        }
        $entityManager->flush();
        $request->getSession()->set('user_image', $utilisateur->getImage());
        $this->addFlash('success', "Modification enregistré avec succés");
    }
    if ($formRs->isSubmitted() && $formRs->isValid()) {
        $currentPassword = $formRs->get('cup')->getData();
        $newPassword = $formRs->get('np')->getData();
        if (!$passwordEncoder->isPasswordValid($utilisateur, $currentPassword)) {
            $this->addFlash('error', 'Incorrect current password.');
            return $this->redirectToRoute('profile_d',['id' => $utilisateur->getId()]);
        }
        $hashedPassword = $passwordEncoder->encodePassword($utilisateur, $newPassword);
        $utilisateur->setMdp($hashedPassword);
        $entityManager->flush();
        $this->addFlash('success', "Password has been updated successfully!");
    }
    return $this->renderForm('utilisateur/profile.html.twig', [
        'utilisateur' => $utilisateur,
        'form' => $form,
        'formRs' => $formRs,
    ]);
    }
    else {
        return $this->redirectToRoute('login_d');
    }
}

private function compressImage($source, $destination, $quality) {
    // Check if the image already exists at the destination path
    if (file_exists($destination)) {
        unlink($destination);
    }

    $info = getimagesize($source);

    // Determine the MIME type of the image and create an image resource
    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
    } elseif ($info['mime'] == 'image/gif') {
        $image = imagecreatefromgif($source);
    } else {
        return false; // Unsupported image type
    }
    $filename = substr($destination, strpos($destination, '/') + 1);
        $destination2 = "C:/Users/hails/Pictures/Assurancy/Assurancy/src/main/resources/assets/uploads/".$filename;
    // Save the compressed image
    imagejpeg($image, $destination, $quality);
    imagejpeg($image, $destination2, $quality);

    // Free up memory by destroying the image resource
    imagedestroy($image);

    return true; // Compression successful
}
}
