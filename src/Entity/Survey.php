<?php

namespace App\Entity;

use App\Repository\SurveyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SurveyRepository::class)]
class Survey
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'integer')]
        private ?int                 $id = null,

        #[ORM\Column(type: 'string', length: 255)]
        private ?string              $name = null,

        #[ORM\OneToMany(mappedBy: 'survey', targetEntity: Answer::class, orphanRemoval: true)]
        private readonly ?Collection $answers = new ArrayCollection(),

        #[ORM\OneToMany(mappedBy: 'survey', targetEntity: Result::class, orphanRemoval: true)]
        private readonly ?Collection $results = new ArrayCollection(),
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setSurvey($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getSurvey() === $this) {
                $answer->setSurvey(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Result>
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    public function addResult(Result $result): self
    {
        if (!$this->results->contains($result)) {
            $this->results->add($result);
            $result->setSurvey($this);
        }

        return $this;
    }

    public function removeResult(Result $result): self
    {
        if ($this->results->removeElement($result)) {
            // set the owning side to null (unless already changed)
            if ($result->getSurvey() === $this) {
                $result->setSurvey(null);
            }
        }

        return $this;
    }
}
