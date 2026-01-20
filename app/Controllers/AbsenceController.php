<?php

namespace App\Controllers;

use App\Models\AlreadyAbsentException;
use App\Models\InvalidPersonException;
use App\Models\OAuthException;
use CodeIgniter\HTTP\RedirectResponse;
use Mpdf\MpdfException;
use function App\Helpers\user;

class AbsenceController extends BaseController
{
    public function view(string $id): string|RedirectResponse
    {
        $group = getAbsenceGroup($id);
        if (!$group) {
            return redirect('/')->with('error', lang('absences.error.invalidGroup'));
        }

        $entries = [];
        $members = findAbsenceGroupMembers($group);
        $absences = getProcuratAbsences();
        $followUps = getProcuratFollowUps();

        foreach ($members as $member) {
            $entry = ['person' => $member];

            foreach ($absences as $absence) {
                if ($absence->getPersonId() == $member->getId() && $absence->isExcused()) {
                    $entry['absent'] = true;
                    $note = $absence->getNote();
                    if ($note && strlen($note) > 0) {
                        $entry['note'] = $absence->getNote();
                    }
                    $entry['halfDay'] = isHalfDayAbsence($absence);
                    break;
                }
            }

            foreach ($followUps as $followUp) {
                if ($followUp->getReferencedPersonId() == $member->getId()
                    && $followUp->getSubject() == 'Schüler fehlt' && isFollowUpToday($followUp)) {
                    $entry['followUp'] = true;
                    $entry['note'] = $followUp->getMessage();
                    break;
                }
            }

            $entries[] = $entry;
        }

        return view('AbsenceView', ['group' => $group, 'entries' => $entries]);
    }

    /**
     * @throws MpdfException
     * @throws OAuthException
     */
    public function printAbsent(string $id): RedirectResponse|string
    {
        helper('mpdf');

        $user = user();
        $group = getAbsenceGroup($id);
        if (!$group) {
            return redirect('/')->with('error', lang('absences.error.invalidGroup'));
        }

        $entries = [];
        $members = findAbsenceGroupMembers($group);
        $absences = getProcuratAbsences();
        $followUps = getProcuratFollowUps();

        foreach ($members as $member) {
            $entry = ['person' => $member];

            foreach ($absences as $absence) {
                if ($absence->getPersonId() == $member->getId() && $absence->isExcused()) {
                    $entry['absent'] = true;
                    $entry['note'] = $absence->getNote();
                    break;
                }
            }

            foreach ($followUps as $followUp) {
                if ($followUp->getReferencedPersonId() == $member->getId() && $followUp->getSubject() == 'Schüler fehlt'
                    && isFollowUpToday($followUp)) {
                    $entry['absent'] = true;
                    $entry['note'] = $followUp->getMessage();
                    break;
                }
            }

            if (key_exists('absent', $entry)) {
                $entries[] = $entry;
            }
        }

        $mpdf = createMPDF();
        $mpdf->WriteHTML(view('print/AbsentPrintView', ['user' => $user, 'group' => $group, 'entries' => $entries]));
        $mpdf->Output();
        exit;
    }

    /**
     * @throws MpdfException
     * @throws OAuthException
     */
    public function printPresent(string $id): RedirectResponse|string
    {
        helper('mpdf');

        $user = user();
        $group = getAbsenceGroup($id);
        if (!$group) {
            return redirect('/')->with('error', lang('absences.error.invalidGroup'));
        }

        $entries = [];
        $members = findAbsenceGroupMembers($group);
        $absences = getProcuratAbsences();
        $followUps = getProcuratFollowUps();

        foreach ($members as $member) {
            $entry = ['person' => $member];

            foreach ($absences as $absence) {
                if ($absence->getPersonId() == $member->getId() && $absence->isExcused()) {
                    $entry['absent'] = true;
                    if (isHalfDayAbsence($absence)) {
                        $entry['halfDay'] = true;
                        $entry['note'] = $absence->getNote();
                        break;
                    }
                }
            }

            // Skip fully absent students
            if (key_exists('absent', $entry) && !key_exists('halfDay', $entry)) {
                continue;
            }

            foreach ($followUps as $followUp) {
                if ($followUp->getReferencedPersonId() == $member->getId() && $followUp->getSubject() == 'Schüler fehlt' && isFollowUpToday($followUp)) {
                    $entry['absent'] = true;
                    $entry['note'] = $followUp->getMessage();
                    break;
                }
            }

            $entries[] = $entry;
        }

        $mpdf = createMPDF();
        $mpdf->WriteHTML(view('print/PresentPrintView', ['user' => $user, 'group' => $group, 'entries' => $entries]));
        $mpdf->Output();
        exit;
    }
}
