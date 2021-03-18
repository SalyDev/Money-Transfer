<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TransactionRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiResource(
 *  attributes={
 *      "security"="is_granted('ROLE_USER_AGENCE')",
 *      "security_message"="Accès refusé"
 * },
 * )
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 * @UniqueEntity(
 *      fields={"code"},
 *      message="Une transaction avec ce code existe déjà"
 * )
 * @ApiFilter(DateFilter::class, properties={"date_depot"})
 * @ApiFilter(SearchFilter::class, properties={"code": "partial"})
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Champs Obligatoire")
     */
    private $montant;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_depot;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_retrait;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champs Obligatoire")
     */
    private $code;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $frais;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $part_agence_depot;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $part_agence_retrait;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $part_etat;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $part_systeme;

    // l'utilisateur(travailleur) de l'agence qui a fait le dépot
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="depots_agence")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_agence_depot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="retraits_agence")
     */
    private $user_agence_retrait;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="depots_client")
     */
    private $client_depot;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="retraits_client")
     */
    private $client_retrait;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->date_depot;
    }

    public function setDateDepot(?\DateTimeInterface $date_depot): self
    {
        $this->date_depot = $date_depot;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->date_retrait;
    }

    public function setDateRetrait(?\DateTimeInterface $date_retrait): self
    {
        $this->date_retrait = $date_retrait;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getFrais(): ?int
    {
        return $this->frais;
    }

    public function setFrais(int $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getPartAgenceDepot(): ?int
    {
        return $this->part_agence_depot;
    }

    public function setPartAgenceDepot(int $part_agence_depot): self
    {
        $this->part_agence_depot = $part_agence_depot;

        return $this;
    }

    public function getPartAgenceRetrait(): ?int
    {
        return $this->part_agence_retrait;
    }

    public function setPartAgenceRetrait(int $part_agence_retrait): self
    {
        $this->part_agence_retrait = $part_agence_retrait;

        return $this;
    }

    public function getPartEtat(): ?int
    {
        return $this->part_etat;
    }

    public function setPartEtat(int $part_etat): self
    {
        $this->part_etat = $part_etat;

        return $this;
    }

    public function getPartSysteme(): ?int
    {
        return $this->part_systeme;
    }

    public function setPartSysteme(int $part_systeme): self
    {
        $this->part_systeme = $part_systeme;

        return $this;
    }

    public function getUserAgenceDepot(): ?User
    {
        return $this->user_agence_depot;
    }

    public function setUserAgenceDepot(?User $user_agence_depot): self
    {
        $this->user_agence_depot = $user_agence_depot;

        return $this;
    }

    public function getUserAgenceRetrait(): ?User
    {
        return $this->user_agence_retrait;
    }

    public function setUserAgenceRetrait(?User $user_agence_retrait): self
    {
        $this->user_agence_retrait = $user_agence_retrait;

        return $this;
    }

    public function getClientDepot(): ?Client
    {
        return $this->client_depot;
    }

    public function setClientDepot(?Client $client_depot): self
    {
        $this->client_depot = $client_depot;

        return $this;
    }

    public function getClientRetrait(): ?Client
    {
        return $this->client_retrait;
    }

    public function setClientRetrait(?Client $client_retrait): self
    {
        $this->client_retrait = $client_retrait;

        return $this;
    }

}
