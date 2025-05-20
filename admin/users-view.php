<?php
require_once('includes/user-handlers.php');

$pageData = initializeUsersView($conn);

$search = $pageData['search'];
$role = $pageData['role'];
$sort_by = $pageData['sort_by'];
$sort_order = $pageData['sort_order'];
$result = $pageData['result'];
$success_message = $pageData['success_message'];
$error_message = $pageData['error_message'];
?>

<?php require_once("includes/header.php") ?>
<h1>User Management</h1>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success">
        <?php echo $success_message; ?>
    </div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger">
        <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<!-- Filters Section -->
<div class="user-filters">
    <form action="" method="GET">
        <input type="text" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">

        <select name="role">
            <option value="">All Roles</option>
            <option value="admin" <?php echo $role == 'admin' ? 'selected' : ''; ?>>Admin</option>
            <option value="user" <?php echo $role == 'user' ? 'selected' : ''; ?>>User</option>
        </select>

        <input type="hidden" name="sort_by" value="<?php echo htmlspecialchars($sort_by); ?>">
        <input type="hidden" name="sort_order" value="<?php echo htmlspecialchars($sort_order); ?>">

        <button type="submit">Filter</button>
        <button type="button" onclick="window.location.href='users-view.php'">Reset</button>
        <button type="button" onclick="window.location.href='user-add.php'" class="form-buttons filter-button">
            <i class="fas fa-plus"></i> Add New User
        </button>
    </form>
</div>

<!-- Users Table -->
<div class="table-responsive">
    <table class="users-table">
        <thead>
            <tr>
                <th onclick="sortUserTable('id')">ID</th>
                <th onclick="sortUserTable('full_name')">Name</th>
                <th onclick="sortUserTable('email')">Email</th>
                <th onclick="sortUserTable('phone')">Phone</th>
                <th onclick="sortUserTable('position')">Role</th>
                <th onclick="sortUserTable('created_at')">Registration Date</th>
                <th onclick="sortUserTable('last_login')">Last Login</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . htmlspecialchars($row['full_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
                    echo '<td>' . ucfirst(htmlspecialchars($row['position'])) . '</td>';
                    echo '<td>' . date('M d, Y', strtotime($row['created_at'])) . '</td>';
                    echo '<td>' . (!empty($row['last_login']) ? date('M d, Y H:i', strtotime($row['last_login'])) : 'Never') . '</td>';
                    echo '<td class="action-buttons">';
                    echo '<a href="user-add.php?id=' . $row['id'] . '" class="table-buttons edit-button"><i class="fas fa-edit"></i></a>';
                    echo '<a href="javascript:void(0);" onclick="confirmUserDelete(' . $row['id'] . ')" class="table-buttons delete-button"><i class="fas fa-trash"></i></a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="9">No users found</td></tr>';
            }
            ?>
        </tbody>
    </table>

    <!-- Js File -->
    <script src="js/script.js?v<?= time(); ?>"></script>

    <?php require_once("includes/footer.php") ?>