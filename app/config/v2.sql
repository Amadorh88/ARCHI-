-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2026 at 04:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `actividades`
--

CREATE TABLE `actividades` (
  `id_actividad` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `accion` varchar(255) NOT NULL,
  `modulo` varchar(100) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `actividades`
--

INSERT INTO `actividades` (`id_actividad`, `id_usuario`, `accion`, `modulo`, `fecha`, `ip`) VALUES
(1, 1, 'Cerró sesión en el sistema', 'Login', '2025-11-19 06:35:57', '::1'),
(2, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 14:09:51', '::1'),
(3, 1, 'Cerró sesión en el sistema', 'Login', '2025-11-19 14:10:47', '::1'),
(4, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 14:11:19', '::1'),
(5, 1, 'Cerró sesión en el sistema', 'Login', '2025-11-19 14:13:59', '::1'),
(6, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 14:14:11', '::1'),
(7, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:21:57', '::1'),
(8, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:22:07', '::1'),
(9, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:25:00', '::1'),
(10, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:26:16', '::1'),
(11, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:49:35', '::1'),
(12, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:50:57', '::1'),
(13, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:52:11', '::1'),
(14, 1, 'Cerró sesión en el sistema', 'Login', '2025-11-19 19:56:33', '::1'),
(15, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:56:40', '::1'),
(16, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:59:05', '::1'),
(17, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 20:01:39', '::1'),
(18, 1, 'Cerró sesión en el sistema', 'Login', '2025-11-19 20:04:21', '::1'),
(19, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 20:04:28', '::1'),
(20, 1, 'Cerró sesión en el sistema', 'Login', '2025-11-19 20:06:24', '::1'),
(21, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 20:06:34', '::1'),
(22, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 20:18:28', '::1'),
(23, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 20:22:54', '::1'),
(24, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 20:23:54', '::1'),
(25, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 20:24:59', '::1'),
(26, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 20:26:28', '::1'),
(27, 1, 'Inició sesión en el sistema', 'Login', '2025-11-20 06:20:29', '::1'),
(28, 1, 'Inició sesión en el sistema', 'Login', '2025-11-21 06:29:39', '::1'),
(29, 1, 'Inició sesión en el sistema', 'Login', '2025-11-21 06:31:07', '::1'),
(30, 1, 'Inició sesión en el sistema', 'Login', '2025-11-21 06:31:49', '::1'),
(31, 1, 'Inició sesión en el sistema', 'Login', '2025-11-21 11:20:29', '::1'),
(32, 1, 'Inició sesión en el sistema', 'Login', '2025-11-27 06:38:52', '::1'),
(33, 1, 'Inició sesión en el sistema', 'Login', '2025-11-28 06:29:28', '::1'),
(34, 1, 'Inició sesión en el sistema', 'Login', '2026-01-30 12:27:51', '::1'),
(35, 1, 'Inició sesión en el sistema', 'Login', '2026-01-22 22:44:38', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `bautismo`
--

CREATE TABLE `bautismo` (
  `id_bautismo` int(11) NOT NULL,
  `registro` varchar(50) DEFAULT NULL,
  `id_feligres` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `padrino` varchar(150) DEFAULT NULL,
  `madrina` varchar(150) DEFAULT NULL,
  `id_ministro` int(11) DEFAULT NULL,
  `id_parroquia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bautismo`
--

INSERT INTO `bautismo` (`id_bautismo`, `registro`, `id_feligres`, `fecha`, `padrino`, `madrina`, `id_ministro`, `id_parroquia`) VALUES
(1, 'B001', 1, '1997-06-15', 'Miguel Torres', 'Laura Díaz', 1, 1),
(2, 'Mtg-145', 4, '2026-01-30', NULL, NULL, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `catequesis`
--

CREATE TABLE `catequesis` (
  `id_catequesis` int(11) NOT NULL,
  `id_feligres` int(11) DEFAULT NULL,
  `nombre_catequesis` varchar(150) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `id_parroquia` int(11) DEFAULT NULL,
  `id_periodo` int(11) DEFAULT NULL,
  `tipo` enum('Pre-bautismal','Primera comunión','Confirmación','Matrimonial') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `catequesis`
--

INSERT INTO `catequesis` (`id_catequesis`, `id_feligres`, `nombre_catequesis`, `id_curso`, `id_parroquia`, `id_periodo`, `tipo`) VALUES
(1, 1, 'Catequesis de Primera Comunión', 1, 1, NULL, 'Primera comunión'),
(2, 2, 'Catequesis Pre-matrimonial', 2, 2, NULL, 'Matrimonial');

-- --------------------------------------------------------

--
-- Table structure for table `catequista`
--

CREATE TABLE `catequista` (
  `id_catequista` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `catequista`
--

INSERT INTO `catequista` (`id_catequista`, `nombre`, `telefono`, `especialidad`) VALUES
(1, 'María Fernández', '600-555-666', 'Catequesis infantil'),
(2, 'Roberto García', '600-777-888', 'Catequesis matrimonial'),
(3, 'pedro tantambar', '55120456', 'In. Informática');

-- --------------------------------------------------------

--
-- Table structure for table `comunion`
--

CREATE TABLE `comunion` (
  `id_comunion` int(11) NOT NULL,
  `registro` varchar(50) DEFAULT NULL,
  `id_feligres` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_ministro` int(11) DEFAULT NULL,
  `id_parroquia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comunion`
--

INSERT INTO `comunion` (`id_comunion`, `registro`, `id_feligres`, `fecha`, `id_ministro`, `id_parroquia`) VALUES
(1, 'C001', 1, '2009-05-20', 1, 1),
(2, 'Ch-52147', 3, '2025-12-29', 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `confirmacion`
--

CREATE TABLE `confirmacion` (
  `id_confirmacion` int(11) NOT NULL,
  `registro` varchar(50) DEFAULT NULL,
  `id_feligres` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_ministro` int(11) DEFAULT NULL,
  `id_parroquia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `confirmacion`
--

INSERT INTO `confirmacion` (`id_confirmacion`, `registro`, `id_feligres`, `fecha`, `id_ministro`, `id_parroquia`) VALUES
(1, 'CONF001', 2, '2014-06-30', 2, 2),
(2, 'Mpp-145', 4, '2026-01-06', 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `curso`
--

CREATE TABLE `curso` (
  `id_curso` int(11) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `duracion` varchar(50) DEFAULT NULL,
  `id_catequista` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curso`
--

INSERT INTO `curso` (`id_curso`, `nombre`, `duracion`, `id_catequista`, `observaciones`) VALUES
(1, 'Curso de Primera Comunión', '6 meses', 1, 'Niños entre 8 y 12 años'),
(2, 'Curso Pre-matrimonial', '3 meses', 2, 'Parejas que van a contraer matrimonio');

-- --------------------------------------------------------

--
-- Table structure for table `feligres`
--

CREATE TABLE `feligres` (
  `id_feligres` int(11) NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `nombre_padre` varchar(150) DEFAULT NULL,
  `nombre_madre` varchar(150) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `lugar_nacimiento` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feligres`
--

INSERT INTO `feligres` (`id_feligres`, `nombre_completo`, `nombre_padre`, `nombre_madre`, `fecha_nacimiento`, `lugar_nacimiento`) VALUES
(1, 'Amador BATAPA EPAM', 'Martín BATAPA PACHONSI', 'Gertrudis EPAM BEAKÁ', '1996-12-04', 'Malabo'),
(2, 'Ana Gómez Ruiz', 'Pedro Gómez', 'Lucía Ruiz', '1998-03-22', 'Ciudad B'),
(3, 'Melania Sima', 'mariano kastro', 'Marta Losa', '2024-02-15', 'Malabo'),
(4, 'Melania kastro', 'mariano kastro', 'Marta Losa', '2025-12-31', 'Malabo'),
(5, 'Petra Cataña', 'Nestor Castaña ', 'Lilia Beña', '2021-06-09', 'Pale');

-- --------------------------------------------------------

--
-- Table structure for table `matrimonio`
--

CREATE TABLE `matrimonio` (
  `id_matrimonio` int(11) NOT NULL,
  `registro` varchar(50) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_ministro` int(11) DEFAULT NULL,
  `lugar` varchar(150) DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matrimonio`
--

INSERT INTO `matrimonio` (`id_matrimonio`, `registro`, `fecha`, `id_ministro`, `lugar`, `estado`) VALUES
(4, 'Mpp-145', '2026-01-28', 1, 'Malabo', 'activo');

-- --------------------------------------------------------

--
-- Table structure for table `matrimonio_feligres`
--

CREATE TABLE `matrimonio_feligres` (
  `id_matrimonio_feligres` int(11) NOT NULL,
  `id_matrimonio` int(11) NOT NULL,
  `id_feligres` int(11) NOT NULL,
  `rol` enum('esposo','esposa','testigo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `matrimonio_feligres`
--

INSERT INTO `matrimonio_feligres` (`id_matrimonio_feligres`, `id_matrimonio`, `id_feligres`, `rol`) VALUES
(2, 4, 1, 'esposo'),
(3, 4, 3, 'esposa'),
(4, 4, 2, 'testigo'),
(5, 4, 4, 'testigo');

-- --------------------------------------------------------

--
-- Table structure for table `ministros`
--

CREATE TABLE `ministros` (
  `id_ministro` int(11) NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `DIP` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `tipo` enum('Sacerdote','Diácono','Obispo','Catequista') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ministros`
--

INSERT INTO `ministros` (`id_ministro`, `nombre_completo`, `DIP`, `telefono`, `tipo`) VALUES
(1, 'Padre José Martínez', 'DIP12345', '600-111-222', 'Sacerdote'),
(2, 'Diácono Luis Herrera', 'DIP67890', '600-333-444', 'Diácono'),
(3, 'Donaciano', NULL, '222001122', 'Obispo'),
(4, 'Pablo Sipako', '1014587', '222001122', 'Catequista');

-- --------------------------------------------------------

--
-- Table structure for table `pago`
--

CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL,
  `concepto` varchar(150) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `recibido` decimal(10,2) DEFAULT NULL,
  `cambio` decimal(10,2) DEFAULT NULL,
  `id_feligres` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pago`
--

INSERT INTO `pago` (`id_pago`, `concepto`, `cantidad`, `recibido`, `cambio`, `id_feligres`) VALUES
(1, 'Matrícula catequesis', 50.00, 50.00, 0.00, 1),
(2, 'Donación sacramento', 100.00, 100.00, 0.00, 2),
(3, 'bautismo', 12000.00, 12000.00, 0.00, 2);

-- --------------------------------------------------------

--
-- Table structure for table `parroquia`
--

CREATE TABLE `parroquia` (
  `id_parroquia` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parroquia`
--

INSERT INTO `parroquia` (`id_parroquia`, `nombre`, `direccion`, `telefono`) VALUES
(1, 'Inmaculado Corazón de María', 'Avda de la Independencia', '222-111-333'),
(2, 'Parroquia Santa María', 'Av. Central 456', '222-222-444'),
(3, 'Santander', 'Malabo', '222001122');

-- --------------------------------------------------------

--
-- Table structure for table `periodo`
--

CREATE TABLE `periodo` (
  `id_periodo` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('activo','finalizado') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sacramento`
--

CREATE TABLE `sacramento` (
  `id_sacramento` int(11) NOT NULL,
  `nombre` enum('Bautismo','Comunión','Confirmación','Matrimonio') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `dni` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('admin','secretario','archivista','parroco') NOT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `dni`, `usuario`, `contraseña`, `rol`, `estado`, `fecha_registro`) VALUES
(1, 'Amador Batapa', '0001457896', 'admin', '$2y$10$rOEIJPa2eBlWq.ztzpCzweP5KFoHD5V3wXqGtv1o0p8Jg6EEShzwu', 'admin', 1, '2025-06-07 08:37:10'),
(5, 'marcos Aurelia', '000000000', 'archivista', '123456', 'archivista', 0, '2026-02-06 22:57:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id_actividad`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `bautismo`
--
ALTER TABLE `bautismo`
  ADD PRIMARY KEY (`id_bautismo`),
  ADD KEY `id_feligres` (`id_feligres`),
  ADD KEY `id_ministro` (`id_ministro`),
  ADD KEY `id_parroquia` (`id_parroquia`);

--
-- Indexes for table `catequesis`
--
ALTER TABLE `catequesis`
  ADD PRIMARY KEY (`id_catequesis`),
  ADD KEY `id_feligres` (`id_feligres`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_parroquia` (`id_parroquia`),
  ADD KEY `catequesis_ibfk_4` (`id_periodo`);

--
-- Indexes for table `catequista`
--
ALTER TABLE `catequista`
  ADD PRIMARY KEY (`id_catequista`);

--
-- Indexes for table `comunion`
--
ALTER TABLE `comunion`
  ADD PRIMARY KEY (`id_comunion`),
  ADD KEY `id_feligres` (`id_feligres`),
  ADD KEY `id_ministro` (`id_ministro`),
  ADD KEY `id_parroquia` (`id_parroquia`);

--
-- Indexes for table `confirmacion`
--
ALTER TABLE `confirmacion`
  ADD PRIMARY KEY (`id_confirmacion`),
  ADD KEY `id_feligres` (`id_feligres`),
  ADD KEY `id_ministro` (`id_ministro`),
  ADD KEY `id_parroquia` (`id_parroquia`);

--
-- Indexes for table `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `id_catequista` (`id_catequista`);

--
-- Indexes for table `feligres`
--
ALTER TABLE `feligres`
  ADD PRIMARY KEY (`id_feligres`);

--
-- Indexes for table `matrimonio`
--
ALTER TABLE `matrimonio`
  ADD PRIMARY KEY (`id_matrimonio`),
  ADD KEY `id_ministro` (`id_ministro`);

--
-- Indexes for table `matrimonio_feligres`
--
ALTER TABLE `matrimonio_feligres`
  ADD PRIMARY KEY (`id_matrimonio_feligres`),
  ADD UNIQUE KEY `uk_matrimonio_persona` (`id_matrimonio`,`id_feligres`),
  ADD KEY `fk_mf_feligres` (`id_feligres`);

--
-- Indexes for table `ministros`
--
ALTER TABLE `ministros`
  ADD PRIMARY KEY (`id_ministro`),
  ADD UNIQUE KEY `DIP` (`DIP`);

--
-- Indexes for table `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_feligres` (`id_feligres`);

--
-- Indexes for table `parroquia`
--
ALTER TABLE `parroquia`
  ADD PRIMARY KEY (`id_parroquia`);

--
-- Indexes for table `periodo`
--
ALTER TABLE `periodo`
  ADD PRIMARY KEY (`id_periodo`);

--
-- Indexes for table `sacramento`
--
ALTER TABLE `sacramento`
  ADD PRIMARY KEY (`id_sacramento`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id_actividad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `bautismo`
--
ALTER TABLE `bautismo`
  MODIFY `id_bautismo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `catequesis`
--
ALTER TABLE `catequesis`
  MODIFY `id_catequesis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `catequista`
--
ALTER TABLE `catequista`
  MODIFY `id_catequista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comunion`
--
ALTER TABLE `comunion`
  MODIFY `id_comunion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `confirmacion`
--
ALTER TABLE `confirmacion`
  MODIFY `id_confirmacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `curso`
--
ALTER TABLE `curso`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feligres`
--
ALTER TABLE `feligres`
  MODIFY `id_feligres` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `matrimonio`
--
ALTER TABLE `matrimonio`
  MODIFY `id_matrimonio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `matrimonio_feligres`
--
ALTER TABLE `matrimonio_feligres`
  MODIFY `id_matrimonio_feligres` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ministros`
--
ALTER TABLE `ministros`
  MODIFY `id_ministro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pago`
--
ALTER TABLE `pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `parroquia`
--
ALTER TABLE `parroquia`
  MODIFY `id_parroquia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `periodo`
--
ALTER TABLE `periodo`
  MODIFY `id_periodo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sacramento`
--
ALTER TABLE `sacramento`
  MODIFY `id_sacramento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `actividades_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `bautismo`
--
ALTER TABLE `bautismo`
  ADD CONSTRAINT `bautismo_ibfk_1` FOREIGN KEY (`id_feligres`) REFERENCES `feligres` (`id_feligres`),
  ADD CONSTRAINT `bautismo_ibfk_2` FOREIGN KEY (`id_ministro`) REFERENCES `ministros` (`id_ministro`),
  ADD CONSTRAINT `bautismo_ibfk_3` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquia` (`id_parroquia`);

--
-- Constraints for table `catequesis`
--
ALTER TABLE `catequesis`
  ADD CONSTRAINT `catequesis_ibfk_1` FOREIGN KEY (`id_feligres`) REFERENCES `feligres` (`id_feligres`),
  ADD CONSTRAINT `catequesis_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`),
  ADD CONSTRAINT `catequesis_ibfk_3` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquia` (`id_parroquia`),
  ADD CONSTRAINT `catequesis_ibfk_4` FOREIGN KEY (`id_periodo`) REFERENCES `periodo` (`id_periodo`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `comunion`
--
ALTER TABLE `comunion`
  ADD CONSTRAINT `comunion_ibfk_1` FOREIGN KEY (`id_feligres`) REFERENCES `feligres` (`id_feligres`),
  ADD CONSTRAINT `comunion_ibfk_2` FOREIGN KEY (`id_ministro`) REFERENCES `ministros` (`id_ministro`),
  ADD CONSTRAINT `comunion_ibfk_3` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquia` (`id_parroquia`);

--
-- Constraints for table `confirmacion`
--
ALTER TABLE `confirmacion`
  ADD CONSTRAINT `confirmacion_ibfk_1` FOREIGN KEY (`id_feligres`) REFERENCES `feligres` (`id_feligres`),
  ADD CONSTRAINT `confirmacion_ibfk_2` FOREIGN KEY (`id_ministro`) REFERENCES `ministros` (`id_ministro`),
  ADD CONSTRAINT `confirmacion_ibfk_3` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquia` (`id_parroquia`);

--
-- Constraints for table `curso`
--
ALTER TABLE `curso`
  ADD CONSTRAINT `curso_ibfk_1` FOREIGN KEY (`id_catequista`) REFERENCES `catequista` (`id_catequista`);

--
-- Constraints for table `matrimonio`
--
ALTER TABLE `matrimonio`
  ADD CONSTRAINT `matrimonio_ibfk_2` FOREIGN KEY (`id_ministro`) REFERENCES `ministros` (`id_ministro`);

--
-- Constraints for table `matrimonio_feligres`
--
ALTER TABLE `matrimonio_feligres`
  ADD CONSTRAINT `fk_mf_feligres` FOREIGN KEY (`id_feligres`) REFERENCES `feligres` (`id_feligres`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mf_matrimonio` FOREIGN KEY (`id_matrimonio`) REFERENCES `matrimonio` (`id_matrimonio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`id_feligres`) REFERENCES `feligres` (`id_feligres`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
