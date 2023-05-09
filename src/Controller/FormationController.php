<?php

namespace App\Controller;

use TCPDF;
use DateTimeImmutable;
use App\Entity\Formation;
use App\Form\Formation1Type;
use App\Services\ImageUploaderHelper;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/formation')]
class FormationController extends AbstractController
{
    #[Route('/pdf/{id}', name: 'app_formation_pdf', methods: ['GET'])]
    public function pdf(Formation $formation): Response
    {
        $pdf = new TCPDF();

        $left_column = '<img src="images/fcpro.jpg">' . 'Tarif : ' . $formation->getPrice() . ' €' . '<br><br>' . 'Places : ' . $formation->getCapacity();
        $right_column = '<b><u>Description de la formation : </u></b><br><br>' . $formation->getDescription() . '<br><br><br>' . '<b><u>Contenu de la formation : </u></b>' . $formation->getContent();
        $y = $pdf->getY();
        $middle = $pdf->getPageWidth() / 2;
        $name = '<b><i>' . $formation->getName() . '</i></b>';

        $pdf->SetAuthor('SIO1-Team');

        $pdf->SetTitle($formation->getName());
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->setCellMargins(1, 1, 1, 1);

        $pdf->AddPage();

        $pdf->setXY(10, 1);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->writeHTMLCell(125, '', '', $y, 'Date de création : 01/01/2023', 0, 0, 1, true, 'J', true);

        $pdf->setXY($middle, 1);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->writeHTMLCell(125, '', '', $y, 'Date de mise à jour : 01/01/2023', 0, 0, 1, true, 'J', true);

        $pdf->setXY($middle-30, 15);
        $pdf->SetFont('helvetica', '', 18);
        $pdf->SetTextColor(1, 14, 51);
        $pdf->SetFillColor(212, 225, 237);
        $pdf->writeHTMLCell(125, '', '', $y, $name, 0, 0, 1, true, 'J', true);

        $pdf->SetFillColor(234, 232, 232);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->setXY(10, 15);
        $pdf->writeHTMLCell(60, '', '', $y, $left_column, 0, 0, 1, true, 'J', true);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->setXY($middle-30, 30);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->writeHTMLCell(125, '', '', $y, $right_column, 0, 0, 1, true, 'J', true);


        return $pdf->Output('fcpro-formation-' . $formation->getId() . '.pdf', 'I');
    }

    #[Route('/{id}/duplicate', name: 'app_formation_duplicate', methods: ['GET', 'POST'])]
    public function duplicate(Request $request, FormationRepository $formationRepository, TranslatorInterface $translator, Formation $formation): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $formation2 = new Formation();
        $formation2->setCreatedAt($formation->getCreatedAt());
        $formation2->setCreatedBy($formation->getCreatedBy());
        $formation2->setContent($formation->getContent());
        $formation2->setDescription($formation->getDescription());

        $formation2->setCapacity($formation->getCapacity());
        $formation2->setStartDateTime($formation->getStartDateTime());
        $formation2->setEndDateTime($formation->getEndDateTime());
        $formation2->setImageFileName($formation->getImageFileName());
        $formation2->setName($formation->getName());
        $formation2->setPrice($formation->getPrice());

        $formationRepository->save($formation2, true);
        $this->addFlash('success', $translator->trans('The formation is copied'));

        return $this->redirectToRoute('app_formation_index');
    }

    /* #[Route('/futur', name: 'app_formation_futur', methods: ['GET'])]
    public function futur(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/futur.html.twig', [
            'formations' => $formationRepository->findAllInTheFuture(),
        ]);
    }
    */

    #[Route('/futur', name: 'app_formation_futur', methods: ['GET'])]
    public function futur(FormationRepository $formationRepository): Response
    {
        $formationsPerThree = array();

        $formations = $formationRepository->findAllInTheFuture();

        $i=1; $j=0;
        foreach ($formations as $formation) {
            $i++;
            if ($i>3) {
                $j++; $i=1;
            }
            $formationsPerThree[$j][$i] = $formation;
        }
        dump($formations);
        dump($formationsPerThree);
        
        return $this->render('formation/futur.html.twig', ['formationsPerThree' => $formationsPerThree,]);
    }

    #[Route('/catalog', name: 'app_formation_catalog', methods: ['GET'])]
    public function catalog(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/catalog.html.twig', [
            'formations' => $formationRepository->findAllInTheFuture(),
        ]);
    }

    #[Route('/', name: 'app_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('formation/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ImageUploaderHelper $imageUploaderHelper, FormationRepository $formationRepository, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $formation = new Formation();
        $formation->setCreatedAt(new DateTimeImmutable());
        $formation->setCreatedBy($this->getUser());

        $form = $this->createForm(Formation1Type::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $errorMessage = $imageUploaderHelper->uploadImage($form, $formation);
            if (!empty($errorMessage)) {
                $this->addFlash ('danger', $translator->trans('An error has occured: ') . $errorMessage);
            }
            $formationRepository->save($formation, true);

            //return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_show', methods: ['GET'])]
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ImageUploaderHelper $imageUploaderHelper, FormationRepository $formationRepository, Formation $formation, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(Formation1Type::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $errorMessage = $imageUploaderHelper->uploadImage($form, $formation);
            if (!empty($errorMessage)) {
                $this->addFlash ('danger', $translator->trans('An error has occured: ') . $errorMessage);
            }
            $formationRepository->save($formation, true);

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) 
        {
            $formationRepository->remove($formation, true);
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }
}
