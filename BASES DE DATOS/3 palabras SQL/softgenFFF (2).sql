-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-12-2024 a las 02:38:40
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
-- Base de datos: `softgen2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `id_admin` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `apellido` text NOT NULL,
  `n_documento` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`id_admin`, `nombre`, `apellido`, `n_documento`, `telefono`, `correo_electronico`) VALUES
(201, 'Edwin', 'Montenegro', '1011097480', '370729821', 'emontenegro@gmail.com'),
(202, 'Marí José', 'Mendoza', '1106227097', '300737142', 'mmendozagmail.com'),
(203, 'Juan', 'Gomez', '1094048008', '320458402', 'jgomez@gmail.com'),
(204, 'Harold', 'Peñaloza', '1015484614', '311458943', 'hpenaloza@gmail.com'),
(205, 'Dajaryth', 'Hernandez', '1096803380', '320406972', 'dhernandez@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `id_inconformidad` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `n_documento` varchar(20) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `id_inconformidad`, `nombre`, `apellido`, `n_documento`, `telefono`, `correo_electronico`) VALUES
(101, 0, 'Juan ', 'Arias', '79953647', '3055493005', 'jarias@gmail.com'),
(102, 0, 'Fatima', 'Becerra', '79049940', '3204078912', 'fbecerra@gmail.com'),
(103, 0, 'George', 'Cuellar', '80345512', '3133953333', 'gcuellar@gmail.com'),
(104, 0, 'Laura', 'Diaz', '90635267', '3018284530', 'ldiaz@gmail.com'),
(105, 0, 'Luisa', 'Flores', '10185163524', '3177298707', 'lflorez@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos`
--

CREATE TABLE `datos` (
  `id_datos` int(11) NOT NULL,
  `id_ubicacion` int(11) NOT NULL,
  `informacion_general` varchar(500) NOT NULL,
  `informacion_equipo` varchar(500) NOT NULL,
  `lugar` varchar(100) NOT NULL,
  `datos_cliente` varchar(100) NOT NULL,
  `datos_tecnico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `datos`
--

INSERT INTO `datos` (`id_datos`, `id_ubicacion`, `informacion_general`, `informacion_equipo`, `lugar`, `datos_cliente`, `datos_tecnico`) VALUES
(1, 1, 'Se realizo una variable de una condensadora correctamente, ZeroCool Infinity, Manizales, La estrella', '', 'Torre 2', '', ''),
(2, 2, 'Se realizo mantenimiento de manejadora correctamente, EcoBreeze 360, Medellin, El tesoro, Cuarto piso, C.C 80345512, Gorge Cuellar, 3133953333, Cliente, Aydee Bermudez 3129128711, Tecnico.', '', 'Segundo piso', '', ''),
(3, 3, 'Se realizo mantenimiento de manejadora correctamente, EcoBreeze 360, Medellin, El tesoro, Cuarto piso, C.C 80345512, Gorge Cuellar, 3133953333, Cliente, Aydee Bermudez 3129128711, Tecnico.', '', 'Cuarto piso', '', ''),
(4, 4, 'Se realizo limpieza de condensadora correctamente, AquaChill Smart Air, Cartagena, El centro, Sexto piso, C.C 90635267, Laura Diaz, 3018284530, Cliente, Aydee Bermudez 3129128711, Tecnico.', '', 'Sexto piso', '', ''),
(5, 5, 'Se realizo el mantennimiento de una manejadora correctamente, AirFlow Nexus, Leticia, Puerto, Sotano, C.C 1085163524, Luisa  Florez, 3177298707, Cliente, Marco Buitrago 3129128711, Tecnico.', '', 'Sotano', '', '');

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

--
-- Volcado de datos para la tabla `equipo`
--

INSERT INTO `equipo` (`id_equipo`, `descripcion`, `informacion_equipo`, `tipo_equipo`) VALUES
(1, 'Aire acondicionado', 'ClimaX 5000', 'Por agua'),
(2, 'Ventilacion mecanica', 'Trane', 'Mecanico'),
(3, 'Aire acondicionado', 'Ecobreeze 360', 'Variable'),
(4, 'Aire acondicionado', 'York', 'Expansion directa'),
(5, 'Aire acondicionado', 'Airconfort\r\n', 'Manejadora\r\n');

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
  `id_informe` int(11) NOT NULL,
  `id_datos` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `tipo_informe` varchar(50) NOT NULL,
  `informacion_limpieza` varchar(100) NOT NULL,
  `informacion_electrica` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `informe`
--

INSERT INTO `informe` (`id_informe`, `id_datos`, `id_equipo`, `tipo_informe`, `informacion_limpieza`, `informacion_electrica`) VALUES
(1, 1, 1, 'Mantenimeinto correctivo', 'Se realizo el respectivo mantenimiento correctivo a los equipos ubicados Bogotá, La estrella, Torre ', 'Voltaje L1, L2, L3;  Amperaje A1, A2, A3'),
(2, 2, 2, 'Mantenimiento correctivo', 'Se realizo una variable de una condensadora correctamente, ZeroCool Infinity, Manizales, La estrella', 'Voltaje L1, L2, L3;  Amperaje A1, A2, A3'),
(3, 3, 3, 'Instalacion', 'Se realizo mantenimiento de manejadora correctamente, EcoBreeze 360, Medellin, El tesoro, Cuarto pis', 'Voltaje L1, L2, L3;  Amperaje A1, A2, A3'),
(4, 4, 4, 'Mantenimiento correctivo', 'Se realizo limpieza de condensadora correctamente, AquaChill Smart Air, Cartagena, El centro, Sexto ', 'Voltaje L1, L2, L3;  Amperaje A1, A2, A3'),
(5, 5, 5, 'Revision general', 'Se realizo el mantennimiento de una manejadora correctamente, AirFlow Nexus, Leticia, Puerto, Sotano', 'Voltaje L1, L2, L3;  Amperaje A1, A2, A3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_roles` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_tecnico` int(11) NOT NULL,
  `id_usuarios` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_roles`, `id_admin`, `id_cliente`, `id_tecnico`, `id_usuarios`) VALUES
