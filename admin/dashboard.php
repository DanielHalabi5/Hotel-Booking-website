
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Vellora Hotel</title>
    <link rel="stylesheet" type="text/css" href="../css/dashboard.css">
    <script src="https://kit.fontawesome.com/354cef8def.js" crossorigin="anonymous"></script>
</head>

<body>
    <div id="DbContainer">
        <div class="sidebar" id="sidebar">
            <h3 class="DBLogo" id="DBLogo"> Vellora Hotel</h3>
            <div class="sidebar_user">
                <span>Username</span>
            </div>
            <div class="sidebar_menu">
                <ul class="MenuList">
                    <li class="liMainMenu">
                        <a href="./dashboard.php"><i class="fa fa-dashboard "></i> <span class="menuText">Dashboard</span></a>
                    </li>
                    <li class="liMainMenu showHideSideMenu">
                        <a href="rooms.php">
                            <i class="fa-solid fa-key showHideSideMenu"></i>
                            <span class="menuText showHideSideMenu"> Rooms </span>
                        </a>
                    </li>
                    <li class="liMainMenu showHideSideMenu">
                        <a href="bookings.php">
                            <i class="fa fa-bed showHideSideMenu"></i>
                            <span class="menuText showHideSideMenu"> Bookings </span>
                        </a>
                    </li>
                    <li class="liMainMenu showHideSideMenu">
                        <a href="users-view.php">
                            <i class="fa fa-user-plus showHideSideMenu"></i>
                            <span class="menuText showHideSideMenu"> User </span>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
        <div class="DBContentConainer" id="DBContentConainer">
            <div class="DBContent">
                <div class="CBody">
                </div>
            </div>
        </div>
    </div>

</body>

</html>