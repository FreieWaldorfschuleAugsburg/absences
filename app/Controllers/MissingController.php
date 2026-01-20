<?php

namespace App\Controllers;

use App\Models\AlreadyAbsentException;
use App\Models\InvalidPersonException;
use App\Models\OAuthException;
use CodeIgniter\HTTP\RedirectResponse;
use Mpdf\MpdfException;
use function App\Helpers\user;

class MissingController extends BaseController
{
    /**
     * @throws OAuthException
     */
    public function reportMissing(int $personId): string|RedirectResponse
    {
        $user = user();

        try {
            reportMissing($personId, $user->getDisplayName());
            return redirect()->back();
        } catch (AlreadyAbsentException) {
            return redirect()->back()->with('error', lang('absences.error.alreadyAbsent'));
        } catch (InvalidPersonException) {
            return redirect()->back()->with('error', lang('absences.error.invalidPerson'));
        }
    }

    public function revokeMissing(int $personId): string|RedirectResponse
    {
        try {
            revokeMissing($personId);
            return redirect()->back();
        } catch (InvalidPersonException) {
            return redirect()->back()->with('error', lang('absences.error.invalidPerson'));
        }
    }
}
