<div class="page-title mb-3">Manage Attendance</div>
<hr>
<?php
// $studentList = $actionClass->list_student();
$classList = $actionClass->list_class();
$class_id = $_GET['class_id'] ?? "";
$class_date = $_GET['class_date'] ?? "";
$studentList = $actionClass->attendanceStudents($class_id, $class_date);

// create a refactored students list for javascript
$studentListRefactored = [];

foreach ($studentList as $student) {
    $studentRefactored = $student;
    $studentRefactored['face_descriptor'] = json_decode($studentRefactored['face_descriptor']);
    $studentListRefactored[] = $studentRefactored;
}

?>
<!-- <pre>
    <?php print_r($studentList) ?>
</pre> -->
<form action="" id="manage-attendance">

    <div class="accordion shadow accordion-flush sticky-top mb-3" style="top:55px;" id="accordionFlushExample">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                    Scanner
                </button>
            </h2>
            <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
                    <div class="row align-items-center">
                        <div class="col-6 text-center">
                            <div id="scannedInfo">

                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <video style="height: 200px;" onplay="onPlay(this)" class=" border border-dark" autoplay muted></video>
                                <div>
                                    <button class="btn btn-success" onclick="toggleScanning(event)" type="button">Start Scanning</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div id="msg"></div>
            <div class="card shadow mb-3">
                <div class="card-body rounded-0">
                    <div class="container-fluid">

                        <div class="row align-items-end">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <label for="class_id" class="form-label">Class</label>
                                <select name="class_id" id="class_id" class="form-select" required="required">
                                    <option value="" disabled <?= empty($class_id) ? "selected" : "" ?>> -- Select Here -- </option>
                                    <?php if (!empty($classList) && is_array($classList)) : ?>
                                        <?php foreach ($classList as $row) : ?>
                                            <option value="<?= $row['id'] ?>" <?= (isset($class_id) && $class_id == $row['id']) ? "selected" : "" ?>><?= $row['name'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <label for="class_date" class="form-label">Date</label>
                                <input type="date" name="class_date" id="class_date" class="form-control" value="<?= $class_date ?? '' ?>" required="required">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!empty($class_id) && !empty($class_date)) : ?>
                <div class="card shadow mb-3">
                    <div class="card-header rounded-0">
                        <div class="card-title">Attendance Sheet</div>
                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="table-responsive">
                                <table id="attendance-tbl" class="table table-bordered">
                                    <colgroup>
                                        <col width="40%">
                                        <col width="15%">
                                        <col width="15%">
                                        <col width="15%">
                                        <col width="15%">
                                    </colgroup>
                                    <thead class="bg-primary">
                                        <tr>
                                            <th class="text-center bg-transparent text-light">Students</th>
                                            <th class="text-center bg-transparent text-light">Present</th>
                                            <th class="text-center bg-transparent text-light">Late</th>
                                            <th class="text-center bg-transparent text-light">Absent</th>
                                            <th class="text-center bg-transparent text-light">Holiday</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th class="text-center px-2 py-1 text-dark-emphasis">Check/Uncheck All</th>
                                            <th class="text-center px-2 py-1 text-dark-emphasis">
                                                <div class="form-check d-flex w-100 justify-content-center">
                                                    <input class="form-check-input checkAll" type="checkbox" id="PCheckAll">
                                                    <label class="form-check-label" for="PCheckAll">
                                                    </label>
                                                </div>
                                            </th>
                                            <th class="text-center px-2 py-1 text-dark-emphasis">
                                                <div class="form-check d-flex w-100 justify-content-center">
                                                    <input class="form-check-input checkAll" type="checkbox" id="LCheckAll">
                                                    <label class="form-check-label" for="LCheckAll">
                                                    </label>
                                                </div>
                                            </th>
                                            <th class="text-center px-2 py-1 text-dark-emphasis">
                                                <div class="form-check d-flex w-100 justify-content-center">
                                                    <input class="form-check-input checkAll" type="checkbox" id="ACheckAll">
                                                    <label class="form-check-label" for="ACheckAll">
                                                    </label>
                                                </div>
                                            </th>
                                            <th class="text-center px-2 py-1 text-dark-emphasis">
                                                <div class="form-check d-flex w-100 justify-content-center">
                                                    <input class="form-check-input checkAll" type="checkbox" id="HCheckAll">
                                                    <label class="form-check-label" for="HCheckAll">
                                                    </label>
                                                </div>
                                            </th>
                                        </tr>
                                        <?php if (!empty($studentList) && is_array($studentList)) : ?>
                                            <?php foreach ($studentList as $row) : ?>
                                                <tr class="student-row">
                                                    <td class="px-2 py-1 text-dark-emphasis fw-bold">
                                                        <input type="hidden" name="student_id[]" value="<?= $row['id'] ?>">
                                                        <?= $row['name'] ?>
                                                    </td>
                                                    <td class="text-center px-2 py-1 text-dark-emphasis">
                                                        <div class="form-check d-flex w-100 justify-content-center">
                                                            <input class="form-check-input status_check" data-id="<?= $row['id'] ?>" type="checkbox" name="status[]" value="1" id="status_p_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 1) ? "checked" : "" ?>>
                                                            <label class="form-check-label" for="status_p_<?= $row['id'] ?>">
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center px-2 py-1 text-dark-emphasis">
                                                        <div class="form-check d-flex w-100 justify-content-center">
                                                            <input class="form-check-input status_check" data-id="<?= $row['id'] ?>" type="checkbox" name="status[]" value="2" id="status_l_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 2) ? "checked" : "" ?>>
                                                            <label class="form-check-label" for="status_l_<?= $row['id'] ?>">
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center px-2 py-1 text-dark-emphasis">
                                                        <div class="form-check d-flex w-100 justify-content-center">
                                                            <input class="form-check-input status_check" data-id="<?= $row['id'] ?>" type="checkbox" name="status[]" value="3" id="status_a_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 3) ? "checked" : "" ?>>
                                                            <label class="form-check-label" for="status_a_<?= $row['id'] ?>">
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center px-2 py-1 text-dark-emphasis">
                                                        <div class="form-check d-flex w-100 justify-content-center">
                                                            <input class="form-check-input status_check" data-id="<?= $row['id'] ?>" type="checkbox" name="status[]" value="4" id="status_h_<?= $row['id'] ?>" <?= (isset($row['status']) && $row['status'] == 4) ? "checked" : "" ?>>
                                                            <label class="form-check-label" for="status_h_<?= $row['id'] ?>">
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="5" class="px-2 py-1 text-center">No Student Listed Yet</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex w-100 justify-content-center align-items-center">
                    <div class="col-lg-4 col-md-6 col-sm-12 col-12">
                        <button class="btn btn-primary rounded-pill w-100" type="submit">Save Attendance</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>
