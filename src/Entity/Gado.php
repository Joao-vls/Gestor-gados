<?php

namespace App\Entity;

use App\Repository\GadoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
* @ORM\Entity(repositoryClass=GadoRepository::class)
*@UniqueEntity(fields={"id"},message="Este ID esta em uso.")
*/
class Gado
{
  /**
  * @ORM\Id
  *
  *@ORM\Column(type="string",unique=true)
  *
  */
  private $id;


  /**
  * @ORM\Column(type="float",options={"default":0})
  *
  */
  private $leite;

  /**
  * @ORM\Column(type="float",options={"default":0})
  *
  */
  private $racao;

  /**
  * @ORM\Column(type="float",options={"default":0})
  *
  */
  private $peso;

  /**
  * @ORM\Column(type="datetime")
  *@Assert\NotBlank(message="data obrigatoria.")
  *@Assert\LessThanOrEqual("today",message="data não pode ser futura.")
  */
  private $nascimento;

  public function getId(): ?string
  {
    return $this->id;
  }
  public function setId(?string $id): self
  {
    $this->id = $id;

    return $this;
  }

  public function getLeite(): ?float
  {
    return $this->leite;
  }

  public function setLeite(?float $leite): self
  {
    $this->leite = $leite;

    return $this;
  }

  public function getRacao(): ?float
  {
    return $this->racao;
  }

  public function setRacao(?float $racao): self
  {
    $this->racao = $racao;

    return $this;
  }

  public function getPeso(): ?float
  {
    return $this->peso;
  }

  public function setPeso(?float $peso): self
  {
    $this->peso = $peso;

    return $this;
  }

  public function getNascimento(): ?\DateTimeInterface
  {
    return $this->nascimento;
  }

  public function setNascimento(?\DateTimeInterface $nascimento): self
  {
    $this->nascimento = $nascimento;

    return $this;
  }
  public function getTd()
  {
    return $this;
  }
}
