<?php
/**
 * Configuración de Base de Datos - CoatzaSeguro
 * Credenciales para: tecuidamos.mx/coatzaseguro/
 */

$host = 'localhost';
$dbname = 'NOMBRE DE TU BASE DE DATOS'; 
$username = 'TU USUARIO DB';
$password = 'AQUI VA TU PASSWORD DE LA BASE DE DATOS'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Configurar el modo de error de PDO a excepción para debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Desactivar emulación de sentencias preparadas para mayor seguridad
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // En caso de error crítico de conexión
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>