<?php

namespace App\Controllers;

use App\Models\OAuthException;
use CodeIgniter\HTTP\RedirectResponse;
use Mpdf\Mpdf;
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
                if ($followUp->getReferencedPersonId() == $member->getId() && $followUp->getSubject() == 'Schüler fehlt') {
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
                if ($followUp->getReferencedPersonId() == $member->getId() && $followUp->getSubject() == 'Schüler fehlt') {
                    $entry['absent'] = true;
                    $entry['note'] = $followUp->getMessage();
                    break;
                }
            }

            if (key_exists('absent', $entry)) {
                $entries[] = $entry;
            }
        }

        $mpdf = $this->createMPDF();
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
                if ($followUp->getReferencedPersonId() == $member->getId() && $followUp->getSubject() == 'Schüler fehlt') {
                    $entry['absent'] = true;
                    $entry['note'] = $followUp->getMessage();
                    break;
                }
            }

            $entries[] = $entry;
        }

        $mpdf = $this->createMPDF();
        $mpdf->WriteHTML(view('print/PresentPrintView', ['user' => $user, 'group' => $group, 'entries' => $entries]));
        $mpdf->Output();
        exit;
    }

    /**
     * @throws OAuthException
     */
    public function absent(int $personId): string|RedirectResponse
    {
        $user = user();
        $person = getProcuratPerson($personId);
        if (!$person) {
            return redirect()->back()->with('error', lang('absences.error.invalidPerson'));
        }

        if (isAbsentToday($person->getId())) {
            return redirect()->back()->with('error', lang('absences.error.alreadyAbsent'));
        }

        createProcuratFollowUp(intval(getenv('absences.assignedPersonId')), $person->getId(), date('Y-m-d') . 'T00:00:00Z',
            'Schüler fehlt', 'Von ' . $user->getDisplayName() . ' um ' . date('H:i') . ' fehlend gemeldet');

        return redirect()->back();
    }

    public function revoke(int $personId): string|RedirectResponse
    {
        $person = getProcuratPerson($personId);
        if (!$person) {
            return redirect()->back()->with('error', lang('absences.error.invalidPerson'));
        }

        if (isAbsentToday($person->getId())) {
            return redirect()->back()->with('error', lang('absences.error.alreadyAbsent'));
        }

        $followUps = findUncompletedProcuratFollowUps($personId, 'Schüler fehlt');
        if (empty($followUps)) {
            return redirect()->back()->with('error', lang('absences.error.noFollowUp'));
        }

        foreach ($followUps as $followUp) {
            deleteProcuratFollowUp($followUp->getId());
        }

        return redirect()->back();
    }

    /**
     * @throws MpdfException
     */
    private function createMPDF(): Mpdf
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 19,
            'margin_right' => 19,
            'margin_top' => 14,
            'margin_bottom' => 45,
            'margin_header' => 19,
            'margin_footer' => 19,
            'orientation' => 'P']);
        $mpdf->setHTMLFooter(view('print/PrintFooter'));
        return $mpdf;
    }
}
