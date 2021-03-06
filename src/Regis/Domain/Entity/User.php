<?php

declare(strict_types=1);

namespace Regis\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Regis\Domain\Uuid;

/**
 * @todo all the symfony and doctrine-related stuff should be in the infrastructure layer
 */
class User implements UserInterface
{
    private $id;
    private $username;
    private $email;
    private $githubId;
    private $githubAccessToken;
    private $roles = [];
    private $password;
    private $repositories;
    private $ownedTeams;
    private $teams;

    public static function createAdmin(string $username, string $password, string $email): User
    {
        $user = new static($username, $password);
        $user->changeEmail($email);
        $user->changePassword($password);
        $user->roles = ['ROLE_ADMIN'];

        return $user;
    }

    public static function createUser(string $username, string $email, int $githubId, string $githubAccessToken): User
    {
        $user = new static($username);
        $user->githubId = $githubId;
        $user->changeEmail($email);
        $user->changeGithubAccessToken($githubAccessToken);
        $user->roles = ['ROLE_USER'];

        return $user;
    }

    private function __construct(string $username, string $password = null)
    {
        $this->id = Uuid::create();
        $this->username = $username;

        $this->repositories = new ArrayCollection();
    }

    public function changePassword(string $password)
    {
        if (empty($password)) {
            throw new \InvalidArgumentException('The new password can not be empty');
        }

        $this->password = $password;
    }

    public function changeEmail(string $email)
    {
        if (empty($email)) {
            throw new \InvalidArgumentException('The new email can not be empty');
        }

        $this->email = $email;
    }

    public function changeGithubAccessToken(string $accessToken)
    {
        if (empty($accessToken)) {
            throw new \InvalidArgumentException('The new access token can not be empty');
        }

        $this->githubAccessToken = $accessToken;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return int|null
     */
    public function getGithubId()
    {
        return $this->githubId;
    }

    /**
     * @return string|null
     */
    public function getGithubAccessToken()
    {
        return $this->githubAccessToken;
    }

    public function getRepositories(): \Traversable
    {
        return $this->repositories;
    }

    public function getOwnedTeams(): \Traversable
    {
        return $this->ownedTeams;
    }

    public function getTeams(): \Traversable
    {
        return $this->teams;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->username;
    }
    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return null;
    }
}
