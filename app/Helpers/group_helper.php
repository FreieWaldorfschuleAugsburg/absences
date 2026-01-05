<?php

use App\Models\AbsenceGroupModel;
use App\Models\ProcuratAbsence;
use App\Models\ProcuratPerson;

/**
 * @return AbsenceGroupModel[]
 */
function getAbsenceGroups(): array
{
    $groups = [];
    $ids = explode(',', getenv('absences.groups'));
    foreach ($ids as $id) {
        $groups[] = getAbsenceGroup($id);
    }

    return $groups;
}

function getAbsenceGroup(string $id): ?AbsenceGroupModel
{
    $displayName = getenv('absences.group.' . $id . '.displayName');
    if (!$displayName) {
        return null;
    }

    $title = getenv('absences.group.' . $id . '.title');
    $groupIds = preg_split('~,~', getenv('absences.group.' . $id . '.groupIds'), -1, PREG_SPLIT_NO_EMPTY);
    $udfFilterStrings = preg_split('~,~', getenv('absences.group.' . $id . '.udfs'), -1, PREG_SPLIT_NO_EMPTY);
    $udfFilter = [];
    foreach ($udfFilterStrings as $udfFilterString) {
        if (!str_contains($udfFilterString, '='))
            continue;

        $key = explode('=', $udfFilterString)[0];
        $value = explode('=', $udfFilterString)[1];

        $udfFilter[$key] = $value;
    }

    $subGroups = preg_split('~,~', getenv('absences.group.' . $id . '.subGroups'), -1, PREG_SPLIT_NO_EMPTY);
    return new AbsenceGroupModel($id, $displayName, $title, $groupIds, $udfFilter, $subGroups);
}

/**
 * @param AbsenceGroupModel $group
 * @return ProcuratPerson[]
 */
function findAbsenceGroupMembers(AbsenceGroupModel $group): array
{
    $allPersons = getAllProcuratPersons();
    $persons = [];

    foreach ($group->getGroupIds() as $groupId) {
        $memberships = getGroupMembershipsByGroupId(intval($groupId));

        foreach ($memberships as $membership) {
            $match = true;
            foreach ($group->getUdfFilters() as $key => $value) {
                if (!property_exists($membership->getData(), $key) || $membership->getData()->{$key} != $value) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                $persons[] = findProcuratPerson($allPersons, $membership->getPersonId());
            }
        }
    }

    usort($persons, function ($a, $b) {
        return strcmp($a->getLastName(), $b->getLastName());
    });

    return $persons;
}

function isHalfDayAbsence(ProcuratAbsence $absence): bool
{
    $lowercaseKeywords = mb_strtolower(getenv('absences.halfDayKeywords'));
    $lowercaseNote = mb_strtolower($absence->getNote());
    $keywords = explode(',', $lowercaseKeywords);

    foreach ($keywords as $keyword) {
        if (str_contains($lowercaseNote, $keyword)) {
            return true;
        }
    }

    return false;
}