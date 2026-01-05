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
<b>Gruppe/Klasse:</b> <?= $group->getTitle() ?><br>
<b>Erstellt von:</b> <?= $user->getDisplayName() ?><br>

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
    <?php foreach ($entries as $entry): ?>
        <tr>
            <td>
                <b><?= $entry['person']->getLastName() . ', ' . $entry['person']->getFirstName() ?></b>
                <?php if (key_exists('absent', $entry)): ?>
                    <br><span style="font-size: 10px">Bemerkung: <?= $entry['note'] ?></span>
                <?php endif; ?>
            </td>
            <td>

            </td>
            <td>

            </td>
        </tr>
    <?php endforeach; ?>
</table>