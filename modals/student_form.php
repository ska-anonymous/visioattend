<?php
session_start();
require_once(realpath(__DIR__ . '/../classes/actions.class.php'));
$actionClass = new Actions();
if (isset($_POST['id'])) {
  $student = $actionClass->get_student($_POST['id']);
  extract($student);
}
$classList = $actionClass->list_class();
?>
<div class="container-fluid">
  <form id="student-form" method="POST">
    <input type="hidden" name="id" value="<?= $id ?? "" ?>">
    <div class="row">
      <div class="col-md-4">
        <div class="mb-3">
          <label for="class_id" class="form-label">Class Name & Subject</label>
          <select type="text" class="form-select" id="class_id" name="class_id" required="required">
            <option value="" <?= !isset($id) ? "selected" : "" ?> disabled> -- Select Class Here -- </option>
            <?php if (!empty($classList) && is_array($classList)) : ?>
              <?php foreach ($classList as $row) : ?>
                <option value="<?= $row['id'] ?>" <?= (isset($class_id) && $class_id == $row['id']) ? "selected" : "" ?>><?= $row['name'] ?></option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="name" class="form-label">Student Name</label>
          <input type="text" class="form-control" id="name" name="name" value="<?= $name ?? "" ?>" required="required">
        </div>
        <div class="mb-3">
          <label for="rollno" class="form-label">Roll No</label>
          <input type="number" class="form-control" id="rollno" name="rollno" value="<?= $rollno ?? "" ?>" required="required">
        </div>
        <div class="mb-3">
          <label for="face_descriptor" class="form-label">Face Id</label>
          <textarea type="text" class="form-control" id="face_descriptor" name="face_descriptor" readonly required="required"> <?= $face_descriptor ?? "" ?></textarea>
        </div>
      </div>
      <div class="col-md-4">
        <div class="mb-3">
          <label for="photo" class="form-label">Upload or Capture Face Photo</label>
          <input type="file" accept="image/*" class="form-control mb-1" id="photo" name="photo" required="required">
        </div>
      </div>
      <div class="col-md-4">
        <button type="button" id="cameraButton" class="btn btn-primary">Start Camera</button>
        <button type="button" class="btn btn-success mb-1" id="captureButton" disabled='true'>Capture</button>
        <video id="video" width="320" height="240" autoplay></video>
        <canvas id="canvas" width="320" height="240" style="display: none;"></canvas>
      </div>
    </div>
  </form>
</div>

<script>
  $('#student-form').submit(function(e) {
    e.preventDefault()
    var _this = $(this)
    start_loader();
    $(uniModal).find('.flashdata').remove()
    var flashData = $('<div>')
    flashData.addClass('flashdata mb-3')
    flashData.html(`<div class="d-flex w-100 align-items-center flex-wrap">
                      <div class="col-11 flashdata-msg"></div>
                      <div class="col-1 text-center">
                          <a href="javascript:void(0)" onclick="this.closest('.flashdata').remove()" class="flashdata-close"><i class="far fa-times-circle"></i></a>
                      </div>
              </div>`);
    $.ajax({
      url: "ajax-api.php?action=save_student",
      method: "POST",
      data: $(this).serialize(),
      dataType: 'JSON',
      error: (err) => {
        flashData.find('.flashdata-msg').text(`An error occured!`)
        flashData.addClass('flashdata-danger')
        _this.prepend(flashData)
        end_loader();
        console.warn(err)
      },
      success: function(resp) {
        if (resp?.status == 'success') {
          location.reload()
        } else {
          if (resp?.msg != '') {
            flashData.find('.flashdata-msg').text(`${resp?.msg}`)
            flashData.addClass('flashdata-danger')
            _this.prepend(flashData)
            end_loader();
          }
        }
      }
    })
  })

  var fileInput = document.querySelector('#photo');
  fileInput.addEventListener('change', async (event) => {
    const fileInput = event.target;
    const files = fileInput.files;

    if (files.length == 0) {
      document.querySelector('#face_descriptor').value = '';
      // remove the image if already put in the form
      fileInput.parentElement.querySelector('img')?.remove();
      return;
    }

    const photo = files[0];
    // check if the file type is not image
    if (!photo.type.startsWith('image/')) {
      alert('Please upload image file');
      fileInput.value = '';
      // remove the image if already put in the form
      fileInput.parentElement.querySelector('img')?.remove();
      return;
    }

    // first remove the first image if this is second time of upload or capture
    fileInput.parentElement.querySelector('img')?.remove();

    const img = document.createElement('img');
    const imgUrl = URL.createObjectURL(photo);
    img.src = imgUrl;
    img.classList.add('img-fluid');

    // put the image in the form for user
    fileInput.parentElement.append(img);

    // now do the rest of the face_api process with the photo
    start_loader();
    try {
      let fullFaceDescriptions = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
      document.querySelector('#face_descriptor').value = JSON.stringify(fullFaceDescriptions.descriptor);

    } catch (err) {
      alert('Faced not identified in the image please upload or capture an image with clear face');
      console.log(err);
      fileInput.value = '';
      fileInput.parentElement.querySelector('img')?.remove();
    }
    end_loader();

  });

  var isWebcamActive = false;
  var videoStream = null;

  var cameraButton = document.getElementById('cameraButton');
  var captureButton = document.getElementById('captureButton');


  cameraButton.addEventListener('click', async (event) => {
    event.preventDefault();
    const video = document.getElementById('video');

    if (isWebcamActive) {
      videoStream.getTracks().forEach(track => track.stop());
      isWebcamActive = false;
      cameraButton.textContent = 'Start Camera';
      captureButton.disabled = true;
    } else {
      try {
        videoStream = await navigator.mediaDevices.getUserMedia({
          video: true
        })
        video.srcObject = videoStream;
        isWebcamActive = true;
        cameraButton.textContent = 'Stop Camera';
        captureButton.disabled = false;
      } catch (err) {
        console.error('Error accessing webcam: ' + err);
        alert("Error accessing camera");
        return;
      }
    }
  })

  captureButton.addEventListener('click', async (event) => {
    event.preventDefault();
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');

    // Draw the video frame to the canvas
    const context = canvas.getContext('2d');

    // fist clear the canvas if previously image was put there
    context.clearRect(0, 0, canvas.width, canvas.height);

    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Convert the canvas image to a data URL
    canvas.toBlob(blob => {
      // Create a file from the blob
      const file = new File([blob], "captured-image.png", {
        type: "image/png"
      });

      // Create a data transfer object to simulate a file input change
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);

      // Set the file input's files property
      fileInput.files = dataTransfer.files;

      // Dispatch a change event to trigger the existing callback
      const event = new Event('change');
      fileInput.dispatchEvent(event);
    }, 'image/png');

  })
</script>