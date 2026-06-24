<?php
class Database {
    private $host = "localhost";
    private $db_name = "catedral";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                  $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }

    /**
     * Registra una acción en la tabla 'actividades' para auditoría.
     * * @param string $accion Descripción de lo realizado (ej: "ELIMINÓ FELIGRÉS")
     * @param string $modulo Módulo afectado (ej: "FELIGRESES")
     * @param string $detalle Detalles adicionales (ej: "ID: 123")
     */
    public function registrarActividad($accion, $modulo, $detalle = "") {
        // Asegurarse de que la conexión esté activa
        if (!$this->conn) {
            $this->getConnection();
        }

        try {
            // Obtener datos de sesión si existen
            $id_usuario = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
            $nombre_usuario = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : '';
            $ip = $_SERVER['REMOTE_ADDR'];

            $query = "INSERT INTO actividades (id_usuario, nombre_usuario, accion, modulo, fecha) 
                      VALUES (:id_usuario, :nombre_usuario, :accion, :modulo, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':id_usuario' => $id_usuario,
                ':nombre_usuario' => $nombre_usuario,
                ':accion' => $accion . ($detalle ? " - " . $detalle : ""),
                ':modulo' => $modulo
            ]);
        } catch (PDOException $e) {
            // Opcional: registrar error en log de servidor
            error_log("Error al registrar actividad: " . $e->getMessage());
        }
    }
}
?>