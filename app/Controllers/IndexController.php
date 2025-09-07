<?php

namespace App\Controllers;

use App\Models\AuthException;
use CodeIgniter\HTTP\RedirectResponse;
use function App\Helpers\handleAuthException;
use function App\Helpers\login;
use function App\Helpers\user;

class IndexController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        try {
            $user = user();
            if (!is_null($user)) {
                $groups = getAbsenceGroups();

                return $this->render('IndexView', ['groups' => $groups]);
            }
            return login();
        } catch (AuthException $e) {
            return handleAuthException($this, $e);
        }
    }
}
