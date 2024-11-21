<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\DevisItem;
use App\Form\DevisItemType;
use App\Repository\DevisItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/devis/item')]
class DevisItemController extends AbstractController
{
    #[Route('/', name: 'app_devis_item_index', methods: ['GET'])]
    public function index(DevisItemRepository $devisItemRepository): Response
    {
        return $this->render('devis_item/index.html.twig', [
            'devis_items' => $devisItemRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_devis_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request,Devis $devis, EntityManagerInterface $entityManager): Response
    {
        $devisItem = new DevisItem();
        $form = $this->createForm(DevisItemType::class, $devisItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prix_u = $devisItem->getPrixU();
            $quantity = $devisItem->getQuantite();
            $devisItem->setTotal($prix_u * $quantity);
            $devisItem->setIdDevis($devis);
            $devis->setTotalTTC(0);
            if (!$devis->getDevisItems()->isEmpty()) {
            foreach ($devis->getDevisItems() as $dev) {
                $totalTTC = $devis->getTotalTTC();
                $devis->setTotalTTC($totalTTC + $dev->getTotal());
            }
            }
            $totalTTC = $devis->getTotalTTC();
            $devis->setTotalTTC($totalTTC + $devisItem->getTotal());
            $entityManager->persist($devisItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_devisItems_list', ['id' => $devis->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('devis_item/new.html.twig', [
            'devis_item' => $devisItem,
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_devis_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DevisItem $devisItem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DevisItemType::class, $devisItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $devis = $devisItem->getIdDevis();
            $prix_u = $devisItem->getPrixU();
            $quantity = $devisItem->getQuantite();
            $devisItem->setTotal($prix_u * $quantity);
            $devis->setTotalTTC(0);
            foreach ($devis->getDevisItems() as $dev) {
                $totalTTC = $devis->getTotalTTC();
                $devis->setTotalTTC($totalTTC + $dev->getTotal());
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_devisItems_list', ['id' => $devisItem->getIdDevis()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('devis_item/edit.html.twig', [
            'devis_item' => $devisItem,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_devis_item_delete', methods: ['GET','POST'])]
    public function delete(Request $request, DevisItem $devisItem, EntityManagerInterface $entityManager): Response
    {
            $devis = $devisItem->getIdDevis();
            $total = $devisItem->getTotal();
            $totalTTC = $devis->getTotalTTC();
            $devis->setTotalTTC($totalTTC - $total);
            $entityManager->remove($devisItem);
            $entityManager->flush();

        return $this->redirectToRoute('app_devisItems_list', ['id' => $devisItem->getIdDevis()->getId()], Response::HTTP_SEE_OTHER);
    }
}
