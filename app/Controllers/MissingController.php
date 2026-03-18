<?php

namespace App\Controllers;

use App\Models\AlreadyAbsentException;
use App\Models\InvalidPersonException;
use App\Models\OAuthException;
use CodeIgniter\HTTP\RedirectResponse;
use PHPMailer\PHPMailer\Exception;
use function App\Helpers\user;

class MissingController extends BaseController
{
    /**
     * @throws OAuthException
     */
    public function apiReportMissing(int $personId): string
    {
        try {
            reportMissing($personId, user()->getDisplayName());
            return "";
        } catch (AlreadyAbsentException) {
            $this->response->setStatusCode(400);
            return lang('absences.error.alreadyAbsent');
        } catch (InvalidPersonException) {
            $this->response->setStatusCode(400);
            return lang('absences.error.invalidPerson');
        }
    }

    public function apiRevokeMissing(int $personId): string
    {
        try {
            revokeMissing($personId);
            return "";
        } catch (InvalidPersonException) {
            $this->response->setStatusCode(400);
            return lang('absences.error.invalidPerson');
        }
    }

    /**
     * @throws Exception
     */
    public function cronFollowUpReminder(): void
    {
        sendUncompletedFollowUpReminder();
    }
}
