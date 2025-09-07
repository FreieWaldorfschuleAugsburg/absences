<?php

namespace App\Models;

class UserModel
{
    private string $username;
    private string $displayName;
    private string $idToken;
    private string $refreshToken;

    function __construct($username, $displayName, $idToken, $refreshToken)
    {
        $this->username = $username;
        $this->displayName = $displayName;
        $this->idToken = $idToken;
        $this->refreshToken = $refreshToken;
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
}