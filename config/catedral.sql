-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-06-2026 a las 05:14:37
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
-- Base de datos: `catedral`
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
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id_actividad`, `id_usuario`, `accion`, `modulo`, `fecha`) VALUES
(1, 1, 'Cerró sesión en el sistema', 'Login', '2025-11-19 06:35:57'),
(2, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 14:09:51'),
(3, 1, 'Cerró sesión en el sistema', 'Login', '2025-11-19 14:10:47'),
(4, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 14:11:19'),
(5, 1, 'Cerró sesión en el sistema', 'Login', '2025-11-19 14:13:59'),
(6, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 14:14:11'),
(7, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:21:57'),
(8, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:22:07'),
(9, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:25:00'),
(10, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:26:16'),
(11, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:49:35'),
(12, 1, 'Inició sesión en el sistema', 'Login', '2025-11-19 19:50:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bautismo`
--

CREATE TABLE `bautismo` (
  `id_bautismo` int(11) NOT NULL,
  `libro` text NOT NULL,
  `registro` varchar(50) DEFAULT NULL,
  `folio` int(6) DEFAULT NULL,
  `id_feligres` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `padrino` varchar(150) DEFAULT NULL,
  `madrina` varchar(150) DEFAULT NULL,
  `id_ministro` int(11) DEFAULT NULL,
  `id_parroquia` int(11) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bautismo`
--

INSERT INTO `bautismo` (`id_bautismo`, `libro`, `registro`, `folio`, `id_feligres`, `fecha`, `padrino`, `madrina`, `id_ministro`, `id_parroquia`, `estado`) VALUES
(25, '', 'BAU-1933', 0, 1, '1999-12-06', 'Metodio LOLA BATAPA', 'Clara MAY MASA', 4, 4, 1),
(26, '', 'BAU-3221', NULL, 25, '2020-09-12', 'Mateo ESTRADA ESONO', 'Guillermina KING NCHANA', 11, 1, 1),
(27, '', 'BAU-2477', NULL, 8, '2021-11-22', 'Rubén Esono ABESO NCHAMA', 'Honorina Akumu ABESO ASANGONO', 1, 8, 1),
(28, '', 'BAU-1606', NULL, 6, '2022-06-11', 'Eugeni Esono NDONG OYANA', 'Petra OKOCHA CAMARERO', 9, 4, 1),
(29, '', 'BAU-1270', NULL, 17, '1999-02-02', 'Mateo ESTRADA ESONO', 'Honorina Akumu ABESO ASANGONO', 11, 3, 1),
(30, '', 'BAU-4675', NULL, 7, '2022-03-05', 'Juan Antonio Ona ESONO ADA', 'Alba Bikie ESONO AVOMO', 9, 2, 1),
(31, '', 'BAU-6996', NULL, 20, '2025-05-12', 'Mateo ESTRADA ESONO', 'Paulina IDJABE BATAPA', 10, 2, 1),
(32, '', 'BAU-5660', NULL, 23, '2001-06-12', 'Martín Eko EWORO ADA', 'Anita Nchama ABESO MBASOGO', 7, 7, 1),
(33, '', 'BAU-3096', NULL, 12, '2009-01-01', 'Pepito SANCHEZ BLANCO', 'Pepa FERNANDEZ CÁCERES', 4, 8, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catequesis`
--

CREATE TABLE `catequesis` (
  `id_catequesis` int(11) NOT NULL,
  `id_feligres` int(11) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `id_curso` int(11) DEFAULT NULL,
  `id_parroquia` int(11) DEFAULT NULL,
  `id_periodo` int(11) DEFAULT NULL,
  `tipo` enum('Bautismal','Primera comunión','Confirmación','Matrimonial') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `catequesis`
--

INSERT INTO `catequesis` (`id_catequesis`, `id_feligres`, `estado`, `id_curso`, `id_parroquia`, `id_periodo`, `tipo`) VALUES
(10, 7, 0, 1, 1, 24, 'Primera comunión'),
(16, 8, 0, 1, 1, 16, 'Primera comunión'),
(17, 8, 0, 13, 1, 16, 'Confirmación');

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
(1, 'María Fernández', '600-555-666', 'Catequesis juvenil'),
(2, 'Marcelino BECOBA SIPELE', '222000017', 'Catequesis juvenil'),
(4, 'Luciano Belono', '222000000', 'General'),
(5, 'Marcelino Copariate', '222707723', 'Catequesis infantil'),
(7, 'Marcelino COBANCHE', '222 782345', 'Catequesis infantil');

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
  `id_parroquia` int(11) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comunion`
--

INSERT INTO `comunion` (`id_comunion`, `registro`, `id_feligres`, `fecha`, `id_ministro`, `id_parroquia`, `estado`) VALUES
(1, 'C001', 1, '2009-05-20', 1, 1, 1),
(4, 'COM-9943', 8, '2005-01-18', 5, 1, 1),
(5, 'COM-9292', 6, '2002-02-21', 7, 1, 1),
(6, 'COM-8039', 7, '2003-02-18', 8, 1, 1),
(7, 'COM-8208', 12, '2009-12-05', 11, 1, 1),
(8, 'COM-9907', 17, '2025-11-09', 6, 1, 1),
(10, 'COM-7419', 23, '2022-06-16', 6, 1, 1),
(11, 'COM-4808', 25, '2009-05-11', 9, 4, 1),
(12, 'COM-8975', 20, '2025-07-12', 11, 2, 1);

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
  `id_parroquia` int(11) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `confirmacion`
--

INSERT INTO `confirmacion` (`id_confirmacion`, `registro`, `id_feligres`, `fecha`, `id_ministro`, `id_parroquia`, `estado`) VALUES
(4, 'CON-4276', 8, '2022-12-22', 9, 1, 1),
(5, 'CON-5109', 1, '2023-05-12', 5, 1, 1),
(6, 'CON-7782', 6, '2006-10-12', 10, 1, 1),
(7, 'CON-1350', 7, '2009-09-22', 9, 1, 1),
(8, 'CON-5219', 12, '2010-12-02', 6, 1, 1),
(9, 'CON-3272', 17, '2022-10-12', 6, 1, 1),
(11, 'CON-5795', 23, '2024-07-30', 6, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `id_curso` int(11) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `duracion` varchar(50) DEFAULT NULL,
  `id_catequista` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`id_curso`, `nombre`, `duracion`, `id_catequista`) VALUES
(1, 'Primera Comunión I', '9 meses', 4),
(2, 'Pre-matrimonial', '3 meses', 2),
(3, 'Pre-bautismal', '3 días', 2),
(4, 'Confirmación', '9 meses', 4),
(5, 'Primera Comunión II', '9 meses', 4),
(13, 'confirmación 1', '9 meses', 4),
(16, 'Matrimonio', '3 meses', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `feligres`
--

CREATE TABLE `feligres` (
  `id_feligres` int(11) NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `genero` enum('Masculino','Femenino') NOT NULL,
  `nombre_padre` varchar(150) DEFAULT NULL,
  `nombre_madre` varchar(150) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `lugar_nacimiento` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `feligres`
--

INSERT INTO `feligres` (`id_feligres`, `nombre_completo`, `genero`, `nombre_padre`, `nombre_madre`, `fecha_nacimiento`, `lugar_nacimiento`) VALUES
(1, 'Amador BATAPA EPAM', 'Masculino', 'Martín BATAPA PACHONSI', 'Gertrudis EPAM BEAKÁ', '1996-12-04', 'Malabo'),
(6, 'Jaime EPATA EKO', 'Masculino', 'Martin ESAPA ELO', 'Claudia EKO LOPELO', '2001-06-09', 'Malabo'),
(7, 'Pepe BARRIL MOSO', 'Masculino', 'Martín BARRIL METE', 'Marta MOSO NCHASO', '2026-01-26', 'Malabo'),
(8, 'Rufina BECHIRO BATAPA', 'Femenino', 'Félix BECHIRO BUELE', 'Secundina BATAPA FAMBOY', '2004-12-31', 'Malabo'),
(12, 'Pepito Perez', 'Masculino', 'PEPO Perez', 'Perez', '2003-03-04', 'Mongomo'),
(17, 'Eva BLANCO CASTILLO', 'Femenino', 'Martín BLANCO TOMÉ', 'Martina CASTILLO MEDIANO', '2010-11-12', 'Malabo'),
(20, 'Marcos KING BATAPA', 'Masculino', 'Omar KING', 'Rosa BATAPA', '2015-05-05', 'Malabo'),
(23, 'Manuela Andeme ABESO NCHAMA', 'Femenino', 'Manuel Abeso ESONO NNANG', 'Faustina NCHAMA EYÍ ASANGONO', '2000-12-22', 'Bata'),
(25, 'Priscila TORRES BARRIL', 'Femenino', 'Andrés TORRES CASTILLO', 'Ana BARRIL TOMÉ', '2000-04-18', 'Malabo');

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
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `matrimonio`
--

INSERT INTO `matrimonio` (`id_matrimonio`, `registro`, `fecha`, `id_ministro`, `lugar`, `estado`) VALUES
(9, 'MAT-8309', '2025-02-14', 6, 'Parroquia Local', 'activo'),
(11, 'MAT-3590', '2023-02-14', 6, 'Parroquia Local', 'activo');

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
(18, 9, 6, 'esposo'),
(19, 9, 8, 'esposa'),
(20, 9, 17, 'testigo'),
(21, 9, 1, 'testigo'),
(26, 11, 7, 'esposo'),
(27, 11, 23, 'esposa'),
(28, 11, 1, 'testigo');

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
(1, 'Padre José Martínez', '000123045', '222-031-222', 'Sacerdote'),
(2, 'Diácono Luis Herrera', '000067890', '555-333-444', 'Diácono'),
(4, 'Pablo Sipako', '001014587', '222001122', 'Sacerdote'),
(5, 'Esteban PENDA', '000.123.123', '222542312', 'Sacerdote'),
(6, 'Benjamín BOSEPA BARILA', '000.133.112', '222091212', 'Sacerdote'),
(7, 'José Ndong Ada', '000123450', '222023432', 'Sacerdote'),
(8, 'Roberto OKON POCO', '000111213', '555123245', 'Sacerdote'),
(9, 'Manuel BECHIRO NCHASO', '000001234', '555181234', 'Sacerdote'),
(10, 'Tarsicio BECOBA TOBACHI', '000001278', '555000034', 'Sacerdote'),
(11, 'Juan OBIANG BECÁ', '000006278', '555040034', 'Sacerdote'),
(12, 'Pepe GANGOZO', '000212345', '222090801', 'Sacerdote');

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
  `id_feligres` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pago`
--

INSERT INTO `pago` (`id_pago`, `concepto`, `cantidad`, `recibido`, `cambio`, `id_feligres`, `fecha`) VALUES
(1, 'Matrícula catequesis', 4000.00, 4000.00, 0.00, 1, '2026-02-10 11:09:10'),
(4, 'Inscripción catequesis', 4000.00, 4000.00, 0.00, 7, '2026-02-10 11:15:50'),
(6, 'Matrícula catequesis', 4000.00, 4000.00, 0.00, 20, '2026-06-02 16:17:10');

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
(2, 'Sagrado corazón de Jesús', 'Campo Yaunde-Malabo', '222-222-444'),
(3, 'San Valentín apóstol', 'San Valentín- Malabo', '222013213'),
(4, 'San Fernando', 'Ela-Nguema-Malabo', '222000000'),
(5, 'San José', 'Banapá-Malabo', '222020012'),
(6, 'Maravillas de Jesús', 'Pérez-Malabo', '222001132'),
(7, 'Santuario Claret', 'Calle...-Malabo', '222456521'),
(8, 'Nuestra señora de Montserrat', 'Rebola', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodo`
--

CREATE TABLE `periodo` (
  `id_periodo` int(11) NOT NULL,
  `fecha_inicio` year(4) NOT NULL,
  `fecha_fin` year(4) NOT NULL,
  `estado` enum('activo','finalizado') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `periodo`
--

INSERT INTO `periodo` (`id_periodo`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(13, '2021', '2022', 'finalizado'),
(14, '2025', '2026', 'finalizado'),
(16, '2026', '2027', 'activo'),
(17, '2024', '2025', 'finalizado'),
(19, '2023', '2024', 'finalizado'),
(20, '2022', '2023', 'finalizado'),
(22, '2006', '2007', 'finalizado'),
(24, '2016', '2017', 'finalizado');

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
(1, 'Amador Batapa', '0001457896', 'admin', '$2y$10$rOEIJPa2eBlWq.ztzpCzweP5KFoHD5V3wXqGtv1o0p8Jg6EEShzwu', 'admin', 1, '2025-06-07 08:37:10'),
(5, 'Esteban PENDA', '000001324', 'parroco', '$2y$10$P93gjShcoOipQ1okC8a4LeCwwZ7kA3GMH2uwucuaUNZNruWxHjO/6', 'parroco', 1, '2026-02-06 22:57:23'),
(6, 'Pedro YAMBÁ', '0001457899', 'archivista', '$2y$10$kfTVGuHHl8vWlJew.YwdjO97xAJnyD379kYQBppOuW2TvSPG3MO2y', 'archivista', 1, '2026-02-10 14:43:26'),
(7, 'Rufina ANGONO', '0001457895', 'secretaria', '$2y$10$fovk4HspGqAidglA.FDtH.Yci.T29uOKC5y/UoJ/98staOtEcL1Xu', 'secretario', 1, '2026-02-10 14:47:39'),
(9, 'ADMINISTRADOR PRINCIPAL', '000.166.994', 'administrador', '$2y$10$P2srT7wxX07ePXibAkjnluNPpgX/NKMsIvwVr0nuXVLIny8peuMDq', 'admin', 1, '2026-06-16 16:44:59');

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
  ADD PRIMARY KEY (`id_bautismo`),
  ADD KEY `id_feligres` (`id_feligres`),
  ADD KEY `id_ministro` (`id_ministro`),
  ADD KEY `id_parroquia` (`id_parroquia`);

--
-- Indices de la tabla `catequesis`
--
ALTER TABLE `catequesis`
  ADD PRIMARY KEY (`id_catequesis`),
  ADD KEY `id_feligres` (`id_feligres`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_parroquia` (`id_parroquia`),
  ADD KEY `catequesis_ibfk_4` (`id_periodo`);

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
-- Indices de la tabla `periodo`
--
ALTER TABLE `periodo`
  ADD PRIMARY KEY (`id_periodo`),
  ADD UNIQUE KEY `unique_periodo` (`fecha_inicio`,`fecha_fin`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id_actividad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `bautismo`
--
ALTER TABLE `bautismo`
  MODIFY `id_bautismo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `catequesis`
--
ALTER TABLE `catequesis`
  MODIFY `id_catequesis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `catequista`
--
ALTER TABLE `catequista`
  MODIFY `id_catequista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `comunion`
--
ALTER TABLE `comunion`
  MODIFY `id_comunion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `confirmacion`
--
ALTER TABLE `confirmacion`
  MODIFY `id_confirmacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `feligres`
--
ALTER TABLE `feligres`
  MODIFY `id_feligres` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `matrimonio`
--
ALTER TABLE `matrimonio`
  MODIFY `id_matrimonio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `matrimonio_feligres`
--
ALTER TABLE `matrimonio_feligres`
  MODIFY `id_matrimonio_feligres` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `ministros`
--
ALTER TABLE `ministros`
  MODIFY `id_ministro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `parroquia`
--
ALTER TABLE `parroquia`
  MODIFY `id_parroquia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `periodo`
--
ALTER TABLE `periodo`
  MODIFY `id_periodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  ADD CONSTRAINT `bautismo_ibfk_3` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquia` (`id_parroquia`);

--
-- Filtros para la tabla `catequesis`
--
ALTER TABLE `catequesis`
  ADD CONSTRAINT `catequesis_ibfk_1` FOREIGN KEY (`id_feligres`) REFERENCES `feligres` (`id_feligres`),
  ADD CONSTRAINT `catequesis_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`),
  ADD CONSTRAINT `catequesis_ibfk_3` FOREIGN KEY (`id_parroquia`) REFERENCES `parroquia` (`id_parroquia`),
  ADD CONSTRAINT `catequesis_ibfk_4` FOREIGN KEY (`id_periodo`) REFERENCES `periodo` (`id_periodo`) ON DELETE SET NULL ON UPDATE CASCADE;

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
