<?php
function connection() {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $bd = "users_crud_php";

    $connect = mysqli_connect($host, $user, $pass);

    if (!$connect) {
        error_log("Error de conexión: " . mysqli_connect_error());
        return null;
    }

    if (!mysqli_select_db($connect, $bd)) {
        error_log("Error al seleccionar la base de datos: " . mysqli_error($connect));
        mysqli_close($connect);
        return null;
    }

    // Establecer charset UTF-8 para evitar problemas con caracteres especiales
    mysqli_set_charset($connect, "utf8mb4");

    return $connect;
}