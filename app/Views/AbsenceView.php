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

<div class="row">
    <?php foreach ($entries as $entry) : ?>
        <div class="col-lg-4">
            <div class="card <?= $entry['status']->getBackgroundColorClass() ?> mb-3">
                <div class="card-body">
                    <h5><?= $entry['person']->getFullName() ?></h5>
                    <?php if (key_exists('note', $entry)): ?>
                        <small><b><?= lang('absences.group.note') ?></b></small><small
                                onmouseenter="blurText(this, false)" onmouseleave="blurText(this, true)"
                                class="blurred"> <?= $entry['note'] ?></small>
                    <?php else: ?>
                        <small>&nbsp;</small>
                    <?php endif; ?>
                </div>
                <div class="card-footer absence-card-footer">
                    <?php if ($entry['status'] != EntryStatus::Absent && $entry['status'] != EntryStatus::Missing): ?>
                        <button class="btn btn-danger btn-sm"
                                onclick="confirmRedirect('<?= base_url('report_missing/') . $entry['person']->getId() ?>')">
                            <i class="fas fa-person-circle-xmark"></i> <?= lang('absences.group.reportMissing') ?>
                        </button>
                    <?php endif; ?>

                    <?php if ($entry['status'] == EntryStatus::Missing): ?>
                        <button class="btn btn-success btn-sm"
                                onclick="confirmRedirect('<?= base_url('revoke_missing/') . $entry['person']->getId() ?>')">
                            <i class="fas fa-person-circle-check"></i> <?= lang('absences.group.revokeMissing') ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    function confirmRedirect(url) {
        if (confirm('<?= lang('app.confirm') ?>')) {
            window.location.href = url;
        }
    }

    function blurText(element, blur) {
        if (blur) {
            element.classList.replace('visible', 'blurred');
        } else {
            element.classList.replace('blurred', 'visible');
        }
    }
</script>