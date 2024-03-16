<?php
namespace App\Controller;

use App\Entity\Sponsor;
use App\Form\SponsorType;
use App\Repository\SponsorRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class PdfGeneratorController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/pdf/generate/{id?}", name="generate_pdf")
     */
    public function generatePdf($id = null): Response
    {
        if ($id === null) {
            // Handle case where no ID is provided
            // For example, display an error message or redirect
        }

        $sponsor = null;
        if ($id !== null) {
            $sponsor = $this->entityManager->getRepository(Sponsor::class)->find($id);

            if (!$sponsor) {
                throw $this->createNotFoundException('Sponsor not found');
            }
        }
        
        $image = $this->imageToBase64($this->getParameter('kernel.project_dir') .'/public/front/images/Logo_ESPRIT.jpg');
        $html = $this->renderView('pdf_generator/index.html.twig', [
            'sponsor' => $sponsor,
            'image' => $image

        ]);        

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="contrat_sponsor.pdf"'
            ]
        );
    }
    public function imageToBase64($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}
