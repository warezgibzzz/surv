<?php

namespace App\Entity;

use App\Repository\ResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultRepository::class)]
class Result
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'integer')]
        private ?int    $id = null,

        #[ORM\ManyToOne(targetEntity: Survey::class, inversedBy: 'results')]
        #[ORM\JoinColumn(nullable: false)]
        private ?Survey $survey = null,

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'results')]
        #[ORM\JoinColumn(nullable: false)]
        private ?User   $participant = null,

        #[ORM\Column(type: 'json')]
        private ?array  $data = []
    )
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Survey
     */
    public function getSurvey(): Survey
    {
        return $this->survey;
    }

    /**
     * @param Survey $survey
     */
    public function setSurvey(Survey $survey): void
    {
        $this->survey = $survey;
    }

    /**
     * @return User
     */
    public function getParticipant(): User
    {
        return $this->participant;
    }

    /**
     * @param User $participant
     */
    public function setParticipant(User $participant): void
    {
        $this->participant = $participant;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
