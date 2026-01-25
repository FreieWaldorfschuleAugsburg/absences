<?php

namespace App\Models;

class ProcuratRelationship
{
    private int $personId;
    private string $relationshipType;
    private bool $custody;
    private bool $physical;
    private bool $realParent;
    private ?string $notes;

    function __construct($personId, $relationshipType, $custody, $physical, $realParent, $notes)
    {
        $this->personId = $personId;
        $this->relationshipType = $relationshipType;
        $this->custody = $custody;
        $this->physical = $physical;
        $this->realParent = $realParent;
        $this->notes = $notes;
    }

    public function getPersonId(): int
    {
        return $this->personId;
    }

    public function getRelationshipType(): string
    {
        return $this->relationshipType;
    }

    public function isCustody(): bool
    {
        return $this->custody;
    }

    public function isPhysical(): bool
    {
        return $this->physical;
    }

    public function isRealParent(): bool
    {
        return $this->realParent;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }
}