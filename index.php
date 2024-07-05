<?php
session_start();
// first check login
if (!isset($_SESSION['user_data'])) {
    header('location:pages/login.php');
}

// store the admin only pages in an array for checkup
$admin_pages_only = ['register_user', 'class_list', 'student_list'];

require_once('classes/actions.class.php');
$actionClass = new Actions();
$page = $_GET['page'] ?? "home";

if (in_array($page, $admin_pages_only) && $_SESSION['user_data']['role'] != 'admin') {
    header('location:./');
}

$page_title = ucwords(str_replace("_", " ", $page));

 // include db connection
 require('db-connect.php');

?>
<!DOCTYPE html>
<html lang="en">
<?php include_once('inc/header.php'); ?>

<body>
    <?php include_once('inc/navigation.php'); ?>
    <div class="container-md py-3">
        <?php if (isset($_SESSION['flashdata']) && !empty($_SESSION['flashdata'])) : ?>
            <div class="flashdata flashdata-<?= $_SESSION['flashdata']['type'] ?? 'default' ?> mb-3">
                <div class="d-flex w-100 align-items-center flex-wrap">
                    <div class="col-11"><?= $_SESSION['flashdata']['msg'] ?? '' ?></div>
                    <div class="col-1 text-center">
                        <a href="javascript:void(0)" onclick="this.closest('.flashdata').remove()" class="flashdata-close"><i class="far fa-times-circle"></i></a>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['flashdata']); ?>
        <?php endif; ?>
        <div class="main-wrapper">
            <?php include_once("pages/{$page}.php"); ?>
        </div>
    </div>
    <?php include_once('inc/footer.php'); ?>
</body>

</html>