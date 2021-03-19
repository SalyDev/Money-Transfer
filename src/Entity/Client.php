<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ClientRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


/**
 * @ApiResource(
 *  attributes={
 *    "security"="is_granted('ROLE_USER_AGENCE')",
 *    "security_message"="Accès refusé"
 * },
 * )
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @ApiFilter(SearchFilter::class, properties={"numero_cni": "partial"})
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"transaction:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champs Obligatoire")
     * @Groups({"transaction:read"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champs Obligatoire")
     * @Groups({"transaction:read"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champs Obligatoire")
     * @Assert\Regex(
     *     pattern="/^(77|76|75|78|70)[0-9]{7}/",
     *     message="Numéro de tétéphone invalide"
     * )
     * @Groups({"transaction:read"})
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Regex(
     *     pattern="/^[0-9]{13}$/",
     *     message="N. CNI invalide"
     * )
     * @Groups({"transaction:read"})
     */
    private $numero_cni;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="client_depot")
     */
    private $depots_client;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="client_retrait")
     */
    private $retraits_client;

    public function __construct()
    {
        $this->depots_client = new ArrayCollection();
        $this->retraits_client = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getNumeroCni(): ?string
    {
        return $this->numero_cni;
    }

    public function setNumeroCni(string $numero_cni): self
    {
        $this->numero_cni = $numero_cni;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getDepotsClient(): Collection
    {
        return $this->depots_client;
    }

    public function addDepotsClient(Transaction $depotsClient): self
    {
        if (!$this->depots_client->contains($depotsClient)) {
            $this->depots_client[] = $depotsClient;
            $depotsClient->setClientDepot($this);
        }

        return $this;
    }

    public function removeDepotsClient(Transaction $depotsClient): self
    {
        if ($this->depots_client->removeElement($depotsClient)) {
            // set the owning side to null (unless already changed)
            if ($depotsClient->getClientDepot() === $this) {
                $depotsClient->setClientDepot(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getRetraitsClient(): Collection
    {
        return $this->retraits_client;
    }

    public function addRetraitsClient(Transaction $retraitsClient): self
    {
        if (!$this->retraits_client->contains($retraitsClient)) {
            $this->retraits_client[] = $retraitsClient;
            $retraitsClient->setClientRetrait($this);
        }

        return $this;
    }

    public function removeRetraitsClient(Transaction $retraitsClient): self
    {
        if ($this->retraits_client->removeElement($retraitsClient)) {
            // set the owning side to null (unless already changed)
            if ($retraitsClient->getClientRetrait() === $this) {
                $retraitsClient->setClientRetrait(null);
            }
        }

        return $this;
    }
}
