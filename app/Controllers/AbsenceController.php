<?php

namespace App\Controllers;

use App\Models\AuthException;
use CodeIgniter\HTTP\RedirectResponse;
use function App\Helpers\handleAuthException;
use function App\Helpers\login;
use function App\Helpers\user;

class AbsenceController extends BaseController
{
    public function view(string $groupName): string|RedirectResponse
    {
        try {
            $user = user();
            if (!is_null($user)) {
                $group = findAbsenceGroupByName($groupName);
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
                            $entry['halfDay'] = false;
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

                return $this->render('AbsenceView', ['group' => $group, 'entries' => $entries]);
            }
            return login();
        } catch (AuthException $e) {
            return handleAuthException($this, $e);
        }
    }

    public function absent(int $personId): string|RedirectResponse
    {
        try {
            $user = user();
            if (!is_null($user)) {
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
            return login();
        } catch (AuthException $e) {
            return handleAuthException($this, $e);
        }
    }
}
