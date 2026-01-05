<?php

namespace App\Models;

class AbsenceGroupModel
{
    private string $id;
    private string $displayName;
    private string $title;
    private array $groupIds;
    private array $udfFilters;
    private array $subGroups;

    function __construct($id, $displayName, $title, $groupIds, $udfFilters, $subGroups)
    {
        $this->id = $id;
        $this->displayName = $displayName;
        $this->title = $title;
        $this->groupIds = $groupIds;
        $this->udfFilters = $udfFilters;
        $this->subGroups = $subGroups;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getGroupIds(): array
    {
        return $this->groupIds;
    }

    public function getUdfFilters(): array
    {
        return $this->udfFilters;
    }

    public function getSubGroups(): array
    {
        $subGroups = [];
        foreach ($this->subGroups as $subGroup) {
            $subGroups[] = getAbsenceGroup($subGroup);
        }

        return $subGroups;
    }
}