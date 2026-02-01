<?php

return [
    'index' => [
        'minDateUndercut' => 'Absence reports are due %s o\' clock on the first day of absence.',
        'endBeforeStartDate' => 'The end date mustn\'t be before the start date.',
        'endBeforeStartTime' => 'The end time mustn\'t be before the start time.',
        'maxDaysExceeded' => 'Absences reports mustn\'t be longer than %s days.',
        'invalidPerson' => 'Invalid person',
        'alreadyAbsent' => 'There\'s at least one absence already reported for the selected time period. Please adjust the absence period!',
        'reportSuccessful' => 'Thank you! We\'ve successfully processed you\'r absence report!',
    ],
    'group' => [
        'view' => 'All',
        'back' => 'Back',
        'note' => 'Note: ',
        'printAbsent' => 'Print absent',
        'printPresent' => 'Print present',
        'reportMissing' => 'Report missing',
        'revokeMissing' => 'Withdraw report',
        'deviationNotice' => 'Please note that absences are usually not fully processed by the office until 8:30 a.m. Therefore, changes may still occur!',
        'officeHoursNotice' => 'Please note that the office will be closed from 1:30 p.m. onwards. Reports of unexcused absences will not be processed by the office until the following day. Please contact the parents yourself!'
    ],
    'error' => [
        'invalidPerson' => 'Invalid person',
        'invalidGroup' => 'Invalid absence group',
        'alreadyAbsent' => 'Person already reported absent',
        'noFollowUp' => 'Person no longer reported missing'
    ]
];