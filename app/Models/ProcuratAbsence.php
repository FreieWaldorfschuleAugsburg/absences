<?php

namespace App\Models;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class ProcuratAbsence
{
    private int $id;
    private int $personId;
    private string $date;
    private bool $excused;
    private ?string $note;

    function __construct($id, $personId, $date, $excused, $note)
    {
        $this->id = $id;
        $this->personId = $personId;
        $this->date = $date;
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
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return DateTimeImmutable::createFromFormat('Y-m-d\TH:i:sp', $this->date)->setTimezone(new DateTimeZone('Europe/Berlin'));
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