<?php

namespace App\Controllers;

use App\Models\OAuthException;
use CodeIgniter\HTTP\RedirectResponse;
use function App\Helpers\logout;
use function App\Helpers\user;

class IndexController extends BaseController
{
    /**
     * @throws OAuthException
     */
    public function index(): string
    {
        $user = user();
        $groups = $user->isStaff() ? getAbsenceGroups() : [];
        $reportablePersons = $user->getProcuratId() ? findReportablePersons($user->getProcuratId()) : [];
        return view('IndexView', ['user' => user(), 'groups' => $groups, 'reportablePersons' => $reportablePersons]);
    }

    /**
     * @throws OAuthException
     */
    public function logout(): RedirectResponse
    {
        return logout();
    }
}
