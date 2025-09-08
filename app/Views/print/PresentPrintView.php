<?php

use App\Models\Procurat\ProcuratPerson;
use function App\Helpers\filterAbsences;
use function App\Helpers\getCurrentUser;
use function App\Helpers\getProcuratAbsencesByGroup;
use function App\Helpers\getProcuratGroupMembers;
use function App\Helpers\getProcuratPersonGrade;
use function App\Helpers\getProcuratPersonGradeId;
use function App\Helpers\isHalfDayAbsence;

$absences = getProcuratAbsencesByGroup($group->getId());

$students = getProcuratGroupMembers($group->getId());
usort($students, function (ProcuratPerson $a, ProcuratPerson $b) {
    return
        (getProcuratPersonGradeId($a->getId()) <=> getProcuratPersonGradeId($b->getId())) * 1000 +
        ($a->getLastName() <=> $b->getLastName()) /*+
        ($a->getFirstName() <=> $b->getFirstName())*/ ;
});
?>
<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>

<h2><b><u>Anwesenheitsliste vom <?= (new DateTime())->format('d.m.Y H:i') ?> Uhr</u></b></h2>
<b>Gruppe/Klasse:</b> <?= $group->getName() ?><br>
<b>Erstellt von:</b> <?= getCurrentUser()->displayName ?>

<hr>

<small><b>Das Fälschen einer Unterschrift wird schwer geahndet!</b></small><br><br>

<table>
    <tr>
        <th style="width: 40%">
            Schüler/in
        </th>
        <th>
            Klasse
        </th>
        <th>
            Unterschrift Schüler/in
        </th>
    </tr>
    <?php foreach ($students as $student): ?>
        <?php $absence = filterAbsences($absences, $student->getId()) ?>
        <?php if ($absence && $absence->isExcused() && !isHalfDayAbsence($absence)): continue; endif; ?>
        <tr>
            <td>
                <b><?= $student->getLastName() . ', ' . $student->getFirstName() ?></b>
                <?php if ($absence): ?>
                    <br><span style="font-size: 10px">Bemerkung: <?= $absence->getNote() ?></span>
                <?php endif; ?>
            </td>
            <td>
                <?= getProcuratPersonGrade($student->getId()); ?>
            </td>
            <td>

            </td>
        </tr>
    <?php endforeach; ?>
</table>