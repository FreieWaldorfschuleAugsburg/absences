<?php

namespace App\Models;

use DateTime;

class ProcuratPerson
{
    private int $id;
    private ?string $firstName;
    private ?string $lastName;
    private ?string $birthDate;
    private ?string $familyRole;

    function __construct($id, $firstName, $lastName, $birthDate, $familyRole)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birthDate = $birthDate;
        $this->familyRole = $familyRole;
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
     * @return string|null
     */
    public function getBirthDate(): ?string
    {
        return $this->birthDate;
    }

    /**
     * @return string|null
     */
    public function getFamilyRole(): ?string
    {
        return $this->familyRole;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function isAdult(): ?bool
    {
        if (!$this->birthDate) {
            return null;
        }

        $age = DateTime::createFromFormat('Y-m-d\TH:i:sp', $this->birthDate)->diff(new DateTime('now'))->y;
        return $age >= 18;
    }
}