(1, 201, 101, 301, 0),
(2, 202, 102, 302, 0),
(3, 203, 103, 303, 0),
(4, 204, 104, 304, 0),
(5, 205, 105, 305, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_usuario`
--

CREATE TABLE `rol_usuario` (
  `id_rol_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_informe` int(11) NOT NULL,
  `contraseña` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `soporte`
--

CREATE TABLE `soporte` (
  `id_soporte` int(11) NOT NULL,
  `id_roles` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tecnico`
--

CREATE TABLE `tecnico` (
  `id_tecnico` int(11) NOT NULL,
  `id_inconformidad` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `apellido` text NOT NULL,
  `n_documento` int(20) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tecnico`
--

INSERT INTO `tecnico` (`id_tecnico`, `id_inconformidad`, `nombre`, `apellido`, `n_documento`, `telefono`, `correo_electronico`) VALUES
(301, 0, 'Aydee', 'Bermudez', 1012377378, '31012912871', 'abermudez@gmail.com'),
(302, 0, 'Marco', 'Buitrago', 1014287406, '3224567879', 'mnuitrago@gmail.com'),
(303, 0, 'Johann', 'Torres', 1014291421, '3190029374', 'jtorres@gmail.com'),
(304, 0, 'Nicolas', 'Pulido', 1018491721, '3112299345', 'mpulido@gmail.com'),
(305, 0, 'Jaime', 'Pulido', 11187866, '3115333788', 'jpulido@gmail.com');

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
  `id_usuario` int(11) NOT NULL,
  `id_rol_usuario` int(11) NOT NULL,
  `id_soporte` int(11) NOT NULL,
  `n_documento` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `rol` varchar(20) NOT NULL,
  `rol_usuario` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_rol_usuario`, `id_soporte`, `n_documento`, `nombre`, `apellido`, `telefono`, `rol`, `rol_usuario`, `email`) VALUES
(1, 0, 0, 'C.C. 79955347', 'Juan', 'Arias', '3055490055', 'Cliente', 'Cliente101', 'jarias@email.com'),
(2, 0, 0, '790499460', 'Fatima', 'Becerra', '3204078912', 'Cliente', 'Cliente102', 'fbecerra@email.com'),
(3, 0, 0, '803451512', 'Jorge', 'Cuellar', '3133953333', 'Cliente', 'Cliente103', 'jcuellar@email.com'),
(4, 0, 0, '906355627', 'Laura', 'Diaz', '3012864350', 'Cliente', 'Cliente104', 'ldiaz@email.com'),
(5, 0, 0, '805185452', 'Luisa', 'Florez', '3172977807', 'Cliente', 'Cliente105', 'lflorez@email.com'),
(6, 0, 0, '1102974080', 'Edwin', 'Montenegro', '3707290210', 'Administrador', 'Administrador201', 'emontenegro@email.com'),
(7, 0, 0, '110629707', 'Maria Jose', 'Mendoza', '3007714298', 'Administrador', 'Administrador202', 'mmendoza@email.com'),
(8, 0, 0, '1094080808', 'Juan', 'Gomez', '3204584403', 'Administrador', 'Administrador203', 'jgomez@email.com'),
(9, 0, 0, '1015484514', 'Harold', 'Peñaloza', '3144589432', 'Administrador', 'Administrador204', 'hpenaloza@email.com'),
(10, 0, 0, '1096830830', 'Dajanhy', 'Hernandez', '3004778802', 'Administrador', 'Administrador205', 'dhernandez@email.com'),
(11, 0, 0, '1012377378', 'Aydee', 'Bermudez', '3129182171', 'Técnico', 'Tecnico301', 'abermudez@email.com'),
(12, 0, 0, '1014287406', 'Marco', 'Buitrago', '3224567879', 'Técnico', 'Tecnico302', 'mbuitrago@email.com'),
(13, 0, 0, '1014294124', 'Johan', 'Torres', '3120099742', 'Técnico', 'Tecnico303', 'jtorres@email.com'),
(14, 0, 0, '1014819271', 'Nicolas', 'Pulido', '3112293945', 'Técnico', 'Tecnico304', 'npulido@email.com'),
(15, 0, 0, '11187865', 'Jaime', 'Pulido', '3115333788', 'Técnico', 'Tecnico305', 'jpulido@email.com');

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
  ADD PRIMARY KEY (`id_cliente`),
  ADD KEY `id_inconformidad` (`id_inconformidad`);

--
-- Indices de la tabla `datos`
--
ALTER TABLE `datos`
  ADD PRIMARY KEY (`id_datos`),
  ADD KEY `id_ubicacion` (`id_ubicacion`);

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
  ADD KEY `id_datos` (`id_datos`),
  ADD KEY `id_equipo` (`id_equipo`);

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
  ADD PRIMARY KEY (`id_rol_usuario`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_informe` (`id_informe`);

--
-- Indices de la tabla `soporte`
--
ALTER TABLE `soporte`
  ADD PRIMARY KEY (`id_soporte`);

--
-- Indices de la tabla `tecnico`
--
ALTER TABLE `tecnico`
  ADD PRIMARY KEY (`id_tecnico`),
  ADD KEY `id_inconformidad` (`id_inconformidad`);

--
-- Indices de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD PRIMARY KEY (`id_ubicacion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol_usuario` (`id_rol_usuario`),
  ADD KEY `id_soporte` (`id_soporte`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1015474616;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1068453217;

--
-- AUTO_INCREMENT de la tabla `datos`
--
ALTER TABLE `datos`
  MODIFY `id_datos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `equipo`
--
ALTER TABLE `equipo`
  MODIFY `id_equipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `inconformidad`
--
ALTER TABLE `inconformidad`
  MODIFY `id_inconformidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `informe`
--
ALTER TABLE `informe`
  MODIFY `id_informe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1025;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_roles` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `rol_usuario`
--
ALTER TABLE `rol_usuario`
  MODIFY `id_rol_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `soporte`
--
ALTER TABLE `soporte`
  MODIFY `id_soporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tecnico`
--
ALTER TABLE `tecnico`
  MODIFY `id_tecnico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=306;

--
-- AUTO_INCREMENT de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  MODIFY `id_ubicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Filtros para la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD CONSTRAINT `equipo_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `informe` (`id_equipo`);

--
-- Filtros para la tabla `inconformidad`
--
ALTER TABLE `inconformidad`
  ADD CONSTRAINT `inconformidad_ibfk_1` FOREIGN KEY (`id_inconformidad`) REFERENCES `cliente` (`id_inconformidad`),
  ADD CONSTRAINT `inconformidad_ibfk_2` FOREIGN KEY (`id_inconformidad`) REFERENCES `tecnico` (`id_inconformidad`);

--
-- Filtros para la tabla `rol_usuario`
--
ALTER TABLE `rol_usuario`
  ADD CONSTRAINT `rol_usuario_ibfk_1` FOREIGN KEY (`id_rol_usuario`) REFERENCES `usuarios` (`id_rol_usuario`),
  ADD CONSTRAINT `rol_usuario_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `rol_usuario_ibfk_3` FOREIGN KEY (`id_informe`) REFERENCES `informe` (`id_informe`);

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
-- Filtros para la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD CONSTRAINT `ubicacion_ibfk_1` FOREIGN KEY (`id_ubicacion`) REFERENCES `datos` (`id_ubicacion`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
