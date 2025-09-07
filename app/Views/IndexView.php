<div class="row mt-3 justify-content-center">
    <div class="col-lg-12">
        <?= isset($error) ? '<div class="alert alert-danger mb-3"> <i class="fas fa-exclamation-triangle"></i> <b>' . lang('index.error') . '</b> ' . $error . '</div>' : '' ?>
        <?= !empty(session('error')) ? '<div class="alert alert-danger mb-3"> <i class="fas fa-exclamation-triangle"></i> <b>' . lang('index.error') . '</b> ' . session('error') . '</div>' : '' ?>
    </div>

</div>

<div class="row">
    <?php foreach ($groups as $group) : ?>
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <h5><?= $group->getDisplayName() ?></h5>
                </div>
                <div class="card-footer">
                    <a class="btn btn-primary btn-sm" href="<?= base_url('view/' . $group->getName()) ?>">
                        <i class="fas fa-external-link"></i> <?= lang('absences.group.view')?>
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
