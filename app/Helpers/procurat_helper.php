<?php

use App\Models\ProcuratAbsence;
use App\Models\ProcuratFollowup;
use App\Models\ProcuratGroupMembership;
use App\Models\ProcuratPerson;
use App\Models\ProcuratRelationship;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;

/**
 * @return Client
 */
function createAPIClient(): Client
{
    $stack = HandlerStack::create();

    return new Client([
        'handler' => $stack,
        'base_uri' => getenv('procurat.endpoint'),
        'headers' => [
            'X-API-KEY' => getenv('procurat.apiKey'),
            'Accept' => 'application/json'
        ],
        'curl' => [
            CURLOPT_SSL_OPTIONS => CURLSSLOPT_NATIVE_CA,
        ]
    ]);
}

/**
 * @param int $groupId
 * @return ProcuratGroupMembership[]
 */
function getGroupMembershipsByGroupId(int $groupId): array
{
    $client = createAPIClient();
    $memberships = [];
    try {
        $rawMemberships = decodeResponse($client->get('groups/' . $groupId . '/members'));
        foreach ($rawMemberships as $rawMembership) {
            $memberships[] = constructProcuratGroupMembership($rawMembership);
        }
    } catch (GuzzleException $e) {
        log_message('error', "Error getting group memberships for group {$groupId} {exception}", ['exception' => $e]);
    }
    return $memberships;
}

/**
 * @param int $id
 * @return ?ProcuratPerson
 */
function getProcuratPerson(int $id): ?ProcuratPerson
{
    $client = createAPIClient();
    try {
        $response = decodeResponse($client->get('persons/' . $id));
        return constructProcuratPerson($response);
    } catch (GuzzleException $e) {
        log_message('error', "Error getting person {$id} {exception}", ['exception' => $e]);
        return null;
    }
}

/**
 * @return ProcuratPerson[]
 */
function getAllProcuratPersons(): array
{
    $client = createAPIClient();
    $persons = [];
    try {
        $rawPersons = decodeResponse($client->get('persons'));
        foreach ($rawPersons as $rawPerson) {
            $persons[] = constructProcuratPerson($rawPerson);
        }
    } catch (GuzzleException $e) {
        log_message('error', "Error getting all persons {exception}", ['exception' => $e]);

    }
    return $persons;
}

/**
 * @param ProcuratPerson[] $allPersons
 * @param int $personId
 * @return ?ProcuratPerson
 */
function findProcuratPerson(array $allPersons, int $personId): ?ProcuratPerson
{
    foreach ($allPersons as $person) {
        if ($person->getId() == $personId) {
            return $person;
        }
    }
    return null;
}

/**
 * @return ProcuratAbsence[]
 */
function getProcuratAbsences(): array
{
    $client = createAPIClient();

    $absences = [];
    try {
        $rawAbsences = decodeResponse($client->get('absences?type=today'));
        foreach ($rawAbsences as $rawAbsence) {
            $absences[] = constructProcuratAbsence($rawAbsence);
        }
    } catch (GuzzleException $e) {
        log_message('error', "Error getting absences {exception}", ['exception' => $e]);
    }
    return $absences;
}

/**
 * @param int $personId
 * @return bool
 */
function isAbsentToday(int $personId): bool
{
    return !is_null(getAbsenceToday($personId));
}

/**
 * @param int $personId
 * @return ProcuratAbsence|null
 */
function getAbsenceToday(int $personId): ?ProcuratAbsence
{
    $client = createAPIClient();

    try {
        $rawAbsences = decodeResponse($client->get('absences/person/' . $personId . '?type=today'));
        if (!empty($rawAbsences)) {
            return constructProcuratAbsence($rawAbsences[0]);
        }
    } catch (GuzzleException $e) {
        log_message('error', "Error getting today's absence for person {$personId} {exception}", ['exception' => $e]);
    }
    return null;
}

/**
 * @param int $personId
 * @return ProcuratAbsence[]
 */
