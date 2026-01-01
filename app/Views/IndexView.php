<div class="row mt-3 justify-content-center">
    <div class="col-lg-12">
        <?= isset($error) ? '<div class="alert alert-danger mb-3"> <i class="fas fa-exclamation-triangle"></i> <b>' . lang('index.error') . '</b> ' . $error . '</div>' : '' ?>
        <?= !empty(session('error')) ? '<div class="alert alert-danger mb-3"> <i class="fas fa-exclamation-triangle"></i> <b>' . lang('index.error') . '</b> ' . session('error') . '</div>' : '' ?>
    </div>
</div>

<?php
$isSchoolDay = date('N') < 6;
?>

<div class="row mb-3 justify-content-center">
    <div class="col-lg-12">
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> <?= lang('absences.index.info') ?>
            </div>
            <div class="card-body text-center">
                <?php date_default_timezone_set('Europe/Berlin');
                setlocale(LC_ALL, 'de_DE.UTF-8') ?>
                <h2><?= sprintf(lang('absences.index.dateInfo'), strftime('%A'), date('d.m.Y')) ?></h2>
                <?php if ($isSchoolDay): ?>
                    <h2><?= sprintf(lang('absences.index.absentInfo'), countProcuratAbsences()) ?></h2>
                <?php else: ?>
                    <h2><?= sprintf(lang('absences.index.weekendInfo'), countProcuratAbsences()) ?></h2>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($isSchoolDay): ?>
    <div class="row">
        <?php foreach ($groups as $group) : ?>
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <h5><?= $group->getDisplayName() ?></h5>
                    </div>
                    <div class="card-footer">
                        <a class="btn btn-primary btn-sm" href="<?= base_url('view/' . $group->getName()) ?>">
                            <i class="fas fa-external-link"></i> <?= lang('absences.group.view') ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>