<?php

namespace App\Helpers;

use App\Controllers\BaseController;
use App\Models\OAuthException;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use Exception;
use Jumbojett\OpenIDConnectClient;
use Jumbojett\OpenIDConnectClientException;

/**
 * @throws OAuthException
 */
function isLoggedIn(): bool
{
    return !is_null(user());
}

/**
 * @throws OAuthException
 */
function login(): RedirectResponse
{
    $oidc = createOIDC();

    try {
        $oidc->authenticate();

        $username = $oidc->requestUserInfo('preferred_username');
        $name = $oidc->requestUserInfo('name');
        $procuratId = $oidc->requestUserInfo('procurat_id');
        if ($procuratId) {
            $procuratId = intval($procuratId);
        }
        $claims = $oidc->getVerifiedClaims();
        $groups = property_exists($claims, 'groups') ? $oidc->getVerifiedClaims()->groups : [];

        $userModel = createUserModel($username, $name, $procuratId, $oidc->getIdToken(), $oidc->getRefreshToken(), $groups);
        session()->set('USER', $userModel);

        log_message('info', sprintf("User logged in (username=%s)", $username));

        return redirect('/');
    } catch (OpenIDConnectClientException $e) {
        throw new OAuthException('login', $e);
    }
}

/**
 * @throws OAuthException
 */
function logout(): RedirectResponse
{
    $oidc = createOIDC();

    try {
        $user = user();
        session()->remove('USER');
        $oidc->signOut($user->getIdToken(), null);

        log_message('info', sprintf("User logged out (username=%s)", $user->getUsername()));
    } catch (OpenIDConnectClientException $e) {
        throw new OAuthException('logout', $e);
    }

    return redirect('/');
}

/**
 * @throws OAuthException
 */
function user(): ?UserModel
{
    $oidc = createOIDC();
    $user = session('USER');
    if (!$user) {
        return null;
    }

    $refreshToken = $user->getRefreshToken();

    try {
        $response = $oidc->introspectToken($refreshToken, 'refresh_token', $oidc->getClientID(), $oidc->getClientSecret());
        if (!$response->active)
            return null;

        // TODO update user

        return $user;
    } catch (Exception $e) {
        throw new OAuthException('refresh', $e);
    }
}

function createUserModel(string $username, string $displayName, ?int $procuratId, string $idToken, string $refreshToken, array $groups): UserModel
{
    return new UserModel($username, $displayName, $procuratId, $idToken, $refreshToken, $groups);
}

/**
 * @return OpenIDConnectClient
 */
function createOIDC(): OpenIDConnectClient
{
    $oidc = new OpenIDConnectClient(
        getenv('oidc.endpoint'),
        getenv('oidc.clientId'),
        getenv('oidc.clientSecret')
    );

    if (getenv('CI_ENVIRONMENT') == 'development') {
        $oidc->setVerifyHost(false);
        $oidc->setVerifyPeer(false);
    }

    return $oidc;
}