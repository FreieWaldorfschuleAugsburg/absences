<?php

namespace App\Models;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class ProcuratFollowup
{
    private int $id;
    private string $dueDate;
    private ?int $assignedGroupId;
    private string $subject;
    private string $message;
    private ?int $referencedPersonId;
    private bool $completed;

    function __construct($id, $dueDate, $assignedGroupId, $subject, $message, $referencedPersonId, $completed)
    {
        $this->id = $id;
        $this->dueDate = $dueDate;
        $this->assignedGroupId = $assignedGroupId;
        $this->subject = $subject;
        $this->message = $message;
        $this->referencedPersonId = $referencedPersonId;
        $this->completed = $completed;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDueDate(): DateTimeInterface
    {
        return DateTimeImmutable::createFromFormat('Y-m-d\TH:i:sp', $this->dueDate)->setTimezone(new DateTimeZone('Europe/Berlin'));
    }

    /**
     * @return int|null
     */
    public function getAssignedGroupId(): ?int
    {
        return $this->assignedGroupId;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return ?int
     */
    public function getReferencedPersonId(): ?int
    {
        return $this->referencedPersonId;
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->completed;
    }

    /**
     * @param bool $completed
     */
    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }
}