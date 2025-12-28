<?php

namespace App\Controllers;

use App\Models\OAuthException;
use CodeIgniter\HTTP\RedirectResponse;
use function App\Helpers\logout;

class IndexController extends BaseController
{
    public function index(): string
    {
        $groups = getAbsenceGroups();
        return view('IndexView', ['groups' => $groups]);
    }

    /**
     * @throws OAuthException
     */
    public function logout(): RedirectResponse
    {
        return logout();
    }
}
