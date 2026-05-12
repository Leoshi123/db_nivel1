<?php
session_start();

// Inicializar variables para mensajes
$error = "";
$success = "";

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Función para escapar HTML
function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

include("conection.php");
$con = connection();

$users = [];

if ($con) {
    // Usar prepared statement para SELECT (aunque no tenga parámetros, es buena práctica)
    $sql = "SELECT id, name, lastname, username, email FROM users ORDER BY id DESC";
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_array($result)) {
            $users[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($con);
} else {
    $error = "Error de conexión a la base de datos.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <title>Users CRUD</title>
</head>
<body>
    <?php if ($error): ?>
        <div class="alert alert-error">
            <?= escape($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= escape($success) ?>
        </div>
    <?php endif; ?>

    <div class="users-form">
        <h1>➕ Crear usuario</h1>
        <form action="insert_user.php" method="POST">
            <input type="text" name="name" placeholder="Nombre" required>
            <input type="text" name="lastname" placeholder="Apellidos" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="submit" value="Agregar">
        </form>
    </div>

    <div class="users-table">
        <h2>👥 Usuarios registrados</h2>
        <?php if (empty($users)): ?>
            <p style="padding: 20px;">No hay usuarios registrados.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $row): ?>
                        <tr>
                            <th><?= escape($row['id']) ?></th>
                            <th><?= escape($row['name']) ?></th>
                            <th><?= escape($row['lastname']) ?></th>
                            <th><?= escape($row['username']) ?></th>
                            <th><?= escape($row['email']) ?></th>
                            <th><a href="update_user.php?id=<?= urlencode($row['id']) ?>" class="users-table--edit">Editar</a></th>
                            <th><a href="delete_user.php?id=<?= urlencode($row['id']) ?>" class="users-table--delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">Eliminar</a></th>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>