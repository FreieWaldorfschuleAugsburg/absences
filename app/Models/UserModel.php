<?php

namespace App\Models;

class UserModel
{
    private string $username;
    private string $displayName;
    private string $idToken;
    private string $refreshToken;
    private array $groups;

    function __construct($username, $displayName, $idToken, $refreshToken, $groups)
    {
        $this->username = $username;
        $this->displayName = $displayName;
        $this->idToken = $idToken;
        $this->refreshToken = $refreshToken;
        $this->groups = $groups;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getIdToken(): string
    {
        return $this->idToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function isStaff(): bool
    {
        return in_array(getenv('oidc.group'), $this->getGroups());
    }
}