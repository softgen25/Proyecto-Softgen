-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-12-2024 a las 17:07:27
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `softgen`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `id_admin` int(50) NOT NULL,
  `nombre` text NOT NULL,
  `apellido` text NOT NULL,
  `n_documento` varchar(20) NOT NULL,
  `telefono` int(20) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`id_admin`, `nombre`, `apellido`, `n_documento`, `telefono`, `correo_electronico`) VALUES
(201, 'Edwin', 'Montenegro', '1011097480', 370729821, 'emontenegro@gmail.com'),
(202, 'Marí José', 'Mendoza', '1106227097', 300737142, 'mmendozagmail.com'),
(203, 'Juan', 'Gomez', '1094048008', 320458402, 'jgomez@gmail.com'),
(204, 'Harold', 'Peñaloza', '1015484614', 311458943, 'h@gmail.com'),
(205, 'Dajaryth', 'Hernandez', '1096803380', 320406972, 'dhernandez@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `n_documento` varchar(20) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nombre`, `apellido`, `n_documento`, `telefono`, `correo_electronico`) VALUES
(101, 'Juan ', 'Arias', '79953647', '3055493005', 'jarias@gmail.com'),
(102, 'Fatima', 'Becerra', '79049940', '3204078912', 'fbecerra@gmail.com'),
(103, 'George', 'Cuellar', '80345512', '3133953333', 'gcuellar@gmail.com'),
(104, 'Laura', 'Diaz', '90635267', '3018284530', 'ldiaz@gmail.com'),
(105, 'Luisa', 'Flores', '10185163524', '3177298707', 'lflorez@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos`
--

CREATE TABLE `datos` (
  `id_datos` int(50) NOT NULL,
  `informacion_general` varchar(100) NOT NULL,
  `informacion_equipo` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `lugar` varchar(100) NOT NULL,
  `cliente` varchar(100) NOT NULL,
  `tecnico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo`
--

CREATE TABLE `equipo` (
  `id_equipo` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `informacion_equipo` varchar(100) NOT NULL,
  `tipo_equipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inconformidad`
--

CREATE TABLE `inconformidad` (
  `id_inconformidad` int(11) NOT NULL,
  `incoformidad` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informe`
--

CREATE TABLE `informe` (
  `id_informe` int(50) NOT NULL,
  `tipo_informe` int(50) NOT NULL,
  `id_datos` int(50) NOT NULL,
  `id_tipo_equipo` int(50) NOT NULL,
  `Id_cliente` int(50) NOT NULL,
  `id_tecnico` int(50) NOT NULL,
  `informacion_limpieza` varchar(100) NOT NULL,
  `informacion_electrica` varchar(100) NOT NULL,
  `id_localidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `informe`
--

INSERT INTO `informe` (`id_informe`, `tipo_informe`, `id_datos`, `id_tipo_equipo`, `Id_cliente`, `id_tecnico`, `informacion_limpieza`, `informacion_electrica`, `id_localidad`) VALUES
(0, 10245, 235698, 10656, 10345698, 1015474614, 'turuturue', 'aja aja electrica', 1),
(1, 0, 0, 0, 0, 1015474614, '', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_roles` int(100) NOT NULL,
  `id_admin` int(100) NOT NULL,
  `id_cliente` int(100) NOT NULL,
  `id_tecnico` int(100) NOT NULL,
  `id_usuarios` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_usuario`
--

CREATE TABLE `rol_usuario` (
  `id_rol_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `contraseña` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `soporte`
--

CREATE TABLE `soporte` (
  `id_soporte` int(50) NOT NULL,
  `id_roles` int(50) NOT NULL,
  `usuarios` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tecnico`
--

CREATE TABLE `tecnico` (
  `id_tecnico` int(50) NOT NULL,
  `nombre` text NOT NULL,
  `apellido` text NOT NULL,
  `n_documento` int(20) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tecnico`
--

INSERT INTO `tecnico` (`id_tecnico`, `nombre`, `apellido`, `n_documento`, `telefono`, `correo_electronico`) VALUES
(301, 'Aydee', 'Bermudez', 1012377378, '31012912871', 'abermudez@gmail.com'),
(302, 'Marco', 'Buitrago', 1014287406, '3224567879', 'mnuitrago@gmail.com'),
(303, 'Johann', 'Torres', 1014291421, '3190029374', 'jtorres@gmail.com'),
(304, 'Nicolas', 'Pulido', 1018491721, '3112299345', 'mpulido@gmail.com'),
(305, 'Jaime', 'Pulido', 11187866, '3115333788', 'jpulido@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion`
--

CREATE TABLE `ubicacion` (
  `id_ubicacion` int(11) NOT NULL,
  `departamento` varchar(100) NOT NULL,
  `municipio_ciudad` varchar(100) NOT NULL,
  `barrio` varchar(100) NOT NULL,
  `calle_carrera` varchar(100) NOT NULL,
  `numero` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubicacion`
--

INSERT INTO `ubicacion` (`id_ubicacion`, `departamento`, `municipio_ciudad`, `barrio`, `calle_carrera`, `numero`) VALUES
(1, 'Bogota', 'Bogota', 'La estrella', '3', '7'),
(2, 'Caldas', 'Manizales', 'El resoro', '15', '99'),
(3, 'Antioquia', 'Medellin', 'Comuna 13', '6', '101'),
(4, 'Bolivar', 'Cartagena', 'El centro', '14', '24'),
(5, 'Amazonas', 'Leticia', 'Puerto', '99', '57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuarios` int(11) NOT NULL,
  `id_iniciar_sesion` int(11) NOT NULL,
  `id_soporte` int(11) NOT NULL,
  `id_informe` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `apellido` text NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `rol` varchar(20) NOT NULL,
  `rol_usuario` varchar(20) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `datos`
--
ALTER TABLE `datos`
  ADD PRIMARY KEY (`id_datos`);

--
-- Indices de la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD PRIMARY KEY (`id_equipo`);

--
-- Indices de la tabla `inconformidad`
--
ALTER TABLE `inconformidad`
  ADD PRIMARY KEY (`id_inconformidad`);

--
-- Indices de la tabla `informe`
--
ALTER TABLE `informe`
  ADD PRIMARY KEY (`id_informe`),
  ADD KEY `tipo_informe` (`tipo_informe`),
  ADD KEY `id_datos` (`id_datos`),
  ADD KEY `id_tipo_equipo` (`id_tipo_equipo`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_roles`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_tecnicos` (`id_tecnico`),
  ADD KEY `id_usuarios` (`id_usuarios`);

--
-- Indices de la tabla `rol_usuario`
--
ALTER TABLE `rol_usuario`
  ADD PRIMARY KEY (`id_rol_usuario`);

--
-- Indices de la tabla `soporte`
--
ALTER TABLE `soporte`
  ADD PRIMARY KEY (`id_soporte`);

--
-- Indices de la tabla `tecnico`
--
ALTER TABLE `tecnico`
  ADD PRIMARY KEY (`id_tecnico`);

--
-- Indices de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD PRIMARY KEY (`id_ubicacion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuarios`),
  ADD KEY `id_iniciar_sesion` (`id_iniciar_sesion`),
  ADD KEY `id_soporte` (`id_soporte`),
  ADD KEY `id_informe` (`id_informe`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id_admin` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1015474616;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1068453217;

--
-- AUTO_INCREMENT de la tabla `datos`
--
ALTER TABLE `datos`
  MODIFY `id_datos` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `equipo`
--
ALTER TABLE `equipo`
  MODIFY `id_equipo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inconformidad`
--
ALTER TABLE `inconformidad`
  MODIFY `id_inconformidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `informe`
--
ALTER TABLE `informe`
  MODIFY `id_informe` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1025;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_roles` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol_usuario`
--
ALTER TABLE `rol_usuario`
  MODIFY `id_rol_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `soporte`
--
ALTER TABLE `soporte`
  MODIFY `id_soporte` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tecnico`
--
ALTER TABLE `tecnico`
  MODIFY `id_tecnico` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=306;

--
-- AUTO_INCREMENT de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  MODIFY `id_ubicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuarios` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