<script>
    const studentsList = JSON.parse('<?php echo json_encode($studentListRefactored) ?>');
    var labeledDescriptors = [];

    $(document).ready(async function() {
        await loadModels();
        // now prepare labeled face descriptors from the students list
        labeledDescriptors = studentsList.map(student => {
            return new faceapi.LabeledFaceDescriptors(
                student.id,
                [new Float32Array(Object.values(student.face_descriptor))]
            )
        })
    })

    let isWebcamActive = false;
    let videoStream = null;
    let onPlayId = null;
    let speechLastRollNo = null;

    const toggleScanning = async (event) => {
        event.preventDefault();

        const videoEl = document.querySelector('video');
        const button = event.currentTarget;

        if (!videoEl)
            return alert('Video Element not found');

        // now get the webcam feed to the video element 
        if (!isWebcamActive) {
            try {
                videoStream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });
                videoEl.srcObject = videoStream;
                button.textContent = 'Stop Scanning';
                isWebcamActive = true;
            } catch (err) {
                alert('Cannot Access Webcam');
                console.error('Error accessing the webcam: ', err);
            }
        } else {
            if (videoStream) {
                if (onPlayId) {
                    clearTimeout(onPlayId);
                    onPlayId = null;
                }
                const tracks = videoStream.getTracks();
                tracks.forEach(track => track.stop());
                videoEl.srcObject = null;
                button.textContent = 'Start Scanning';
                isWebcamActive = false;
            }
        }
    }

    async function onPlay(videoEl) {
        // run face detection & recognition

        const mtcnnParams = {
            minFaceSize: 400,
            scaleFactor: 0.709,
            scoreThresholds: [0.6, 0.7, 0.7]
        };
        const options = new faceapi.MtcnnOptions(mtcnnParams)
        const fullFaceDescriptions = await faceapi.detectAllFaces(videoEl, options).withFaceLandmarks().withFaceDescriptors();

        // now do face matching
        const maxDescriptorDistance = 0.6
        const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, maxDescriptorDistance)

        const results = fullFaceDescriptions.map(fd => faceMatcher.findBestMatch(fd.descriptor))

        // now get the matched student id from label and mark the student as present
        results.forEach((bestMatch, i) => {
            const studentId = bestMatch.label;
            if (studentId != 'unknown') {
                markStudentPresent(studentId);
                speakStudentName(studentId);
            }
        })

        // repeat the process to get the latest frame from webcam
        onPlayId = setTimeout(() => onPlay(videoEl));
    }

    const markStudentPresent = (studentId) => {
        let studentPresentCheckbox = document.querySelector('#status_p_' + studentId);
        if (!studentPresentCheckbox)
            return;
        studentPresentCheckbox.checked = true;
        studentPresentCheckbox.dispatchEvent(new Event('change'));
    }
    const speakStudentName = (studentId) => {
        // first of all get student data from the students list
        const studentData = studentsList.find(student => student.id == studentId);
        if (!studentData)
            return;

        const studentName = studentData.name;
        const studentRollNo = studentData.rollno;

        // put the student name and roll no in info section
        document.querySelector('#scannedInfo').innerHTML = `<h4>${studentName} ${studentRollNo}</h4>`;

        if (speechLastRollNo != studentRollNo) {
            const speech = new SpeechSynthesisUtterance(studentName + studentRollNo);
            speech.lang = 'en-US'; // Set the language, e.g., 'en-US' for English (United States)
            window.speechSynthesis.speak(speech);
            speechLastRollNo = studentRollNo;
        }

    }

    async function loadModels() {
        const MODEL_URL = 'face_api/models';

        await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
        await faceapi.nets.mtcnn.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
    }

    $(document).ready(function() {
        checkAll_count()

        $('#class_id, #class_date').change(function(e) {
            var class_id = $('#class_id').val()
            var class_date = $('#class_date').val()
            location.replace(`./?page=attendance&class_id=${class_id}&class_date=${class_date}`)
        })
        $('.status_check').change(function() {
            var student_id = $(this)[0].dataset?.id
            var isChecked = $(this).is(":checked")
            if (isChecked === true) {
                $(`.status_check[data-id='${student_id}']`).prop("checked", false)
                $(this).prop("checked", true)
            }
            checkAll_count()
        })
        $('.checkAll').change(function() {
            var _this = $(this)
            var isChecked = $(this).is(":checked")
            var id = $(this).attr('id')
            if (isChecked === true) {
                $('.checkAll').each(function() {
                    if ($(this).attr('id') != id && $(this).is(":checked") == true) {
                        $(this).prop("checked", false)
                    }
                })
                $('.status_check').prop('checked', false)
                if (id == 'PCheckAll') {
                    $('.status_check[value="1"]').prop('checked', true)
                } else if (id == 'LCheckAll') {
                    $('.status_check[value="2"]').prop('checked', true)
                } else if (id == 'ACheckAll') {
                    $('.status_check[value="3"]').prop('checked', true)
                } else if (id == 'HCheckAll') {
                    $('.status_check[value="4"]').prop('checked', true)
                }
            } else {
                if (id == 'PCheckAll') {
                    $('.status_check[value="1"]').prop('checked', false)
                } else if (id == 'LCheckAll') {
                    $('.status_check[value="2"]').prop('checked', false)
                } else if (id == 'ACheckAll') {
                    $('.status_check[value="3"]').prop('checked', false)
                } else if (id == 'HCheckAll') {
                    $('.status_check[value="4"]').prop('checked', false)
                }
            }
        })
        $('#manage-attendance').submit(function(e) {
            e.preventDefault()
            start_loader()
            var _this = $(this)
            $('#attendance-tbl .student-row').each(function() {
                var has_checks = $(this).find('.status_check:checked').length
                if (has_checks < 1) {
                    var name = $(this).find('td').first().text() || "";
                    name = String(name).trim();
                    console.log(name)
                    alert(`${name}'s attendance is not yet marked!`);
                    end_loader()
                    return false;
                }
            })
            $.ajax({
                url: './ajax-api.php?action=save_attendance',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'JSON',
                error: (err) => {
                    console.error(err)
                    alert("An error occurred while saving the data. kindly reload this page.")
                    end_loader();
                },
                success: function(resp) {
                    if (resp?.status == "success") {
                        location.reload()
                    } else if (resp?.status == "error" && resp?.msg != "") {
                        var fd = $(flashdataHTML).clone()
                        fd.addClass('flashdata-danger')
                        fd.find('.flashdata-msg').html(resp.msg)
                        $('#msg').html(fd)
                        $('html, body').scrollTop(0)
                    } else {
                        alert("An error occurred while saving the data. kindly reload this page.")
                    }
                    end_loader();
                }
            })
        })
    })

    function checkAll_count() {
        var statuses = {
            'PCheckAll': 1,
            'LCheckAll': 2,
            'ACheckAll': 3,
            'HCheckAll': 4
        }
        $('.checkAll').each(function() {
            var id = $(this).attr('id')
            var checkedCount = $(`.status_check[value="${statuses[id]}"]:checked`).length
            var totalCount = $(`.status_check[value="${statuses[id]}"]`).length
            if (totalCount != checkedCount) {
                $(this).prop('checked', false)
            } else {
                $(`#${id}`).prop('checked', true)
            }
        })
    }
</script>