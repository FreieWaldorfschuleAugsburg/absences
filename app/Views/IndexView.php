<?php if (!empty($reportablePersons)) : ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fas fa-school-flag"></i> <?= lang('absences.reportAbsence') ?>
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

                                <div id="calendar-div-<?= $person->getId() ?>" style="display: none">
                                    <button class="btn btn-primary btn-sm mt-3"
                                            onclick="switchToReporting('<?= $person->getId() ?>')">
                                        <i class="fas fa-person-circle-plus"></i> <?= lang('absences.reportAbsence') ?>
                                    </button>

                                    <div class="mt-3" id="calendar-<?= $person->getId() ?>"></div>
                                </div>

                                <div id="report-div-<?= $person->getId() ?>">
                                    <button class="btn btn-primary btn-sm mt-3"
                                            onclick="switchToCalendar('<?= $person->getId() ?>')">
                                        <i class="fas fa-calendar"></i> <?= lang('absences.showAbsences') ?>
                                    </button>

                                    <?= form_open('report') ?>
                                    <?= form_hidden('person', strval($person->getId())) ?>

                                    <label for="inputReason"
                                           class="form-label mt-3"><?= lang('absences.reason') ?></label>
                                    <div class="input-group mb-3">
                                        <select class="form-select" id="inputReason" name="reason" required>
                                            <option value="" selected disabled><?= lang('absences.pleaseSelect') ?></option>
                                            <?php foreach ($reasons as $reason) : ?>
                                                <option value="<?= $reason ?>">
                                                    <?= $reason ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <label id="inputStart" class="form-label"><?= lang('absences.start') ?></label>
                                    <div class="input-group mb-3">
                                        <input class="form-control" type="date" id="inputStartDate" name="startDate"
                                               value="<?= $minDate = getMinAbsenceDateFormatted() ?>"
                                               min="<?= $minDate ?>"
                                               onchange="adjustDate(this, document.getElementById('inputEndDate'), false)"
                                               aria-describedby="inputStart" required>
                                        <select class="form-select" id="inputStartTime" name="startTime"
                                                aria-describedby="inputStart"
                                                required>
                                            <option value="" selected disabled><?= lang('absences.pleaseSelect') ?></option>
                                            <option value="-1"><?= lang('absences.schoolDayStart') ?></option>
                                            <?php $i = 0 ?>
                                            <?php foreach ($timeslots as $timeslot) : ?>
                                                <option value="<?= $i++; ?>">
                                                    <?= $timeslot ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <label for="inputEnd" class="form-label"><?= lang('absences.end') ?></label>
                                    <div class="input-group mb-3">
                                        <input class="form-control" type="date" id="inputEndDate" name="endDate"
                                               value="<?= $minDate ?>" min="<?= $minDate ?>" aria-describedby="inputEnd"
                                               onchange="adjustDate(this, document.getElementById('inputStartDate'), true)"
                                               required>
                                        <select class="form-select" id="inputEndTime" name="endTime"
                                                aria-describedby="inputEnd"
                                                required>
                                            <option value="" selected disabled><?= lang('absences.pleaseSelect') ?></option>
                                            <option value="-1"><?= lang('absences.schoolDayEnd') ?></option>
                                            <?php $i = 0 ?>
                                            <?php foreach ($timeslots as $timeslot) : ?>
                                                <option value="<?= $i++; ?>">
                                                    <?= $timeslot ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <button id="submitButton" class="btn btn-primary btn-block" type="submit">
                                        <?= lang('absences.submitReport') ?>
                                    </button>
                                    <?= form_close() ?>
                                </div>
                            </div>
                            <?php $active = true; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const calendarMap = new Map();

        function switchToReporting(id) {
            const calendarDiv = document.getElementById('calendar-div-' + id);
            const reportDiv = document.getElementById('report-div-' + id);

            calendarDiv.style.display = 'none';
            reportDiv.style.display = 'block';
        }

        function switchToCalendar(id) {
            const calendarDiv = document.getElementById('calendar-div-' + id);
            const reportDiv = document.getElementById('report-div-' + id);

            calendarDiv.style.display = 'block';
            reportDiv.style.display = 'none';

            let calendar = calendarMap.get(id);
            if (!calendar) {
                calendar = new FullCalendar.Calendar(document.getElementById('calendar-' + id), {
                    locale: '<?= service('request')->getLocale(); ?>',
                    themeSystem: 'bootstrap5',
                    initialView: 'listMonth',
                    headerToolbar: {
                        left: 'prev,next',
                        center: '',
                        right: 'title'
                    },
                    buttonText: {
                        today: 'Heute'
                    },
                    events: '/absence_events/' + id,
                    noEventsContent: '<?= lang('absences.noEntries') ?>',
                    allDayText: ''
                });
            }

            calendar.render();
        }

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
                    <i class="fas fa-list-check"></i> <?= lang('absences.checkAbsences') ?>
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