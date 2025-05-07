-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-12-2024 a las 02:48:07
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
  `telefono` int(20) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`id_admin`, `nombre`, `apellido`, `telefono`, `correo_electronico`) VALUES
(2, 'Dajaryht', 'Herrandez', 315455825, 'dj654@gmail.com'),
(1015474614, 'Harold', 'Peñaloza', 255454655, 'harold-p-26@hotmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL,
  `Dirección` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nombre`, `apellido`, `telefono`, `correo_electronico`, `Dirección`) VALUES
(3, 'Sofia', 'Alvares', '3197855254', 'sofiamamona@hotmail.com', '0'),
(4, 'Gabriel ', 'Chacon', '3125528798', 'gabriel@gmail.com', '0'),
(104673855, 'Juan', 'Tellez', '3214698725', 'tellez@gmail.com', 'calle 39 N3'),
(1032459687, 'Maria', 'Perez', '3214568576', 'maRIA@GMAIL.COM', 'cr 45'),
(1064529940, 'Juan', 'Perez', '3204098744', 'juanp@gmail.com', '0'),
(1068453216, 'Maria', 'Tellez', '3124202092', 'tellez@gmail.com', '0');

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

--
-- Volcado de datos para la tabla `datos`
--

INSERT INTO `datos` (`id_datos`, `informacion_general`, `informacion_equipo`, `ubicacion`, `lugar`, `cliente`, `tecnico`) VALUES
(1, 'equipo ', 'Ventilación mecanica', 'marsella', 'torre 3', 'Camilo torres', 'Jaime Garzon'),
(2, 'equipo de aire', 'Mini split', 'suba', 'torre 4+', 'Luis Hernandez', 'Yohan'),
(3, 'aquipo ', 'condensadora', 'Bosa', 'torre 1', 'Joge', 'Yohan Torres');

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
-- Estructura de tabla para la tabla `iniciar_sesion`
--

CREATE TABLE `iniciar_sesion` (
  `id_iniciar_sesion` int(50) NOT NULL,
  `rol_actual` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `localidad`
--

CREATE TABLE `localidad` (
  `Id_localidad` int(11) NOT NULL,
  `Nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `localidad`
--

INSERT INTO `localidad` (`Id_localidad`, `Nombre`) VALUES
(1, 'Bosa'),
(2, 'Soacha'),
(3, 'Antonio Nariño'),
(4, 'Barrios Unidos'),
(5, 'Bosa'),
(6, 'Usaquen'),
(7, 'Chapinero'),
(8, 'Santa Fe'),
(9, 'Kennedy'),
(10, 'Fontibon'),
(11, 'Suba'),
(12, 'Antonio Nariño'),
(13, 'Barrios Unidos'),
(14, 'Bosa'),
(15, 'Candelaria'),
(16, 'Chapinero'),
(17, 'Ciudad Bolivar'),
(18, 'Engativa'),
(19, 'Fontibon'),
(20, 'Kennedy'),
(21, 'Los Martires'),
(22, 'Puente Aranda'),
(23, 'Rafael Uribe Uribe'),
(24, 'San Cristobal');

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
  `telefono` varchar(100) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tecnico`
--

INSERT INTO `tecnico` (`id_tecnico`, `nombre`, `apellido`, `telefono`, `correo_electronico`) VALUES
(2, 'jaime', 'Garzon', '315420320', 'jaime_545@gmail.com'),
(3, 'Yojan ', 'Torres', '3154554747', 'yojan_255@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo de equipo`
--

CREATE TABLE `tipo de equipo` (
  `id_tipo_equipo` int(50) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `informacion_equipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indices de la tabla `informe`
--
ALTER TABLE `informe`
  ADD PRIMARY KEY (`id_informe`),
  ADD KEY `tipo_informe` (`tipo_informe`),
  ADD KEY `id_datos` (`id_datos`),
  ADD KEY `id_tipo_equipo` (`id_tipo_equipo`);

--
-- Indices de la tabla `iniciar_sesion`
--
ALTER TABLE `iniciar_sesion`
  ADD PRIMARY KEY (`id_iniciar_sesion`);

--
-- Indices de la tabla `localidad`
--
ALTER TABLE `localidad`
  ADD PRIMARY KEY (`Id_localidad`);

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
-- Indices de la tabla `tipo de equipo`
--
ALTER TABLE `tipo de equipo`
  ADD PRIMARY KEY (`id_tipo_equipo`);

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
-- AUTO_INCREMENT de la tabla `informe`
--
ALTER TABLE `informe`
  MODIFY `id_informe` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1025;

--
-- AUTO_INCREMENT de la tabla `iniciar_sesion`
--
ALTER TABLE `iniciar_sesion`
  MODIFY `id_iniciar_sesion` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `localidad`
--
ALTER TABLE `localidad`
  MODIFY `Id_localidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_roles` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `soporte`
--
ALTER TABLE `soporte`
  MODIFY `id_soporte` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tecnico`
--
ALTER TABLE `tecnico`
  MODIFY `id_tecnico` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipo de equipo`
--
ALTER TABLE `tipo de equipo`
  MODIFY `id_tipo_equipo` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuarios` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
