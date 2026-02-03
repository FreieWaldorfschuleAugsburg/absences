<?php use function App\Helpers\isTestUser;
use function App\Helpers\user;

if (!empty($reportablePersons) && isTestUser(user())) : ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fas fa-school-flag"></i> Abwesenheit melden
                </div>
                <div class="card-body">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <?php $active = false; ?>
                            <?php foreach ($reportablePersons as $person) : ?>
                                <button class="nav-link <?= !$active ? 'active' : '' ?>"
                                        id="tab-<?= $person->getId() ?>"
                                        data-bs-toggle="tab"
                                        data-bs-target="#form-<?= $person->getId() ?>" type="button" role="tab"
                                        aria-controls="form-<?= $person->getId() ?>"
                                        aria-selected="true">
                                    <?= $person->getFullName() ?>
                                </button>
                                <?php $active = true; ?>
                            <?php endforeach; ?>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <?php $active = false; ?>
                        <?php foreach ($reportablePersons as $person) : ?>
                            <div class="tab-pane fade <?= !$active ? 'show active' : '' ?>"
                                 id="form-<?= $person->getId() ?>" role="tabpanel"
                                 aria-labelledby="tab-<?= $person->getId() ?>">

                                <div class="text-end">
                                    <a href="" class="btn btn-primary btn-sm mt-3">
                                        <i class="fas fa-list"></i> Abwesenheiten für <?= $person->getFirstName() ?> anzeigen
                                    </a>
                                </div>

                                <?= form_open('report') ?>
                                <?= form_hidden('person', strval($person->getId())) ?>

                                <label for="inputReason" class="form-label mt-3">Grund</label>
                                <div class="input-group mb-3">
                                    <select class="form-select" id="inputReason" name="reason" required>
                                        <?php foreach ($reasons as $reason) : ?>
                                            <option value="<?= $reason ?>">
                                                <?= $reason ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <label id="inputStart" class="form-label">Beginn der Abwesenheit</label>
                                <div class="input-group mb-3">
                                    <input class="form-control" type="date" id="inputStartDate" name="startDate"
                                           value="<?= $minDate = getMinAbsenceDateFormatted() ?>" min="<?= $minDate ?>"
                                           onchange="adjustDate(this, document.getElementById('inputEndDate'), false)"
                                           aria-describedby="inputStart" required>
                                    <select class="form-select" id="inputStartTime" name="startTime"
                                            aria-describedby="inputStart"
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
                                           value="<?= $minDate ?>" min="<?= $minDate ?>" aria-describedby="inputEnd"
                                           onchange="adjustDate(this, document.getElementById('inputStartDate'), true)"
                                           required>
                                    <select class="form-select" id="inputEndTime" name="endTime"
                                            aria-describedby="inputEnd"
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

                                <button id="submitButton" class="btn btn-primary btn-block" type="submit">
                                    Meldung absenden
                                </button>
                                <?= form_close() ?>
                            </div>
                            <?php $active = true; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function adjustDate(originObject, targetObject, reverseComparison) {
            const originDate = Date.parse(originObject.value);
            const targetDate = Date.parse(targetObject.value);

            if ((!reverseComparison && originDate > targetDate) || (reverseComparison && originDate < targetDate)) {
                targetObject.value = originObject.value;
            }
        }
    </script>
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