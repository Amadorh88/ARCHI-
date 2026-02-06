-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-02-2026 a las 14:49:51
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `v2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id_actividad` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `accion` varchar(255) NOT NULL,
  `modulo` varchar(100) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bautismo`
--

CREATE TABLE `bautismo` (
  `id_bautismo` int(11) NOT NULL,
  `registro` varchar(50) DEFAULT NULL,
  `id_feligres` int(11) DEFAULT NULL,
  `id_padrino` int(11) DEFAULT NULL,
  `id_madrina` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_ministro` int(11) DEFAULT NULL,
  `id_parroquia` int(11) DEFAULT NULL,
  `libro` varchar(50) DEFAULT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `folio` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bautismo`
--

INSERT INTO `bautismo` (`id_bautismo`, `registro`, `id_feligres`, `id_padrino`, `id_madrina`, `fecha`, `id_ministro`, `id_parroquia`, `libro`, `numero`, `folio`) VALUES
(1, 'B001', 1, 3, 4, '1997-06-15', 1, 1, 'Bautismos-1', '001', '001');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catequesis`
--

CREATE TABLE `catequesis` (
  `id_catequesis` int(11) NOT NULL,
  `id_feligres` int(11) DEFAULT NULL,
  `nombre_catequesis` varchar(150) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `id_parroquia` int(11) DEFAULT NULL,
  `tipo` enum('Pre-bautismal','Primera comunión','Confirmación','Matrimonial') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `catequesis`
--

INSERT INTO `catequesis` (`id_catequesis`, `id_feligres`, `nombre_catequesis`, `id_curso`, `id_parroquia`, `tipo`) VALUES
(1, 1, 'Catequesis de Primera Comunión', 1, 1, 'Primera comunión'),
(2, 2, 'Catequesis Pre-matrimonial', 2, 2, 'Matrimonial');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catequista`
--

CREATE TABLE `catequista` (
  `id_catequista` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `catequista`
--

INSERT INTO `catequista` (`id_catequista`, `nombre`, `telefono`, `especialidad`) VALUES
(1, 'María Fernández', '600-555-666', 'Catequesis infantil'),
(2, 'Roberto García', '600-777-888', 'Catequesis matrimonial');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunion`
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
-- Volcado de datos para la tabla `comunion`
--

INSERT INTO `comunion` (`id_comunion`, `registro`, `id_feligres`, `fecha`, `id_ministro`, `id_parroquia`) VALUES
(1, 'C001', 1, '2009-05-20', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `confirmacion`
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
-- Volcado de datos para la tabla `confirmacion`
--

INSERT INTO `confirmacion` (`id_confirmacion`, `registro`, `id_feligres`, `fecha`, `id_ministro`, `id_parroquia`) VALUES
(1, 'CONF001', 2, '2014-06-30', 2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `id_curso` int(11) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `duracion` varchar(50) DEFAULT NULL,
  `id_catequista` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`id_curso`, `nombre`, `duracion`, `id_catequista`, `observaciones`) VALUES
(1, 'Curso de Primera Comunión', '6 meses', 1, 'Niños entre 8 y 12 años'),
(2, 'Curso Pre-matrimonial', '3 meses', 2, 'Parejas que van a contraer matrimonio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `feligres`
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
-- Volcado de datos para la tabla `feligres`
--

INSERT INTO `feligres` (`id_feligres`, `nombre_completo`, `nombre_padre`, `nombre_madre`, `fecha_nacimiento`, `lugar_nacimiento`) VALUES
(1, 'Amador BATAPA EPAM', 'Martín BATAPA PACHONSI', 'Gertrudis EPAM BEAKÁ', '1996-12-04', 'Malabo'),
(2, 'Ana Gómez Ruiz', 'Pedro Gómez', 'Lucía Ruiz', '1998-03-22', 'Ciudad B'),
(3, 'Miguel Torres', NULL, NULL, NULL, NULL),
(4, 'Laura Díaz', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matrimonio`
--

CREATE TABLE `matrimonio` (
  `id_matrimonio` int(11) NOT NULL,
  `registro` varchar(50) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_ministro` int(11) DEFAULT NULL,
  `lugar` varchar(150) DEFAULT NULL,
  `libro` varchar(50) DEFAULT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `folio` varchar(50) DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `matrimonio`
--

INSERT INTO `matrimonio` (`id_matrimonio`, `registro`, `fecha`, `id_ministro`, `lugar`, `libro`, `numero`, `folio`, `estado`) VALUES
(1, 'M001', '2022-08-15', 1, 'Parroquia Santa María', 'Matrimonios-1', '001', '001', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matrimonio_feligres`
--

CREATE TABLE `matrimonio_feligres` (
  `id_matrimonio_feligres` int(11) NOT NULL,
  `id_matrimonio` int(11) NOT NULL,
  `id_feligres` int(11) NOT NULL,
  `rol` enum('esposo','esposa','testigo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `matrimonio_feligres`
--

INSERT INTO `matrimonio_feligres` (`id_matrimonio_feligres`, `id_matrimonio`, `id_feligres`, `rol`) VALUES
(1, 1, 2, 'esposa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ministros`
--

CREATE TABLE `ministros` (
  `id_ministro` int(11) NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `DIP` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `tipo` enum('Sacerdote','Diácono','Obispo','Catequista') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ministros`
--

INSERT INTO `ministros` (`id_ministro`, `nombre_completo`, `DIP`, `telefono`, `tipo`) VALUES
(1, 'Padre José Martínez', 'DIP12345', '600-111-222', 'Sacerdote'),
(2, 'Diácono Luis Herrera', 'DIP67890', '600-333-444', 'Diácono');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
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
-- Volcado de datos para la tabla `pago`
--

INSERT INTO `pago` (`id_pago`, `concepto`, `cantidad`, `recibido`, `cambio`, `id_feligres`) VALUES
(1, 'Matrícula catequesis', 50.00, 50.00, 0.00, 1),
(2, 'Donación sacramento', 100.00, 100.00, 0.00, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parroquia`
--

CREATE TABLE `parroquia` (
  `id_parroquia` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `parroquia`
--

INSERT INTO `parroquia` (`id_parroquia`, `nombre`, `direccion`, `telefono`) VALUES
(1, 'Inmaculado Corazón de María', 'Avda de la Independencia', '222-111-333'),
(2, 'Parroquia Santa María', 'Av. Central 456', '222-222-444');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sacramento`
--

CREATE TABLE `sacramento` (
  `id_sacramento` int(11) NOT NULL,
  `nombre` enum('Bautismo','Comunión','Confirmación','Matrimonio') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
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
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `dni`, `usuario`, `contraseña`, `rol`, `estado`, `fecha_registro`) VALUES
(1, 'Amador Batapa', '0001457896', 'admin', '$2y$10$rOEIJPa2eBlWq.ztzpCzweP5KFoHD5V3wXqGtv1o0p8Jg6EEShzwu', 'admin', 1, '2025-06-07 08:00:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id_actividad`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `bautismo`
--
ALTER TABLE `bautismo`
  ADD KEY `bautismo_ibfk_1` (`id_feligres`),
  ADD KEY `bautismo_ibfk_2` (`id_ministro`),
  ADD KEY `bautismo_ibfk_3` (`id_parroquia`),
  ADD KEY `bautismo_ibfk_4` (`id_padrino`),
  ADD KEY `bautismo_ibfk_5` (`id_madrina`);

--
-- Indices de la tabla `catequesis`
--
ALTER TABLE `catequesis`
  ADD PRIMARY KEY (`id_catequesis`),
  ADD KEY `id_feligres` (`id_feligres`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_parroquia` (`id_parroquia`);

--
-- Indices de la tabla `catequista`
--
ALTER TABLE `catequista`
  ADD PRIMARY KEY (`id_catequista`);

--
-- Indices de la tabla `comunion`
--
ALTER TABLE `comunion`
  ADD PRIMARY KEY (`id_comunion`),
  ADD KEY `id_feligres` (`id_feligres`),
  ADD KEY `id_ministro` (`id_ministro`),
  ADD KEY `id_parroquia` (`id_parroquia`);

--
-- Indices de la tabla `confirmacion`
--
ALTER TABLE `confirmacion`
  ADD PRIMARY KEY (`id_confirmacion`),
  ADD KEY `id_feligres` (`id_feligres`),
  ADD KEY `id_ministro` (`id_ministro`),
  ADD KEY `id_parroquia` (`id_parroquia`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `id_catequista` (`id_catequista`);

--
-- Indices de la tabla `feligres`
--
ALTER TABLE `feligres`
  ADD PRIMARY KEY (`id_feligres`);

--
-- Indices de la tabla `matrimonio`
--
ALTER TABLE `matrimonio`
  ADD PRIMARY KEY (`id_matrimonio`),
  ADD KEY `id_ministro` (`id_ministro`);

--
-- Indices de la tabla `matrimonio_feligres`
--
ALTER TABLE `matrimonio_feligres`
  ADD PRIMARY KEY (`id_matrimonio_feligres`),
  ADD UNIQUE KEY `uk_matrimonio_persona` (`id_matrimonio`,`id_feligres`),
  ADD KEY `fk_mf_feligres` (`id_feligres`);

--
-- Indices de la tabla `ministros`
--
ALTER TABLE `ministros`
  ADD PRIMARY KEY (`id_ministro`),
  ADD UNIQUE KEY `DIP` (`DIP`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_feligres` (`id_feligres`);

--
-- Indices de la tabla `parroquia`
--
ALTER TABLE `parroquia`
  ADD PRIMARY KEY (`id_parroquia`);

--
-- Indices de la tabla `sacramento`
--
ALTER TABLE `sacramento`
  ADD PRIMARY KEY (`id_sacramento`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id_actividad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `actividades_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `bautismo`
--
ALTER TABLE `bautismo`
  ADD CONSTRAINT `bautismo_ibfk_1` FOREIGN KEY (`id_feligres`) REFERENCES `feligres` (`id_feligres`),
  ADD CONSTRAINT `bautismo_ibfk_2` FOREIGN KEY (`id_ministro`) REFERENCES `ministros` (`id_ministro`),
  ADD CONSTRAINT `bautismo_ibfk_3` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquia` (`id_parroquia`),
  ADD CONSTRAINT `bautismo_ibfk_4` FOREIGN KEY (`id_padrino`) REFERENCES `feligres` (`id_feligres`),
  ADD CONSTRAINT `bautismo_ibfk_5` FOREIGN KEY (`id_madrina`) REFERENCES `feligres` (`id_feligres`);

--
-- Filtros para la tabla `catequesis`
--
ALTER TABLE `catequesis`
  ADD CONSTRAINT `catequesis_ibfk_1` FOREIGN KEY (`id_feligres`) REFERENCES `feligres` (`id_feligres`),
  ADD CONSTRAINT `catequesis_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`),
  ADD CONSTRAINT `catequesis_ibfk_3` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquia` (`id_parroquia`);

--
-- Filtros para la tabla `comunion`
--
ALTER TABLE `comunion`
  ADD CONSTRAINT `comunion_ibfk_1` FOREIGN KEY (`id_feligres`) REFERENCES `feligres` (`id_feligres`),
  ADD CONSTRAINT `comunion_ibfk_2` FOREIGN KEY (`id_ministro`) REFERENCES `ministros` (`id_ministro`),
  ADD CONSTRAINT `comunion_ibfk_3` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquia` (`id_parroquia`);

--
-- Filtros para la tabla `confirmacion`
--
ALTER TABLE `confirmacion`
  ADD CONSTRAINT `confirmacion_ibfk_1` FOREIGN KEY (`id_feligres`) REFERENCES `feligres` (`id_feligres`),
  ADD CONSTRAINT `confirmacion_ibfk_2` FOREIGN KEY (`id_ministro`) REFERENCES `ministros` (`id_ministro`),
  ADD CONSTRAINT `confirmacion_ibfk_3` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquia` (`id_parroquia`);

--
-- Filtros para la tabla `curso`
--
ALTER TABLE `curso`
  ADD CONSTRAINT `curso_ibfk_1` FOREIGN KEY (`id_catequista`) REFERENCES `catequista` (`id_catequista`);

--
-- Filtros para la tabla `matrimonio`
--
ALTER TABLE `matrimonio`
  ADD CONSTRAINT `matrimonio_ibfk_2` FOREIGN KEY (`id_ministro`) REFERENCES `ministros` (`id_ministro`);

--
-- Filtros para la tabla `matrimonio_feligres`
--
ALTER TABLE `matrimonio_feligres`
  ADD CONSTRAINT `fk_mf_feligres` FOREIGN KEY (`id_feligres`) REFERENCES `feligres` (`id_feligres`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mf_matrimonio` FOREIGN KEY (`id_matrimonio`) REFERENCES `matrimonio` (`id_matrimonio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`id_feligres`) REFERENCES `feligres` (`id_feligres`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
