<?php

namespace App\Controllers;

use App\Models\EndBeforeStartDateException;
use App\Models\EntryStatus;
use App\Models\InvalidPersonException;
use App\Models\MaxDiffException;
use App\Models\OAuthException;
use CodeIgniter\HTTP\RedirectResponse;
use Exception;
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
        } catch (InvalidPersonException) {
            return redirect('/')->with('error', lang('absences.index.invalidPerson'));
        } catch (EndBeforeStartDateException) {
            return redirect('/')->with('error', lang('absences.index.endBeforeStartDate'));
        } catch (MaxDiffException) {
            return redirect('/')->with('error', lang('absences.index.maxDiff'));
        } catch (Exception) {
            return redirect('/')->with('error', lang('absences.index.unknownError'));
        }

        return redirect('/')->with('success', lang('absences.index.reportSuccessful'));
    }
}
