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

    $groupNames = explode(',', getenv('absences.groups'));
    foreach ($groupNames as $groupName) {
        $displayName = getenv('absences.group.' . $groupName . '.displayName');
        $groupIds = explode(',', getenv('absences.group.' . $groupName . '.groupIds'));
        $udfFilterStrings = explode(',', getenv('absences.group.' . $groupName . '.udfs'));
        $udfFilter = [];
        foreach ($udfFilterStrings as $udfFilterString) {
            if (!str_contains($udfFilterString, '='))
                continue;

            $key = explode('=', $udfFilterString)[0];
            $value = explode('=', $udfFilterString)[1];

            $udfFilter[$key] = $value;
        }

        $groups[] = new AbsenceGroupModel($groupName, $displayName, $groupIds, $udfFilter);
    }

    return $groups;
}

/**
 * @param string $name
 * @return ?AbsenceGroupModel
 */
function findAbsenceGroupByName(string $name): ?AbsenceGroupModel
{
    $groups = getAbsenceGroups();
    foreach ($groups as $group) {
        if ($group->getName() == $name) {
            return $group;
        }
    }

    return null;
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