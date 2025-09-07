<?php

use App\Models\ProcuratAbsence;
use App\Models\ProcuratFollowup;
use App\Models\ProcuratGroupMembership;
use App\Models\ProcuratPerson;
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
    } catch (GuzzleException) {
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
        return new ProcuratPerson($response->id, $response->firstName, $response->lastName);
    } catch (GuzzleException) {
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
            $persons[] = new ProcuratPerson($rawPerson->id, $rawPerson->firstName, $rawPerson->lastName);
        }
    } catch (GuzzleException) {
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
    }
    return $absences;
}

/**
 * @param int $personId
 * @return bool
 */
function isAbsentToday(int $personId): bool
{
    $client = createAPIClient();

    try {
        $rawAbsences = decodeResponse($client->get('absences/person/' . $personId . '?type=today'));
        return !empty($rawAbsences);
    } catch (GuzzleException $e) {
    }
    return false;
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
    }

    // Sort descending by id
    usort($followUps, function ($a, $b) {
        return $b->getId() <=> $a->getId();
    });

    return $followUps;
}

/**
 * @param int $assignedPersonId
 * @param int $referencedPersonId
 * @param string $dueDate
 * @param string $subject
 * @param string $message
 * @return void
 */
function createProcuratFollowUp(int $assignedPersonId, int $referencedPersonId, string $dueDate, string $subject, string $message): void
{
    $client = createAPIClient();
    try {
        $client->post('followups', [
            'json' => [
                'assignedPersonId' => $assignedPersonId,
                'referencedPersonId' => $referencedPersonId,
                'dueDate' => $dueDate,
                'subject' => $subject,
                'message' => $message
            ]
        ]);
    } catch (GuzzleException) {
    }
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
    return new ProcuratAbsence($raw->id, $raw->personId, $raw->excused, $raw->note);
}

/**
 * @param object $raw
 * @return ProcuratFollowup
 */
function constructProcuratFollowUp(object $raw): ProcuratFollowup
{
    return new ProcuratFollowup($raw->id, $raw->dueDate, $raw->assignedPersonId, $raw->subject, $raw->message, $raw->referencedPersonId, $raw->completed);
}

/**
 * @param ResponseInterface $response
 * @return mixed
 */
function decodeResponse(ResponseInterface $response): mixed
{
    return json_decode($response->getBody());
}