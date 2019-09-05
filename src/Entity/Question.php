<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $question;

    /**
     * @ORM\Column(type="integer")
     */
    private $question_order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Form", inversedBy="questions")
     */
    private $form;

    public function setId(int $id): self{
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getQuestionOrder(): ?int
    {
        return $this->question_order;
    }

    public function setQuestionOrder(int $question_order): self
    {
        $this->question_order = $question_order;

        return $this;
    }

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setForm(?Form $form): self
    {
        $this->form = $form;

        return $this;
    }

    public function getDisplayedQuestion(){
        return "Enter " . $this->getQuestion() . ": ";
    }
}
