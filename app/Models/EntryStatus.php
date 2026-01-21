<?php

namespace App\Models;

enum EntryStatus
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
}