<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource()
 * @UniqueEntity(
 *      fields={"telephone"},
 *      message="Un utilisateur avec ce numéro de téléphone existe deja"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/",
     *     message="Email invalide"
     * )
     * @Groups({"compte:write"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Champs Obligatoire")
     * @Groups({"compte:write"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champs Obligatoire")
     * @Groups({"compte:write"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champs Obligatoire")
     * @Groups({"compte:write"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champs Obligatoire")
     * @Groups({"compte:write"})
     */
    private $telephone;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    // role_user => status du user => admin_systeme, admin_agence, user_agence, caissier(caissier systeme)
    private $role_user;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="user_agence_depot")
     */
    // les transactions de dépot d'un travailleur de l'agence
    private $depots_agence;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="user_agence_retrait")
     */
    private $retraits_agence;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="users")
     */
    private $agence;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_actif = 1;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $avatar;

    public function __construct()
    {
        $this->depots_agence = new ArrayCollection();
        $this->retraits_agence = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        // $this->roles = $roles;
        // $roles = 'ROLE_'.strtoupper($this->getRoleUser());
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getRoleUser(): ?Role
    {
        return $this->role_user;
    }

    public function setRoleUser(?Role $role_user): self
    {
        $this->role_user = $role_user;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getDepotsAgence(): Collection
    {
        return $this->depots_agence;
    }

    public function addDepotsAgence(Transaction $depotsAgence): self
    {
        if (!$this->depots_agence->contains($depotsAgence)) {
            $this->depots_agence[] = $depotsAgence;
            $depotsAgence->setUserAgenceDepot($this);
        }

        return $this;
    }

    public function removeDepotsAgence(Transaction $depotsAgence): self
    {
        if ($this->depots_agence->removeElement($depotsAgence)) {
            // set the owning side to null (unless already changed)
            if ($depotsAgence->getUserAgenceDepot() === $this) {
                $depotsAgence->setUserAgenceDepot(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getRetraitsAgence(): Collection
    {
        return $this->retraits_agence;
    }

    public function addRetraitsAgence(Transaction $retraitsAgence): self
    {
        if (!$this->retraits_agence->contains($retraitsAgence)) {
            $this->retraits_agence[] = $retraitsAgence;
            $retraitsAgence->setUserAgenceRetrait($this);
        }

        return $this;
    }

    public function removeRetraitsAgence(Transaction $retraitsAgence): self
    {
        if ($this->retraits_agence->removeElement($retraitsAgence)) {
            // set the owning side to null (unless already changed)
            if ($retraitsAgence->getUserAgenceRetrait() === $this) {
                $retraitsAgence->setUserAgenceRetrait(null);
            }
        }

        return $this;
    }

    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): self
    {
        $this->agence = $agence;

        return $this;
    }

    public function getIsActif(): ?bool
    {
        return $this->is_actif;
    }

    public function setIsActif(bool $is_actif): self
    {
        $this->is_actif = $is_actif;

        return $this;
    }

    public function getAvatar()
    {
        if(!$this->avatar){
            return null;
        }
        else{
            if(is_resource($this->avatar)){
                return base64_encode(stream_get_contents($this->avatar)); 
            }
            else{
                return base64_encode($this->avatar); 
            }
        }
    }


    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }



  

}
