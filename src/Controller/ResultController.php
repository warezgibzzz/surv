<?php

namespace App\Controller;

use App\Entity\Result;
use App\Repository\ResultRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/result')]
#[IsGranted('ROLE_ADMIN')]
class ResultController extends AbstractController
{
    #[Route('/', name: 'app_result_index', methods: ['GET'])]
    public function index(ResultRepository $resultRepository): Response
    {
        return $this->render('result/index.html.twig', [
            'results' => $resultRepository->findBy([], ['id' => 'ASC']),
        ]);
    }
    #[Route('/{id}', name: 'app_result_view', methods: ['GET'])]
    public function view(Result $result): Response
    {
        return $this->render('result/view.html.twig', [
            'result' => $result
        ]);
    }
}
