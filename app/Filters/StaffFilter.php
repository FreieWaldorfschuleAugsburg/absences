<?php

namespace App\Filters;

use App\Models\OAuthException;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use function App\Helpers\login;
use function App\Helpers\user;

class StaffFilter implements FilterInterface
{
    /**
     * @throws OAuthException
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('oauth');

        $user = user();
        if (is_null($user)) {
            return login();
        }

        if (!$user->isStaff()) {
            return redirect('/')->with('error', lang('app.error.oauth.noPermissions'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}