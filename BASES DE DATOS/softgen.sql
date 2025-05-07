-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-11-2024 a las 19:47:35
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(50) NOT NULL,
  `nombre` text NOT NULL,
  `apellido` text NOT NULL,
  `telefono` int(100) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estructura de tabla para la tabla `informe`
--

CREATE TABLE `informe` (
  `id_informe` int(50) NOT NULL,
  `tipo_informe` int(50) NOT NULL,
  `id_datos` int(50) NOT NULL,
  `id_tipo_equipo` int(50) NOT NULL,
  `informacion_limpieza` varchar(100) NOT NULL,
  `informacion_electrica` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `telefono` int(100) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id_admin` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `datos`
--
ALTER TABLE `datos`
  MODIFY `id_datos` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `informe`
--
ALTER TABLE `informe`
  MODIFY `id_informe` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `iniciar_sesion`
--
ALTER TABLE `iniciar_sesion`
  MODIFY `id_iniciar_sesion` int(50) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_tecnico` int(50) NOT NULL AUTO_INCREMENT;

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

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD CONSTRAINT `administrador_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `roles` (`id_admin`);

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `roles` (`id_cliente`);

--
-- Filtros para la tabla `datos`
--
ALTER TABLE `datos`
  ADD CONSTRAINT `datos_ibfk_1` FOREIGN KEY (`id_datos`) REFERENCES `informe` (`id_datos`);

--
-- Filtros para la tabla `informe`
--
ALTER TABLE `informe`
  ADD CONSTRAINT `informe_ibfk_1` FOREIGN KEY (`id_informe`) REFERENCES `usuarios` (`id_informe`);

--
-- Filtros para la tabla `iniciar_sesion`
--
ALTER TABLE `iniciar_sesion`
  ADD CONSTRAINT `iniciar_sesion_ibfk_1` FOREIGN KEY (`id_iniciar_sesion`) REFERENCES `usuarios` (`id_iniciar_sesion`);

--
-- Filtros para la tabla `soporte`
--
ALTER TABLE `soporte`
  ADD CONSTRAINT `soporte_ibfk_1` FOREIGN KEY (`id_soporte`) REFERENCES `usuarios` (`id_soporte`);

--
-- Filtros para la tabla `tecnico`
--
ALTER TABLE `tecnico`
  ADD CONSTRAINT `tecnico_ibfk_1` FOREIGN KEY (`id_tecnico`) REFERENCES `roles` (`id_tecnico`);

--
-- Filtros para la tabla `tipo de equipo`
--
ALTER TABLE `tipo de equipo`
  ADD CONSTRAINT `tipo de equipo_ibfk_1` FOREIGN KEY (`id_tipo_equipo`) REFERENCES `informe` (`id_tipo_equipo`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_usuarios`) REFERENCES `roles` (`id_usuarios`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
