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
        return view('IndexView', ['user' => user(), 'groups' => getAbsenceGroups()]);
    }

    /**
     * @throws OAuthException
     */
    public function logout(): RedirectResponse
    {
        return logout();
    }
}
