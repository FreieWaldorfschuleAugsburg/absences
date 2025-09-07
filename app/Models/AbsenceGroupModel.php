<?php

namespace App\Models;

class AbsenceGroupModel
{
    private string $name;
    private string $displayName;
    private array $groupIds;
    private array $udfFilters;

    function __construct($name, $displayName, $groupIds, $udfFilters)
    {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->groupIds = $groupIds;
        $this->udfFilters = $udfFilters;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getGroupIds(): array
    {
        return $this->groupIds;
    }

    public function getUdfFilters(): array
    {
        return $this->udfFilters;
    }
}