<nav class="navbar navbar-expand-lg navbar-light bg-dark sticky-top" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Visio Attend</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= (isset($page)) && $page == 'home' ? 'active' : '' ?>" href="./">Home</a>
                </li>
                <?php
                // check if admin is logged
                if (isset($_SESSION['user_data']) && $_SESSION['user_data']['role'] == 'admin') {

                ?>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($page)) && $page == 'class_list' ? 'active' : '' ?>" href="./?page=class_list">Classes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($page)) && $page == 'student_list' ? 'active' : '' ?>" href="./?page=student_list">Students</a>
                    </li>
                <?php

                }
                ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($page)) && $page == 'attendance' ? 'active' : '' ?>" href="./?page=attendance">Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($page)) && $page == 'attendance_report' ? 'active' : '' ?>" href="./?page=attendance_report">Report</a>
                </li>
                <?php
                // check if admin is logged
                if (isset($_SESSION['user_data']) && $_SESSION['user_data']['role'] == 'admin') {

                ?>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($page)) && $page == 'register_user' ? 'active' : '' ?>" href="./?page=register_user">Register User</a>
                    </li>
                <?php

                }
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="pages/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>