function getSchoolYearAbsencesByPersonId(int $personId): array
{
    $absences = [];
    $client = createAPIClient();

    try {
        $rawAbsences = decodeResponse($client->get('absences/person/' . $personId . '?type=schoolyear'));
        foreach ($rawAbsences as $rawAbsence) {
            $absences[] = constructProcuratAbsence($rawAbsence);
        }
    } catch (GuzzleException $e) {
        log_message('error', "Error absences for person {$personId} {exception}", ['exception' => $e]);
    }
    return $absences;
}

/**
 * @param int $personId
 * @return ProcuratAbsence[]
 */
function getAbsencesByPersonId(int $personId): array
{
    $absences = [];
    $client = createAPIClient();

    try {
        $rawAbsences = decodeResponse($client->get('absences/person/' . $personId));
        foreach ($rawAbsences as $rawAbsence) {
            $absences[] = constructProcuratAbsence($rawAbsence);
        }
    } catch (GuzzleException $e) {
        log_message('error', "Error absences for person {$personId} {exception}", ['exception' => $e]);
    }
    return $absences;
}

/**
 * @param ProcuratAbsence[] $absences
 * @param DateTimeInterface $date
 * @return ProcuratAbsence|null
 */
function findAbsence(array $absences, DateTimeInterface $date): ?ProcuratAbsence
{
    foreach ($absences as $absence) {
        if ($absence->getDate()->format('Y-m-d') == $date->format('Y-m-d')) {
            return $absence;
        }
    }
    return null;
}

function createProcuratAbsence(int $personId, DateTimeInterface $date, string $reason): void
{
    $formattedDate = $date->format('Y-m-d\TH:i:sp');
    $client = createAPIClient();
    try {
        $client->post('absences', [
            'json' => [
                'personId' => $personId,
                'startDate' => $formattedDate,
                'endDate' => $formattedDate,
                'excused' => true,
                'parentsInformed' => true,
                'note' => $reason
            ]
        ]);

        log_message('info', sprintf('Created absence for %s on %s with reason %s', $personId, $formattedDate, $reason));
    } catch (GuzzleException $e) {
        log_message('error', "Error creating absence {exception}", ['exception' => $e]);
    }
}

function deleteProcuratAbsence(int $absenceId): void
{
    $client = createAPIClient();
    try {
        $client->delete('absences/' . $absenceId);
        log_message('info', sprintf('Deleted absence %s', $absenceId));
    } catch (GuzzleException $e) {
        log_message('error', "Error deleting absence {exception}", ['exception' => $e]);
    }
}

/**
 * @return ProcuratFollowup[]
 */
function getProcuratFollowUps(): array
{
    $client = createAPIClient();

    $followUps = [];
    try {
        $rawFollowUps = decodeResponse($client->get('followups'));
        foreach ($rawFollowUps as $rawFollowUp) {
            $followUps[] = constructProcuratFollowUp($rawFollowUp);
        }
    } catch (GuzzleException $e) {
        log_message('error', "Error getting follow-ups {exception}", ['exception' => $e]);
    }

    // Sort descending by id
    usort($followUps, function ($a, $b) {
        return $b->getId() <=> $a->getId();
    });

    return $followUps;
}

/**
 * @param int $assignedGroupId
 * @param int $referencedPersonId
 * @param string $dueDate
 * @param string $subject
 * @param string $message
 * @return void
 */
function createProcuratFollowUp(int $assignedGroupId, int $referencedPersonId, string $dueDate, string $subject, string $message): void
{
    $client = createAPIClient();
    try {
        $client->post('followups', [
            'json' => [
                'assignedGroupId' => $assignedGroupId,
                'referencedPersonId' => $referencedPersonId,
                'dueDate' => $dueDate,
                'subject' => $subject,
                'message' => $message
            ]
        ]);

        log_message('info', sprintf('Created follow-up (assignedGroupId=%s,referencedPersonId=%s,dueDate=%s,subject=%s,message=%s)',
            $assignedGroupId, $referencedPersonId, $dueDate, $subject, $message));
    } catch (GuzzleException $e) {
        log_message('error', "Error creating follow-up {exception}", ['exception' => $e]);
    }
}

