-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS sistema_parroquial;
USE sistema_parroquial;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'secretaria', 'archivista', 'parroco') NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de feligreses
CREATE TABLE feligreses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) UNIQUE NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE,
    telefono VARCHAR(15),
    email VARCHAR(100),
    direccion TEXT,
    estado_civil ENUM('soltero', 'casado', 'divorciado', 'viudo'),
    fecha_bautismo DATE,
    fecha_comunion DATE,
    fecha_confirmacion DATE,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de catequesis
CREATE TABLE catequesis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('prebautismal', 'comunion', 'confirmacion', 'prematrimonial') NOT NULL,
    nombre_curso VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    catequista_id INT,
    estado ENUM('activo', 'completado', 'cancelado') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de sacramentos
CREATE TABLE sacramentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('bautismo', 'comunion', 'confirmacion', 'matrimonio') NOT NULL,
    feligres_id INT NOT NULL,
    fecha_sacramento DATE,
    lugar VARCHAR(100),
    ministro VARCHAR(100),
    padrinos TEXT,
    observaciones TEXT,
    numero_acta VARCHAR(50),
    libro_bautismo VARCHAR(50),
    folio_bautismo VARCHAR(50),
    nombre_bautizado VARCHAR(200),
    fecha_nacimiento_bautizado DATE,
    lugar_nacimiento VARCHAR(200),
    nombre_padre VARCHAR(200),
    nombre_madre VARCHAR(200),
    nombre_padrino VARCHAR(200),
    nombre_madrina VARCHAR(200),
    observaciones_bautismo TEXT,
    estado ENUM('programado', 'realizado', 'cancelado') DEFAULT 'programado',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (feligres_id) REFERENCES feligreses(id)
);

-- Tabla de catequistas
CREATE TABLE catequistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) UNIQUE NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    email VARCHAR(100),
    especialidad ENUM('prebautismal', 'comunion', 'confirmacion', 'prematrimonial'),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de ministros
CREATE TABLE ministros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) UNIQUE NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    email VARCHAR(100),
    tipo ENUM('sacerdote', 'diacono', 'ministro', 'acolito'),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de parroquias
CREATE TABLE parroquias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    direccion TEXT,
    telefono VARCHAR(15),
    email VARCHAR(100),
    parroco_actual VARCHAR(100),
    fecha_fundacion DATE,
    estado ENUM('activa', 'inactiva') DEFAULT 'activa',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de pagos
CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    feligres_id INT,
    concepto ENUM('diezmo', 'ofrenda', 'sacramento', 'catequesis', 'otros'),
    monto DECIMAL(10,2) NOT NULL,
    fecha_pago DATE,
    metodo_pago ENUM('efectivo', 'transferencia', 'tarjeta'),
    observaciones TEXT,
    estado ENUM('pendiente', 'completado', 'cancelado') DEFAULT 'completado',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (feligres_id) REFERENCES feligreses(id)
);

-- Insertar usuario administrador por defecto (contraseña: "password")
INSERT INTO usuarios (username, password, nombre_completo, rol) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador Principal', 'admin');

-- Insertar datos de ejemplo
INSERT INTO feligreses (cedula, nombres, apellidos, fecha_nacimiento, telefono, email, direccion, estado_civil) VALUES
('001-1234567-8', 'María', 'González Pérez', '1985-03-15', '809-123-4567', 'maria@email.com', 'Calle Principal #123, Santo Domingo', 'casado'),
('002-7654321-9', 'Carlos', 'Rodríguez Santos', '1990-07-22', '809-234-5678', 'carlos@email.com', 'Av. Libertad #456, Santiago', 'soltero'),
('003-1122334-5', 'Ana', 'Martínez López', '1978-11-30', '809-345-6789', 'ana@email.com', 'Calle 5 #789, La Vega', 'divorciado');

INSERT INTO catequistas (cedula, nombres, apellidos, telefono, email, especialidad) VALUES
('004-5566778-9', 'Laura', 'Hernández García', '809-456-7890', 'laura@email.com', 'comunion'),
('005-9988776-5', 'Pedro', 'Sánchez Jiménez', '809-567-8901', 'pedro@email.com', 'confirmacion');

INSERT INTO ministros (cedula, nombres, apellidos, telefono, email, tipo) VALUES
('006-4433221-1', 'Padre José', 'Ramírez Méndez', '809-678-9012', 'padrejose@email.com', 'sacerdote'),
('007-3344556-7', 'Diácono Miguel', 'Torres Reyes', '809-789-0123', 'miguel@email.com', 'diacono');

INSERT INTO parroquias (nombre, direccion, telefono, email, parroco_actual, fecha_fundacion) VALUES
('Parroquia San Juan Bautista', 'Av. Central #100, Santo Domingo Este', '809-800-1000', 'info@sanjuan.com', 'Padre José Ramírez Méndez', '1950-05-15'),
('Parroquia Santa María', 'Calle Duarte #200, Santiago de los Caballeros', '809-800-2000', 'info@santamaria.com', 'Padre Luis García', '1960-08-20');

INSERT INTO catequesis (tipo, nombre_curso, descripcion, fecha_inicio, fecha_fin, catequista_id) VALUES
('comunion', 'Primera Comunión 2024', 'Curso de preparación para primera comunión para niños de 8-10 años', '2024-01-15', '2024-06-15', 1),
('confirmacion', 'Confirmación Juvenil 2024', 'Curso de confirmación para jóvenes de 14-18 años', '2024-02-01', '2024-07-30', 2);

INSERT INTO sacramentos (tipo, feligres_id, fecha_sacramento, lugar, ministro, padrinos, numero_acta, libro_bautismo, folio_bautismo, nombre_bautizado, fecha_nacimiento_bautizado, lugar_nacimiento, nombre_padre, nombre_madre, nombre_padrino, nombre_madrina, observaciones_bautismo) VALUES
('bautismo', 1, '2024-03-20', 'Capilla San Juan', 'Padre José Ramírez', 'Juan Pérez y Marta Sánchez', 'B-2024-001', 'LB-15', 'F-123', 'María González Pérez', '1985-03-15', 'Santo Domingo, República Dominicana', 'Juan González', 'Ana Pérez', 'Carlos Rodríguez', 'Laura Hernández', 'Bautizado en la Capilla San Juan según el rito católico.'),
('comunion', 2, '2024-04-15', 'Iglesia Principal', 'Padre Luis García', 'Roberto Martínez', 'C-2024-001', 'LC-10', 'F-45', 'Carlos Rodríguez Santos', '1990-07-22', 'Santiago, República Dominicana', 'Roberto Rodríguez', 'María Santos', 'Pedro Sánchez', 'Ana Martínez', 'Primera comunión recibida según el rito católico.');

INSERT INTO pagos (feligres_id, concepto, monto, fecha_pago, metodo_pago) VALUES
(1, 'diezmo', 500.00, '2024-01-05', 'efectivo'),
(2, 'sacramento', 200.00, '2024-02-10', 'transferencia');