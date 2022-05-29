<?php

namespace App\Component;

use App\Entity\Survey;
use App\Form\SurveyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('survey_form')]
class SurveyForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'data')]
    public ?Survey $survey = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SurveyType::class, $this->survey);
    }

    #[LiveAction]
    public function addQuestion()
    {
        $this->formValues['questions'][] = [];
    }

    #[LiveAction]
    public function removeQuestion(#[LiveArg] int $index)
    {
        unset($this->formValues['questions'][$index]);
    }

    #[LiveAction]
    public function addAnswer(#[LiveArg] int $index)
    {
        $this->formValues['questions'][$index]['answers'][] = [];
    }

    #[LiveAction]
    public function removeAnswer(#[LiveArg] int $index, #[LiveArg] int $questionIndex)
    {
        unset($this->formValues['questions'][$questionIndex]['answers'][$index]);
    }
}