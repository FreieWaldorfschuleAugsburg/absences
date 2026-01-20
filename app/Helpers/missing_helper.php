<?php

use App\Models\AlreadyAbsentException;
use App\Models\InvalidPersonException;
use App\Models\ProcuratFollowup;

/**
 * @throws InvalidPersonException
 * @throws AlreadyAbsentException
 */
function reportMissing(int $personId, string $reporterName): void
{
    $person = getProcuratPerson($personId);
    if (!$person) {
        throw new InvalidPersonException();
    }

    if (isAbsentToday($person->getId())) {
        throw new AlreadyAbsentException();
    }

    createProcuratFollowUp(intval(getenv('absences.assignedGroupId')), $person->getId(), date('Y-m-d') . 'T00:00:00Z',
        'Schüler fehlt', 'Von ' . $reporterName . ' um ' . date('H:i') . ' fehlend gemeldet');
}

/**
 * @throws InvalidPersonException
 */
function revokeMissing(int $personId): void
{
    $person = getProcuratPerson($personId);
    if (!$person) {
        throw new InvalidPersonException();
    }

    $followUps = findUncompletedProcuratFollowUps($personId, 'Schüler fehlt');
    foreach ($followUps as $followUp) {
        deleteProcuratFollowUp($followUp->getId());
    }
}

function isFollowUpToday(ProcuratFollowUp $followUp): bool
{
    $dueDate = $followUp->getDueDate();
    return $dueDate['day'] == date('d') && $dueDate['month'] == date('m') && $dueDate['year'] == date('Y');
}