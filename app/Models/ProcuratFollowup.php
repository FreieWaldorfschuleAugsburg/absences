<?php

namespace App\Models;

use DateTime;

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
     * @return DateTime
     */
    public function getDueDate(): string
    {
        return $this->dueDate;
    }

    /**
     * @return int
     */
    public function getAssignedPersonId(): int
    {
        return $this->assignedPersonId;
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
}