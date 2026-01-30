<?php

use App\Models\AbsenceGroupModel;
use App\Models\AlreadyAbsentException;
use App\Models\DueTimeExceededException;
use App\Models\EndBeforeStartDateException;
use App\Models\EntryStatus;
use App\Models\InvalidPersonException;
use App\Models\MaxDaysExceededException;
use App\Models\ProcuratAbsence;
use App\Models\ProcuratFollowup;
use App\Models\ProcuratPerson;
use App\Models\EndBeforeStartTimeException;
use App\Models\MinDateUndercutException;
use Mpdf\MpdfException;
use function App\Helpers\user;

/**
 * @throws MinDateUndercutException
 * @throws EndBeforeStartDateException
 * @throws MaxDaysExceededException
 * @throws InvalidPersonException
 * @throws EndBeforeStartTimeException
 * @throws AlreadyAbsentException
 */
function reportAbsent(int $personId, string $startDateString, string $startIndex, string $endDateString, string $endIndex, string $reason): void
{
    $minDate = getMinAbsenceDate();
    $startDate = DateTimeImmutable::createFromFormat('Y-m-d', $startDateString);
    if ($minDate > $startDate) {
        throw new MinDateUndercutException();
    }

    $endDate = DateTimeImmutable::createFromFormat('Y-m-d', $endDateString);
    if ($startDate > $endDate) {
        throw new EndBeforeStartDateException();
    }

    $dateDiff = $endDate->diff($startDate)->days;
    if ($dateDiff > getMaxAbsenceDays()) {
        throw new MaxDaysExceededException();
    }

    if ($startIndex != -1 && $endIndex != -1) {
        if ($dateDiff == 0 && $startIndex > $endIndex) {
            throw new EndBeforeStartTimeException();
        }
    }

    $person = getProcuratPerson($personId);
    if (!$person) {
        throw new InvalidPersonException();
    }

    $absences = getAbsencesByPersonId($personId);
    for ($i = 0; $i < $dateDiff + 1; $i++) {
        $currentDate = $startDate->add(new DateInterval('P' . $i . 'D'));
        $currentReason = $reason;

        // If first absence in sequence
        if ($i == 0 && $startIndex != '-1') {
            $currentReason .= ' ab ' . getReportTimeslots()[intval($startIndex)];
        }

        // If last absence in sequence
        if ($i == $dateDiff && $endIndex != '-1') {
            $currentReason .= ' bis ' . getReportTimeslots()[intval($endIndex)];
        }

        $absence = findAbsence($absences, $currentDate);
        if ($absence) {
            if (!isHalfDayAbsence($absence)) {
                throw new AlreadyAbsentException();
            }

            deleteProcuratAbsence($absence->getId());
        }

        createProcuratAbsence($personId, $currentDate, $currentReason);
    }
}

/**
 * @param int $personId
 * @return ProcuratPerson[]
 */
function findReportablePersons(int $personId): array
{
    $persons = [];
    $ownPerson = getProcuratPerson($personId);
    // TODO find better criteria
    if ($ownPerson && $ownPerson->isAdult() && $ownPerson->getFamilyRole() == 'child') {
        $persons[] = $ownPerson;
    }

    $relationships = getProcuratRelationships($personId);
    foreach ($relationships as $relationship) {
        if ($relationship->getRelationshipType() == 'son' || $relationship->getRelationshipType() == 'daughter') {
            $persons[] = getProcuratPerson($relationship->getPersonId());
        }
    }

    return $persons;
}

/**
 * @param AbsenceGroupModel $group
 * @param string $view
 * @param EntryStatus[] $ignoreStatus
 * @return void
 * @throws MpdfException
 * @throws \App\Models\OAuthException
 */
function renderPDF(AbsenceGroupModel $group, string $view, array $ignoreStatus): void
{
    helper('mpdf');

    $mpdf = createMPDF();
    $mpdf->WriteHTML(view($view, ['user' => user(), 'group' => $group, 'entries' => generateEntries($group, $ignoreStatus)]));
    $mpdf->Output();
}

/**
 * @param AbsenceGroupModel $group
 * @param EntryStatus[] $ignoreStatus
 * @return array
 */
function generateEntries(AbsenceGroupModel $group, array $ignoreStatus): array
{
    $entries = [];

    $members = findAbsenceGroupMembers($group);
    $absences = getProcuratAbsences();
    $followUps = getProcuratFollowUps();

    foreach ($members as $member) {
        $entry = ['person' => $member, 'status' => EntryStatus::Present];

        $absence = findAbsenceByPersonId($absences, $member->getId());
        if ($absence) {
            $entry['status'] = isHalfDayAbsence($absence) ? EntryStatus::HalfDay : EntryStatus::Absent;
            $note = $absence->getNote();
            if (strlen($note) > 0) {
                $entry['note'] = $note;
            }
        } else {
            $followUp = findFollowUpByPersonId($followUps, $member->getId());
            if ($followUp) {
                $entry['status'] = EntryStatus::Missing;
                $entry['note'] = $followUp->getMessage();
            }
        }

        if (in_array($entry['status'], $ignoreStatus)) {
            continue;
        }

        $entries[] = $entry;
    }

    return $entries;
}

/**
 * @param ProcuratAbsence[] $absences
 * @param int $personId
 * @return ProcuratAbsence|null
 *
 */
function findAbsenceByPersonId(array $absences, int $personId): ?ProcuratAbsence
{
    foreach ($absences as $absence) {
        // Only match if excused
        if ($absence->getPersonId() == $personId && $absence->isExcused()) {
            return $absence;
        }
    }

    return null;
}

/**
 * @param ProcuratFollowup[] $followUps
 * @param int $personId
 * @return ProcuratFollowup|null
 */
function findFollowUpByPersonId(array $followUps, int $personId): ?ProcuratFollowup
{
    foreach ($followUps as $followUp) {
        // Only match uncompleted with correct subject
        if (!$followUp->isCompleted() && $followUp->getReferencedPersonId() == $personId
            && isFollowUpToday($followUp) && $followUp->getSubject() == 'Schüler fehlt') {
            return $followUp;
        }
    }

    return null;
}

/**
 * @return string[]
 */
function getReportReasons(): array
{
    return explode(',', getenv('absences.report.reasons'));
}

/**
 * @return string[]
 */
function getReportTimeslots(): array
{
    return explode(',', getenv('absences.report.timeslots'));
}


/**
 * @return int
 */
function getMaxAbsenceDays(): int
{
    return intval(getenv('absences.report.maxDays'));
}

/**
 * @return DateTime
 */
function getMinAbsenceDate(): DateTime
{
    $now = new DateTime();
    if ($now->getTimestamp() > getAbsenceDueDateTimestamp($now)) {
        $now->modify('+1 weekdays');
    }

    return $now->setTime(0, 0);
}

/**
 * @return string
 */
function getMinAbsenceDateFormatted(): string
{
    return getMinAbsenceDate()->format('Y-m-d');
}

/**
 * @param DateTimeInterface $now
 * @return int
 */
function getAbsenceDueDateTimestamp(DateTimeInterface $now): int
{
    return strtotime($now->format('Y-m-d') . ' ' . getenv('absences.report.dueTime'));
}