<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "detail",
 *      href = @Hateoas\Route(
 *          "detailuser",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="user:read")
 * )
 * 
 * @Hateoas\Relation(
 *      "create",
 *      href = @Hateoas\Route(
 *          "createtUser",
 *          parameters = { "id" = "expr(object.getId())" },
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="user:read", excludeIf = "expr(not is_granted('ROLE_ADMIN'))"),
 * )
 *
 *
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "deleteUser",
 *          parameters = {
 *              "clientId" = "expr(object.getClient().getId())",
 *              "userId" = "expr(object.getId())"
 *          },
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="user:read", excludeIf = "expr(not is_granted('ROLE_ADMIN'))"),
 * )

 *
 * 
 * @Hateoas\Relation(
 *      "list",
 *      href = @Hateoas\Route(
 *          "users",
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="user:read")
 * )
 *
 */

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read'])]
    private ?string $email = null;

    #[\JMS\Serializer\Annotation\Type("array")]
    #[ORM\Column(type:"json")]
    #[Groups(['user:read'])]
    private array $roles = [];


    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['user:read'])]
    private ?Client $client = null;

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
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

        /**
     * Méthode getUsername qui permet de retourner le champ qui est utilisé pour l'authentification.
     *
     * @return string
     */
    public function getUsername(): string {
        return $this->getUserIdentifier();
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
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
