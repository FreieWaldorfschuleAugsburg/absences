<?php

namespace App\Controllers;

use App\Models\AlreadyAbsentException;
use App\Models\EndBeforeStartDateException;
use App\Models\EndBeforeStartTimeException;
use App\Models\EntryStatus;
use App\Models\InvalidPersonException;
use App\Models\MaxDaysExceededException;
use App\Models\NoCustodyException;
use App\Models\OAuthException;
use App\Models\MinDateUndercutException;
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

        return view('AbsenceView', ['group' => $group, 'entries' => generateEntries($group, [])]);
    }

    /**
     * @throws MpdfException
     * @throws OAuthException
     */
    public function printAbsent(string $id): RedirectResponse|string
    {
        $group = getAbsenceGroup($id);
        if (!$group) {
            return redirect('/')->with('error', lang('absences.error.invalidGroup'));
        }

        renderPDF($group, 'print/AbsentPrintView', [EntryStatus::Present]);
        exit;
    }

    /**
     * @throws MpdfException
     * @throws OAuthException
     */
    public function printPresent(string $id): RedirectResponse|string
    {
        $group = getAbsenceGroup($id);
        if (!$group) {
            return redirect('/')->with('error', lang('absences.error.invalidGroup'));
        }

        renderPDF($group, 'print/PresentPrintView', [EntryStatus::Absent]);
        exit;
    }

    public function reportAbsent(): RedirectResponse
    {
        $personId = $this->request->getPost('person');
        $startDate = $this->request->getPost('startDate');
        $startTime = $this->request->getPost('startTime');
        $endDate = $this->request->getPost('endDate');
        $endTime = $this->request->getPost('endTime');
        $reason = $this->request->getPost('reason');

        try {
            reportAbsent($personId, $startDate, $startTime, $endDate, $endTime, $reason);
        } catch (MinDateUndercutException) {
            return redirect('/')->with('error', lang('absences.error.minDateUndercut'));
        } catch (EndBeforeStartDateException) {
            return redirect('/')->with('error', lang('absences.error.endBeforeStartDate'));
        } catch (EndBeforeStartTimeException) {
            return redirect('/')->with('error', lang('absences.error.endBeforeStartTime'));
        } catch (MaxDaysExceededException) {
            return redirect('/')->with('error', lang('absences.error.maxDaysExceeded'));
        } catch (InvalidPersonException) {
            return redirect('/')->with('error', lang('absences.error.invalidPerson'));
        } catch (AlreadyAbsentException) {
            return redirect('/')->with('error', lang('absences.error.alreadyAbsentTimespan'));
        } catch (NoCustodyException) {
            return redirect('/')->with('error', lang('absences.error.noCustody'));
        }

        return redirect('/')->with('success', lang('absences.reportSuccessful'));
    }

    public function absenceEvents(string $id): string
    {
        $person = getProcuratPerson(intval($id));
        if (!$person) {
            $this->response->setStatusCode(404);
            return "";
        }

        $user = user();
        if (!isProcuratChildCustodyRelationship($user->getProcuratId(), $person->getId())) {
            $this->response->setStatusCode(403);
            return "";
        }

        $absences = getSchoolYearAbsencesByPersonId($person->getId());
        $events = [];
        foreach ($absences as $entry) {
            $formattedDate = $entry->getDate()->format("Y-m-d");
            $events[] = [
                "title" => is_null($entry->getNote()) ? lang('absences.allDay') : $entry->getNote(),
                "start" => $formattedDate,
                "end" => $formattedDate,
            ];
        }
        return json_encode($events);
    }
}
