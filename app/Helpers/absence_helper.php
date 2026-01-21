<?php

use App\Models\AbsenceGroupModel;
use Mpdf\MpdfException;
use function App\Helpers\user;

/**
 * @throws MpdfException
 * @throws \App\Models\OAuthException
 */
function renderAbsencesPDF(AbsenceGroupModel $group): void
{
    renderPDF($group, 'print/AbsentPrintView', true);
}

/**
 * @throws MpdfException
 * @throws \App\Models\OAuthException
 */
function renderPresentPDF(AbsenceGroupModel $group): void
{
    renderPDF($group, 'print/PresentPrintView', false);
}

/**
 * @throws MpdfException
 * @throws \App\Models\OAuthException
 */
function renderPDF(AbsenceGroupModel $group, string $view, bool $absentOnly): void
{
    helper('mpdf');

    $mpdf = createMPDF();
    $mpdf->WriteHTML(view($view, ['user' => user(), 'group' => $group, 'entries' => generateEntries($group, $absentOnly)]));
    $mpdf->Output();
}

function generateEntries(AbsenceGroupModel $group, bool $absentOnly): array
{
    $entries = [];

    $members = findAbsenceGroupMembers($group);
    $absences = getProcuratAbsences();
    $followUps = getProcuratFollowUps();

    foreach ($members as $member) {
        $entry = ['person' => $member];

        foreach ($absences as $absence) {
            if ($absence->getPersonId() == $member->getId() && $absence->isExcused()) {
                $entry['absent'] = true;
                $entry['halfDay'] = isHalfDayAbsence($absence);
                $entry['note'] = $absence->getNote();
                break;
            }
        }

        if (key_exists('absent', $entry)) {
            continue;
        }


        if (key_exists('absent', $entry)) {
            foreach ($followUps as $followUp) {
                if ($followUp->getReferencedPersonId() == $member->getId() && $followUp->getSubject() == 'Schüler fehlt'
                    && isFollowUpToday($followUp)) {
                    $entry['absent'] = true;
                    $entry['note'] = $followUp->getMessage();
                    break;
                }
            }
        }

        if (key_exists('absent', $entry)) {
            $entries[] = $entry;
        }
    }

    return $entries;
}