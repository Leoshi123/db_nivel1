<?php
include("conection.php");

// Inicializar mensaje de error
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $con = connection();

    if (!$con) {
        $error = "Error de conexión a la base de datos.";
    } else {
        // Validar que los campos no estén vacíos
        $name = trim($_POST['name'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $email = trim($_POST['email'] ?? '');

        if (empty($name) || empty($lastname) || empty($username) || empty($password) || empty($email)) {
            $error = "Todos los campos son obligatorios.";
        } else {
            // Validar email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "El formato del email no es válido.";
            } else {
                // Hashear la contraseña
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Verificar si el username ya existe
                $checkSql = "SELECT id FROM users WHERE username = ?";
                $checkStmt = mysqli_prepare($con, $checkSql);
                mysqli_stmt_bind_param($checkStmt, "s", $username);
                mysqli_stmt_execute($checkStmt);
                mysqli_stmt_store_result($checkStmt);

                if (mysqli_stmt_num_rows($checkStmt) > 0) {
                    $error = "El nombre de usuario ya está en uso.";
                } else {
                    // Usar prepared statement para Insert
                    $sql = "INSERT INTO users (name, lastname, username, password, email) VALUES (?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($con, $sql);

                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "sssss", $name, $lastname, $username, $hashedPassword, $email);

                        if (mysqli_stmt_execute($stmt)) {
                            $success = "Usuario creado correctamente.";
                        } else {
                            $error = "Error al crear el usuario: " . mysqli_stmt_error($stmt);
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $error = "Error en la preparación de la consulta.";
                    }
                }
                mysqli_stmt_close($checkStmt);
            }
        }
        mysqli_close($con);
    }

    // Guardar mensajes en sesión para mostrar en index
    session_start();
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