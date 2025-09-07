<?php

namespace App\Models;

class ProcuratAbsence
{
    private int $id;
    private int $personId;
    private bool $excused;
    private ?string $note;

    function __construct($id, $personId, $excused, $note)
    {
        $this->id = $id;
        $this->personId = $personId;
        $this->excused = $excused;
        $this->note = $note;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPersonId(): int
    {
        return $this->personId;
    }

    /**
     * @return bool
     */
    public function isExcused(): bool
    {
        return $this->excused;
    }

    /**
     * @return ?string
     */
    public function getNote(): ?string
    {
        return $this->note;
    }
}