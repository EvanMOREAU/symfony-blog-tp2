<?php

namespace App\Controller;

use TCPDF;
use DateTimeImmutable;
use App\Entity\Formation;
use App\Form\FormationType;
use App\Services\ImageUploaderHelper;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/formation')]
class FormationController extends AbstractController
{

    #[Route('/catalog', name: 'app_formation_catalog', methods: ['GET'])]
    public function catalog(FormationRepository $formationRepository): Response
    {
        
        return $this->render('formation/catalog.html.twig', [
            'formations' => $formationRepository->findAllInTheFuture(),
        ]);
    }

    #[Route('/pdf/{id}', name: 'app_formation_pdf', methods: ['GET'])]
    public function pdf(Formation $formation): Response
    {

        $pdf = new \TCPDF();
        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 056', PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('helvetica', '', 18);

        // add a page
        $pdf->AddPage();

        $pdf->Write(0, 'Example of Registration Marks, Crop Marks and Color Bars', '', 0, 'L', true, 0, false, false, 0);

        $pdf->Ln(5);

        // color registration bars

        // A,W,R,G,B,C,M,Y,K,RGB,CMYK,ALL,ALLSPOT,<SPOT_COLOR_NAME>
        $pdf->colorRegistrationBar(50, 70, 40, 40, true, false, 'A,R,G,B,C,M,Y,K');
        $pdf->colorRegistrationBar(90, 70, 40, 40, true, true, 'A,R,G,B,C,M,Y,K');
        $pdf->colorRegistrationBar(50, 115, 80, 5, false, true, 'A,W,R,G,B,C,M,Y,K,ALL');
        $pdf->colorRegistrationBar(135, 70, 5, 50, false, false, 'A,W,R,G,B,C,M,Y,K,ALL');

        // corner crop marks

        $pdf->cropMark(50, 70, 10, 10, 'TL');
        $pdf->cropMark(140, 70, 10, 10, 'TR');
        $pdf->cropMark(50, 120, 10, 10, 'BL');
        $pdf->cropMark(140, 120, 10, 10, 'BR');

        // various crop marks

        $pdf->cropMark(95, 65, 5, 5, 'LEFT,TOP,RIGHT', array(255,0,0));
        $pdf->cropMark(95, 125, 5, 5, 'LEFT,BOTTOM,RIGHT', array(255,0,0));

        $pdf->cropMark(45, 95, 5, 5, 'TL,BL', array(0,255,0));
        $pdf->cropMark(145, 95, 5, 5, 'TR,BR', array(0,255,0));

        $pdf->cropMark(95, 140, 5, 5, 'A,D', array(0,0,255));

        // registration marks

        $pdf->registrationMark(40, 60, 5, false);
        $pdf->registrationMark(150, 60, 5, true, array(0,0,0), array(255,255,0));
        $pdf->registrationMark(40, 130, 5, true, array(0,0,0), array(255,255,0));
        $pdf->registrationMark(150, 130, 5, false, array(100,100,100,100,'All'), array(0,0,0,0,'None'));

        // test registration bar with spot colors

        $pdf->AddSpotColor('My TCPDF Dark Green', 100, 50, 80, 45);
        $pdf->AddSpotColor('My TCPDF Light Yellow', 0, 0, 55, 0);
        $pdf->AddSpotColor('My TCPDF Black', 0, 0, 0, 100);
        $pdf->AddSpotColor('My TCPDF Red', 30, 100, 90, 10);
        $pdf->AddSpotColor('My TCPDF Green', 100, 30, 100, 0);
        $pdf->AddSpotColor('My TCPDF Blue', 100, 60, 10, 5);
        $pdf->AddSpotColor('My TCPDF Yellow', 0, 20, 100, 0);

        $pdf->colorRegistrationBar(50, 150, 80, 10, false, true, 'ALLSPOT');

        // CMYK registration mark
        $pdf->registrationMarkCMYK(150, 155, 8);

        $pdf->Output('example_001.pdf', 'I');
        


        return $this->render('formation/catalog.html.twig', []);
    }


    #[Route('/', name: 'app_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('formation/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FormationRepository $formationRepository, ImageUploaderHelper $imageUploaderHelper): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $formation = new Formation();
        
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageUploaderHelper->uploadImage($form, $formation);
            $formationRepository->save($formation, true);

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        $formation = new Formation();
        $formation->setCreatedAt(new DateTimeImmutable());
        $formation->setCreatedBy($this->getUser());
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formationRepository->save($formation, true);

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
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
    public function edit(Request $request, Formation $formation, ImageUploaderHelper $imageUploaderHelper, FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageUploaderHelper->uploadImage($form, $formation);
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $formationRepository->remove($formation, true);
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }
}
