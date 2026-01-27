<?php if (!empty($reportablePersons)) : ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fas fa-school-flag"></i> Abwesenheit melden
                </div>
                <div class="card-body">
                    <?= form_open('report') ?>
                    <label for="inputPerson" class="form-label">Kind</label>
                    <div class="input-group mb-3">
                        <select class="form-select" id="inputPerson" name="person" required>
                            <?php foreach ($reportablePersons as $person) : ?>
                                <option value="<?= $person->getId() ?>">
                                    <?= $person->getFullName() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <label id="inputStart" class="form-label">Beginn der Abwesenheit</label>
                    <div class="input-group mb-3">
                        <input class="form-control" type="date" id="inputStartDate" name="startDate"
                               value="<?= date("Y-m-d") ?>" aria-describedby="inputStart" required>
                        <select class="form-select" id="inputStartTime" name="startTime" aria-describedby="inputStart"
                                required>
                            <option value="-1">Schulbeginn</option>
                            <?php $i = 0 ?>
                            <?php foreach ($timeslots as $timeslot) : ?>
                                <option value="<?= $i++; ?>">
                                    <?= $timeslot ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <label for="inputEnd" class="form-label">Ende der Abwesenheit</label>
                    <div class="input-group mb-3">
                        <input class="form-control" type="date" id="inputEndDate" name="endDate"
                               value="<?= date("Y-m-d") ?>" aria-describedby="inputEnd" required>
                        <select class="form-select" id="inputEndTime" name="endTime" aria-describedby="inputEnd"
                                required>
                            <option value="-1">Schulschluss</option>
                            <?php $i = 0 ?>
                            <?php foreach ($timeslots as $timeslot) : ?>
                                <option value="<?= $i++; ?>">
                                    <?= $timeslot ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <label for="inputReason" class="form-label">Grund</label>
                    <div class="input-group mb-3">
                        <select class="form-select" id="inputReason" name="reason" required>
                            <?php foreach ($reasons as $reason) : ?>
                                <option value="<?= $reason ?>">
                                    <?= $reason ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button id="submitButton" class="btn btn-primary btn-block" type="submit">
                        Meldung absenden
                    </button>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($groups)) : ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fas fa-list-check"></i> Abwesenheiten prüfen
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($groups as $group) : ?>
                            <div class="col-lg-4">
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <h5><?= $group->getDisplayName() ?></h5>
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-group">
                                            <a class="btn btn-primary btn-sm"
                                               href="<?= base_url('view/' . $group->getId()) ?>">
                                                <?= lang('absences.group.view') ?>
                                            </a>
                                            <?php foreach ($group->getSubGroups() as $subGroup) : ?>
                                                <a class="btn btn-primary btn-sm"
                                                   href="<?= base_url('view/' . $subGroup->getId()) ?>">
                                                    <?= $subGroup->getDisplayName() ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>