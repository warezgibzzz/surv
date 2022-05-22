<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Survey;
use App\Entity\User;
use App\Form\SurveyType;
use App\Repository\AnswerRepository;
use App\Repository\ResultRepository;
use App\Repository\SurveyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class SurveyController extends AbstractController
{
    #[Route('/', name: 'app_survey_index', methods: ['GET'])]
    public function index(SurveyRepository $surveyRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            /** @var User $user */
            $user = $this->getUser();
            $surveys = $surveyRepository->findWhereNotAnsweredByUser($user);
        } else {
            $surveys = $surveyRepository->findAll();
        }

        return $this->render('survey/index.html.twig', [
            'surveys' => $surveys,
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/survey/new', name: 'app_survey_new', methods: ['GET', 'POST'])]
    public function new(
        Request                $request,
        SurveyRepository       $surveyRepository,
        AnswerRepository       $answerRepository,
        EntityManagerInterface $manager
    ): Response
    {
        $survey = new Survey();
        $form = $this->createForm(SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $surveyRepository->add($survey);
            foreach ($survey->getAnswers() as $answer) {
                $answer->setSurvey($survey);
                $answerRepository->add($answer);
            }

            $manager->flush();

            return $this->redirectToRoute('app_survey_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('survey/new.html.twig', [
            'survey' => $survey,
            'form' => $form,
        ]);
    }

    #[Route('/survey/{id}', name: 'app_survey_show', methods: ['GET'])]
    public function show(Survey $survey, ResultRepository $resultRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getResults()->exists(function () use ($resultRepository, $user, $survey) {
            return null !== $resultRepository->findOneBy([
                    'survey' => $survey,
                    'participant' => $user
                ]);
        })) {
            return $this->redirectToRoute('app_survey_index');
        }

        $answers = $survey->getAnswers()->toArray();

        usort($answers, function (Answer $a, Answer $b) {
            if ($a->getPosition() == $b->getPosition()) {
                return 0;
            }


            return ($a->getPosition() > $b->getPosition()) ? 1 : -1;

        });

        return $this->render('survey/show.html.twig', [
            'survey' => $survey,
            'answers' => $answers
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/survey/{id}/edit', name: 'app_survey_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        Survey                 $survey,
        SurveyRepository       $surveyRepository,
        AnswerRepository       $answerRepository,
        EntityManagerInterface $manager
    ): Response
    {
        $form = $this->createForm(SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $surveyRepository->add($survey);
            foreach ($survey->getAnswers() as $answer) {
                $answer->setSurvey($survey);
                $answerRepository->add($answer);
            }

            $manager->flush();

            return $this->redirectToRoute('app_survey_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('survey/edit.html.twig', [
            'survey' => $survey,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/survey/{id}', name: 'app_survey_delete', methods: ['POST'])]
    public function delete(Request $request, Survey $survey, SurveyRepository $surveyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $survey->getId(), $request->request->get('_token'))) {
            $surveyRepository->remove($survey, true);
        }

        return $this->redirectToRoute('app_survey_index', [], Response::HTTP_SEE_OTHER);
    }
}
