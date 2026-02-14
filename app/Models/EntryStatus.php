<?php

namespace App\Models;

use JsonSerializable;

enum EntryStatus implements JsonSerializable
{
    case Present;
    case Absent;
    case HalfDay;
    case Missing;

    public function getBackgroundColorClass(): string
    {
        return match ($this) {
            EntryStatus::Present => 'bg-green',
            EntryStatus::Absent => 'bg-red',
            EntryStatus::HalfDay, EntryStatus::Missing => 'bg-orange'
        };
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'color' => $this->getBackgroundColorClass()
        ];
    }
}