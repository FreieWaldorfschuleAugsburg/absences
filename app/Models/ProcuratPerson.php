<?php

namespace App\Models;

class ProcuratPerson
{
    private int $id;
    private ?string $firstName;
    private ?string $lastName;

    function __construct($id, $firstName, $lastName)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return ?string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return ?string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}