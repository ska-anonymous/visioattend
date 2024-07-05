<div class="page-title">Visio Attend (Students Attendance System Using Face Recognition)</div>
<hr>
<div class="row">
    <div class="col-4">
        <form action="" id="change-password-form">
            <div class="card">
                <div class="card-body">
                    <h4 class="text-center">Change Password</h4>
                    <div class="form-group">
                        <label for="">Old Password</label>
                        <input type="password" class="form-control" name="oldpass" id="oldpass" required>
                    </div>
                    <div class="form-group">
                        <label for="">New Password</label>
                        <input type="password" class="form-control" name="newpass" id="newpass" required>
                    </div>
                    <div class="form-group">
                        <label for="">Confirm Password</label>
                        <input type="password" class="form-control" name="confirmpass" id="confirmpass" required>
                    </div>
                    <div class="form-group my-1">
                        <button class="btn btn-sm btn-primary">Change</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-8">
        
    </div>
</div>
<script>
    $('#change-password-form').submit(function(e) {
        e.preventDefault()
        var _this = $(this)
        var form = this;
        start_loader();
        _this.find('.flashdata').remove()
        var flashData = $('<div>')
        flashData.addClass('flashdata mb-3')
        flashData.html(`<div class="d-flex w-100 align-items-center flex-wrap">
                      <div class="col-11 flashdata-msg"></div>
                      <div class="col-1 text-center">
                          <a href="javascript:void(0)" onclick="this.closest('.flashdata').remove()" class="flashdata-close"><i class="far fa-times-circle"></i></a>
                      </div>
              </div>`);
        $.ajax({
            url: "ajax-api.php?action=change_password",
            method: "POST",
            data: $(this).serialize(),
            dataType: 'JSON',
            error: (err) => {
                flashData.find('.flashdata-msg').text(`An error occured!`)
                flashData.addClass('flashdata-danger')
                _this.find('.card-body').prepend(flashData)
                end_loader();
                console.warn(err)
            },
            success: function(resp) {
                if (resp?.status == 'success') {
                    flashData.find('.flashdata-msg').text(`Password changed successfully`)
                    flashData.addClass('flashdata-success')
                    _this.find('.card-body').prepend(flashData)
                    form.reset();
                    end_loader();
                } else {
                    if (resp?.msg != '') {
                        flashData.find('.flashdata-msg').text(`${resp?.msg}`)
                        flashData.addClass('flashdata-danger')
                        _this.find('.card-body').prepend(flashData)
                        end_loader();
                    }
                }
            }
        })
    })
</script>