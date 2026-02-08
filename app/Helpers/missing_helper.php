<?php

use App\Models\AlreadyAbsentException;
use App\Models\InvalidPersonException;
use App\Models\ProcuratFollowup;

/**
 * @throws \PHPMailer\PHPMailer\Exception
 */
function sendUncompletedFollowUpReminder(): void
{
    helper('mail');
    $followUps = findUncompletedAbsenceFollowUps();
    if (empty($followUps)) {
        return;
    }

    $count = 0;
    foreach ($followUps as $followUp) {
        if (($absence = getAbsenceToday($followUp->getReferencedPersonId())) && !isHalfDayAbsence($absence)) {
            completeProcuratFollowUp($followUp->getId());
            continue;
        }

        $count++;
    }

    if ($count == 0) {
        return;
    }

    $subject = sprintf("%s ausstehende Meldung(en)", $count);
    sendGenericMail([getenv('absences.reminderEmail')], $subject, $subject,
        "Prüfe bitte die ausstehenden Meldungen in Procurat!5 unter 'Wiedervorlagen' und markiere sie ggf. als erledigt.");
}

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

    $followUps = findUncompletedAbsenceFollowUpsByPersonId($personId);
    foreach ($followUps as $followUp) {
        deleteProcuratFollowUp($followUp->getId());
    }
}

/**
 * @return ProcuratFollowup[]
 */
function findUncompletedAbsenceFollowUps(): array
{
    $followUps = [];
    foreach (getProcuratFollowUps() as $followUp) {
        if (!$followUp->isCompleted() && $followUp->getSubject() == 'Schüler fehlt') {
            $followUps[] = $followUp;
        }
    }
    return $followUps;
}

/**
 * @param int $personId
 * @return ProcuratFollowup[]
 */
function findUncompletedAbsenceFollowUpsByPersonId(int $personId): array
{
    $followUps = [];
    foreach (getProcuratFollowUps() as $followUp) {
        if (!$followUp->isCompleted() && $followUp->getReferencedPersonId() == $personId && $followUp->getSubject() == 'Schüler fehlt') {
            $followUps[] = $followUp;
        }
    }
    return $followUps;
}

function isFollowUpToday(ProcuratFollowUp $followUp): bool
{
    $dueDate = $followUp->getDueDate();
    return $dueDate->format('Y-m-d') == date('Y-m-d');
}