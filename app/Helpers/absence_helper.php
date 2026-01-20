<?php

use App\Models\ProcuratFollowup;

function isFollowUpToday(ProcuratFollowUp $followUp): bool
{
    $dueDate = $followUp->getDueDate();
    return $dueDate['day'] == date('d') && $dueDate['month'] == date('m') && $dueDate['year'] == date('Y');
}