<?php

namespace App\Controller;

use App\Entity\Result;
use App\Repository\QuestionAnswerRepository;
use App\Repository\ResultRepository;
use App\Repository\SurveyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/result')]
class ResultController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', name: 'app_result_index', methods: ['GET'])]
    public function index(ResultRepository $resultRepository): Response
    {
        return $this->render('result/index.html.twig', [
            'results' => $resultRepository->findBy([], ['id' => 'ASC']),
        ]);
    }
    #[Route('/{id}', name: 'app_result_view', methods: ['GET'])]
    public function view(Result $result, SurveyRepository $surveyRepository, QuestionAnswerRepository $questionAnswerRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $data = [];
            $answers = $questionAnswerRepository->findBy(['id' => array_map(function (array $answer) {
                return $answer['id'];
            }, $result->getData()
            )]);

            foreach ($answers as $answer) {
                $data[] = [
                    'position' => $answer->getQuestion()->getPosition(),
                    'question' => $answer->getQuestion()->getName(),
                    'answer' => $answer->getText(),
                    'valid' => $answer->isValid()
                ];
            }

            uksort($data, function ($a, $b) {
                if ($a['position'] == $b['position']) {
                    return 0;
                }

                return $a['position'] > $b['position'] ? 1 : -1;
            });

            return $this->render('result/view.html.twig', [
                'result' => $result,
                'data' => $data
            ]);
        } else {

            $answers = $questionAnswerRepository->findBy(['id' => array_map(function (array $answer) {
                return $answer['id'];
            }, $result->getData()
            )]);

            $totalQuestions = $result->getSurvey()->getQuestions()->count();
            $validAnswers = 0;
            foreach ($answers as $answer) {
                if ($answer->isValid()) {
                    $validAnswers++;
                }
            }

            return $this->render('result/participant.html.twig', [
                'result' => $result,
                'totalQuestions' => $totalQuestions,
                'validAnswers' => $validAnswers
            ]);
        }
    }
}
