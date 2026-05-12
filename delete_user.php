<?php
include("conection.php");

session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $con = connection();
    $error = "";
    $success = "";

    if (!$con) {
        $error = "Error de conexión a la base de datos.";
    } else {
        $id = $_GET['id'] ?? '';

        // Validar que el ID sea numérico
        if (!is_numeric($id) || $id <= 0) {
            $error = "ID de usuario inválido.";
        } else {
            // Usar prepared statement para DELETE
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = mysqli_prepare($con, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id);

                if (mysqli_stmt_execute($stmt)) {
                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        $success = "Usuario eliminado correctamente.";
                    } else {
                        $error = "Usuario no encontrado.";
                    }
                } else {
                    $error = "Error al eliminar el usuario: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "Error en la preparación de la consulta.";
            }
        }
        mysqli_close($con);
    }

    if ($error) {
        $_SESSION['error'] = $error;
    } elseif ($success) {
        $_SESSION['success'] = $success;
    }
}

header("Location: index.php");
exit;