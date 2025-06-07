<?php
require_once __DIR__ . '/inc/user.php';
require_once __DIR__ . '/inc/admin.php';
if (!isUserAdmin($db, $_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$pageTitle = 'Úprava uživatelů';
include __DIR__ . '/inc/layoutApp.php';


$query = $db->query('SELECT user_id, name, surname, email, admin FROM user ORDER BY surname, name');
$query->execute();
$users = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="">

    <ul class="list-group">
        <li class="list-group-item d-flex fw-bold">
            <div class="col-3 font-weight-bold">Jméno</div>
            <div class="col-3 font-weight-bold">Příjmení</div>
            <div class="col-4 font-weight-bold">E-mail</div>
            <div class="col-1 text-center font-weight-bold">Admin</div>
            <div class="col-1 font-weight-bold"></div>
        </li>
        <?php foreach ($users as $user): ?>
            <li class="list-group-item d-flex align-items-center">
                <div class="col-3"><?= htmlspecialchars($user['name']) ?></div>
                <div class="col-3"><?= htmlspecialchars($user['surname']) ?></div>
                <div class="col-4"><?= htmlspecialchars($user['email']) ?></div>
                <div class="col-1 text-center"><?= $user['admin'] ? '<span class="text-success fw-bold">✔</span>' : '' ?>
                </div>
                <div class="col-1">
                    <form method="post" action="deleteUser.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= (int) $user['user_id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Opravdu smazat?');"
                            title="Smazat">
                            &times;
                        </button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>