<style>
    @media (prefers-color-scheme: dark) {
        .bg-green {
            background-color: green;
        }

        .bg-orange {
            background-color: #FF5C00;
        }

        .bg-red {
            background-color: darkred;
        }
    }

    @media (prefers-color-scheme: light) {
        .bg-green {
            background-color: #88e788;
        }

        .bg-orange {
            background-color: #ffc067;
        }

        .bg-red {
            background-color: #ff746c;
        }
    }
</style>

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
        <?php if (key_exists('absent', $entry)): ?>
            <div class="col-lg-4">
                <div class="card <?= $entry['halfDay'] ? 'bg-orange' : 'bg-red' ?> mb-3">
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
                    <div class="card-footer">
                        <?php if ($entry['halfDay']): ?>
                            <button class="btn btn-primary btn-sm"
                                    onclick="confirmRedirect('<?= base_url('absent/') . '?id=' . $entry['person']->getId() ?>')">
                                <i class="fas fa-person-running"></i> <?= lang('absences.group.reportAbsent') ?>
                            </button>
                        <?php else: ?>
                            <button class="btn btn-primary btn-sm" disabled>
                                <i class="fas fa-person-running"></i> <?= lang('absences.group.reportAbsent') ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-lg-4">
                <div class="card <?= key_exists('followUp', $entry) ? 'bg-orange' : 'bg-green' ?> mb-3">
                    <div class="card-body">
                        <h5><?= $entry['person']->getFullName() ?></h5>
                        <?php if (key_exists('followUp', $entry)): ?>
                            <small><b><?= lang('absences.group.note') ?></b></small>
                            <small onmouseenter="blurText(this, false)" onmouseleave="blurText(this, true)"
                                   class="blurred"> <?= $entry['note'] ?></small>
                        <?php else: ?>
                            <small>&nbsp;</small>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary btn-sm"
                                onclick="confirmRedirect('<?= base_url('absent/') . $entry['person']->getId() ?>')">
                            <i class="fas fa-person-running"></i> <?= lang('absences.group.reportAbsent') ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
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