/**
 * @param int $followUpId
 * @return void
 */
function completeProcuratFollowUp(int $followUpId): void
{
    $client = createAPIClient();
    try {
        $client->put('followups/' . $followUpId, [
            'json' => [
                'completed' => true
            ]
        ]);

        log_message('info', sprintf('Completed follow-up (id=%s)', $followUpId));
    } catch (GuzzleException $e) {
        log_message('error', "Error completing follow-up {exception}", ['exception' => $e]);
    }
}

function deleteProcuratFollowUp(int $followUpId): void
{
    $client = createAPIClient();
    try {
        $client->delete('followups/' . $followUpId);

        log_message('info', sprintf('Deleted follow-up (id=%s)', $followUpId));
    } catch (GuzzleException $e) {
        log_message('error', "Error deleting follow-up {exception}", ['exception' => $e]);
    }
}

/**
 * @param int $personId
 * @return ProcuratRelationship[]
 */
function getProcuratRelationships(int $personId): array
{
    $client = createAPIClient();
    $relationships = [];
    try {
        $rawRelationships = decodeResponse($client->get('relationships/person/' . $personId));
        foreach ($rawRelationships as $raw) {
            $relationships[] = constructProcuratRelationship($raw);
        }
    } catch (GuzzleException $e) {
        log_message('error', "Error getting relationships for person {$personId} {exception}", ['exception' => $e]);
    }
    return $relationships;
}

/**
 * @param int $childPersonId
 * @param int $parentPersonId
 * @return bool
 */
function isProcuratChildCustodyRelationship(int $parentPersonId, int $childPersonId): bool
{
    if ($parentPersonId == $childPersonId) {
        return true;
    }

    $relationships = getProcuratRelationships($parentPersonId);
    foreach ($relationships as $relationship) {
        if ($relationship->getPersonId() == $childPersonId && $relationship->isCustody() && ($relationship->getRelationshipType() == "son" || $relationship->getRelationshipType() == "daughter")) {
            return true;
        }
    }

    return false;
}

/**
 * @param object $raw
 * @return ProcuratPerson
 */
function constructProcuratPerson(object $raw): ProcuratPerson
{
    return new ProcuratPerson($raw->id, $raw->firstName, $raw->lastName, $raw->birthDate, $raw->familyRole);
}

/**
 * @param object $raw
 * @return ProcuratGroupMembership
 */
function constructProcuratGroupMembership(object $raw): ProcuratGroupMembership
{
    return new ProcuratGroupMembership($raw->id, $raw->groupId, $raw->personId, $raw->jsonData);
}

/**
 * @param object $raw
 * @return ProcuratAbsence
 */
function constructProcuratAbsence(object $raw): ProcuratAbsence
{
    return new ProcuratAbsence($raw->id, $raw->personId, $raw->date, $raw->excused, $raw->note);
}

/**
 * @param object $raw
 * @return ProcuratFollowup
 */
function constructProcuratFollowUp(object $raw): ProcuratFollowup
{
    return new ProcuratFollowup($raw->id, $raw->dueDate, $raw->assignedGroupId, $raw->subject, $raw->message, $raw->referencedPersonId, $raw->completed);
}

/**
 * @param object $raw
 * @return ProcuratRelationship
 */
function constructProcuratRelationship(object $raw): ProcuratRelationship
{
    return new ProcuratRelationship($raw->personId, $raw->relationshipType, $raw->custody, $raw->physical, $raw->realParent, $raw->notes);
}

/**
 * @param ResponseInterface $response
 * @return mixed
 */
function decodeResponse(ResponseInterface $response): mixed
{
    return json_decode($response->getBody());
}