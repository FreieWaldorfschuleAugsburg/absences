<?php

use App\Models\AbsenceGroupModel;
use App\Models\EntryStatus;
use App\Models\ProcuratAbsence;
use App\Models\ProcuratFollowup;
use Mpdf\MpdfException;
use function App\Helpers\user;

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