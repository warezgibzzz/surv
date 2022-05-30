<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{

    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'integer')]
        private ?int        $id = null,

        #[ORM\Column(type: 'text')]
        private ?string     $name = null,

        #[ORM\ManyToOne(targetEntity: Survey::class, inversedBy: 'questions')]
        #[ORM\JoinColumn(nullable: false)]
        private ?Survey     $survey = null,

        #[ORM\Column(type: 'integer')]
        private ?int        $position = null,

        #[ORM\OneToMany(mappedBy: 'question', targetEntity: QuestionAnswer::class, cascade: ['persist'], orphanRemoval: true)]
        private ?Collection $answers = new ArrayCollection()
    )

    {
        $this->answers = new ArrayCollection();
    }

    /**
     * @return int
     */
    public
    function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public
    function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public
    function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Survey
     */
    public
    function getSurvey(): Survey
    {
        return $this->survey;
    }

    /**
     * @param Survey $survey
     */
    public
    function setSurvey(Survey $survey): void
    {
        $this->survey = $survey;
    }

    /**
     * @return int
     */
    public
    function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public
    function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return Collection<int, QuestionAnswer>
     */
    public
    function getAnswers(): Collection
    {
        return $this->answers;
    }

    public
    function addAnswer(QuestionAnswer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setQuestion($this);
        }

        return $this;
    }

    public
    function removeAnswer(QuestionAnswer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }
}
