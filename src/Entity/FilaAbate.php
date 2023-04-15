<?php

namespace App\Entity;

use App\Repository\FilaAbateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * @ORM\Entity(repositoryClass=FilaAbateRepository::class)
 * @ORM\\Table(name="fila_abate")
 */
class FilaAbate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $morte;

    /**
     * @ORM\Column(type="string")
     */
    private $infoantigas;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMorte(): ?\DateTimeInterface
    {
        return $this->morte;
    }

    public function setMorte(\DateTimeInterface $morte): self
    {
        $this->morte = $morte;

        return $this;
    }

    public function getInfoantigas(): ?string
    {
        return $this->infoantigas;
    }

    public function setInfoantigas(string $infoantigas): self
    {
        $this->infoantigas = $infoantigas;

        return $this;
    }
}
