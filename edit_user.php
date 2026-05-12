<?php
include("conection.php");

session_start();
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $con = connection();

    if (!$con) {
        $error = "Error de conexión a la base de datos.";
    } else {
        // Validar y sanitizar inputs
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $email = trim($_POST['email'] ?? '');

        // Validar que el ID sea numérico
        if (!is_numeric($id) || $id <= 0) {
            $error = "ID de usuario inválido.";
        } elseif (empty($name) || empty($lastname) || empty($username) || empty($email)) {
            $error = "Los campos Nombre, Apellidos, Username y Email son obligatorios.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "El formato del email no es válido.";
        } else {
            // Verificar si el username ya existe (excluyendo el usuario actual)
            $checkSql = "SELECT id FROM users WHERE username = ? AND id != ?";
            $checkStmt = mysqli_prepare($con, $checkSql);
            mysqli_stmt_bind_param($checkStmt, "si", $username, $id);
            mysqli_stmt_execute($checkStmt);
            mysqli_stmt_store_result($checkStmt);

            if (mysqli_stmt_num_rows($checkStmt) > 0) {
                $error = "El nombre de usuario ya está en uso por otro usuario.";
            } else {
                // Obtener la contraseña actual si no se proporciona una nueva
                $getPasswordSql = "SELECT password FROM users WHERE id = ?";
                $getPasswordStmt = mysqli_prepare($con, $getPasswordSql);
                mysqli_stmt_bind_param($getPasswordStmt, "i", $id);
                mysqli_stmt_execute($getPasswordStmt);
                $passwordResult = mysqli_stmt_get_result($getPasswordStmt);
                $passwordRow = mysqli_fetch_array($passwordResult);
                mysqli_stmt_close($getPasswordStmt);

                // Usar la nueva contraseña si se proporcionó, o mantener la actual
                $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $passwordRow['password'];

                // Usar prepared statement para UPDATE
                $sql = "UPDATE users SET name = ?, lastname = ?, username = ?, password = ?, email = ? WHERE id = ?";
                $stmt = mysqli_prepare($con, $sql);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssssi", $name, $lastname, $username, $hashedPassword, $email, $id);

                    if (mysqli_stmt_execute($stmt)) {
                        $success = "Usuario actualizado correctamente.";
                    } else {
                        $error = "Error al actualizar el usuario: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error = "Error en la preparación de la consulta.";
                }
            }
            mysqli_stmt_close($checkStmt);
        }
        mysqli_close($con);
    }

    if ($error) {
        $_SESSION['error'] = $error;
    } elseif ($success) {
        $_SESSION['success'] = $success;
    }
    header("Location: index.php");
    exit;
} else {
    header("Location: index.php");
    exit;
}