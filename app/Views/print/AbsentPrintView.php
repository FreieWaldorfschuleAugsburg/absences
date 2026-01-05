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

<h2><b><u>Abwesenheitsliste vom <?= (new DateTime())->format('d.m.Y H:i') ?> Uhr</u></b></h2>
<b>Gruppe/Klasse:</b> <?= $group->getTitle() ?><br>
<b>Erstellt von:</b> <?= $user->getDisplayName() ?><br>

<hr>

<table>
    <tr>
        <th style="width: 40%">
            Schüler/in
        </th>
        <th>
            Klasse
        </th>
        <th>
            Bemerkung
        </th>
    </tr>
    <?php foreach ($entries as $entry): ?>
        <tr>
            <td>
                <?= $entry['person']->getLastName() . ', ' . $entry['person']->getFirstName() ?>
            </td>
            <td>
            </td>
            <td>
                <?= $entry['note'] ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>