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
        $claims = $oidc->getVerifiedClaims();
        $groups = property_exists($claims, 'groups') ? $oidc->getVerifiedClaims()->groups : [];

        $userModel = createUserModel($username, $name, $oidc->getIdToken(), $oidc->getRefreshToken(), $groups);
        session()->set('USER', $userModel);

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

function createUserModel(string $username, string $displayName, string $idToken, string $refreshToken, array $groups): UserModel
{
    return new UserModel($username, $displayName, $idToken, $refreshToken, $groups);
}

/**
 * @return OpenIDConnectClient
 */
function createOIDC(): OpenIDConnectClient
{
    return new OpenIDConnectClient(
        getenv('oidc.endpoint'),
        getenv('oidc.clientId'),
        getenv('oidc.clientSecret')
    );
}

function isPermitted(UserModel $user): bool
{
    return in_array(getenv('oidc.group'), $user->getGroups());
}
