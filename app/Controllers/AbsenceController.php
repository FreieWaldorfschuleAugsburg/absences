<?php

namespace App\Controllers;

use App\Models\EntryStatus;
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
}
