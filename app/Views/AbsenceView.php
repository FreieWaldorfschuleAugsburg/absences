<?php

use App\Models\EntryStatus;

?>
<div class="row mt-3 justify-content-center">
    <div class="col-lg-12">
        <?= isset($error) ? '<div class="alert alert-danger mb-3"> <i class="fas fa-exclamation-triangle"></i> <b>' . lang('index.error') . '</b> ' . $error . '</div>' : '' ?>
        <?= !empty(session('error')) ? '<div class="alert alert-danger mb-3"> <i class="fas fa-exclamation-triangle"></i> <b>' . lang('index.error') . '</b> ' . session('error') . '</div>' : '' ?>

        <?php if (idate('H') < 9): ?>
            <div class="alert alert-warning mb-3">
                <?= lang('absences.group.deviationNotice') ?>
            </div>
        <?php endif; ?>
        <?php if (idate('H') >= 13): ?>
            <div class="alert alert-warning mb-3">
                <?= lang('absences.group.officeHoursNotice') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row mb-3">
    <div class="col-lg-4">
        <div class="btn-group" role="group">
            <a href="/" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left"></i> <?= lang('absences.group.back') ?>
            </a>
            <a class="btn btn-primary btn-sm" href="<?= base_url('print_absent/' . $group->getId()) ?>">
                <i class="fas fa-print"></i> <?= lang('absences.group.printAbsent') ?>
            </a>
            <a class="btn btn-primary btn-sm" href="<?= base_url('print_present/' . $group->getId()) ?>">
                <i class="fas fa-print"></i> <?= lang('absences.group.printPresent') ?>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <h1>
            <?= $group->getTitle() ?>
        </h1>
        <hr>
    </div>
</div>

<div id="entryRow" class="row">
    <div class="text-center">
        <h1><i class="fas fa-spinner fa-2xl fa-spin mt-5 mb-5"></i></h1>
        <h2>Einträge werden geladen ...</h2>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        updateEntries();

        setInterval(() => updateEntries(), 10000)
    });

    function updateEntries() {
        axios.get('<?= base_url('api/entries') ?>/<?= $group->getId() ?>')
            .then(function (response) {
                let innerHTML = "";
                for (const entry of response.data) {
                    innerHTML += '<div class="col-lg-4">' +
                        '<div class="card ' + entry.status.color + ' mb-3">' +
                        '<div class="card-body">' +
                        '<h5>' + entry.person.fullName + '</h5>'

                    if (Object.hasOwn(entry, 'note')) {
                        innerHTML += '<small><b><?= lang('absences.group.note') ?></b></small>' +
                            '<small onmouseenter="blurText(this, false)" onmouseleave="blurText(this, true)" class="blurred">' + entry.note + '</small>'
                    } else {
                        innerHTML += '<small>&nbsp;</small>'
                    }

                    innerHTML += '</div><div class="card-footer absence-card-footer">';

                    if (entry.status.name !== 'Absent' && entry.status.name !== 'Missing') {
                        innerHTML += '<button class="btn btn-danger btn-sm" onclick="reportMissing(this, \'' + entry.person.id + '\')">' +
                            '<i class="fas fa-person-circle-xmark"></i> <?= lang('absences.group.reportMissing') ?>' +
                            '</button>';
                    }

                    if (entry.status.name === 'Missing') {
                        innerHTML += '<button class="btn btn-success btn-sm" onclick="revokeMissing(this, \'' + entry.person.id + '\')">' +
                            '<i class="fas fa-person-circle-check"></i> <?= lang('absences.group.revokeMissing') ?>' +
                            '</button>';
                    }

                    innerHTML += '</div></div></div>';
                }

                const row = document.getElementById('entryRow');
                row.innerHTML = innerHTML;
            });
    }

    function reportMissing(object, personId) {
        object.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'
        axios.get('<?= base_url('report_missing') ?>/' + personId)
            .then(function () {
                setTimeout(() => updateEntries(), 500);
            });
    }

    function revokeMissing(object, personId) {
        object.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'
        axios.get('<?= base_url('revoke_missing') ?>/' + personId)
            .then(function () {
                setTimeout(() => updateEntries(), 500);
            });
    }

    function blurText(element, blur) {
        if (blur) {
            element.classList.replace('visible', 'blurred');
        } else {
            element.classList.replace('blurred', 'visible');
        }
    }
</script>