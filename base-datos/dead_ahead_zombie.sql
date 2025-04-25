-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-04-2025 a las 06:32:25
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
-- Base de datos: `dead_ahead_zombie`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adquisicion`
--

CREATE TABLE `adquisicion` (
  `id_licencia` varchar(21) NOT NULL,
  `fecha_adquisicion` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `id_tipo_licencia` int(11) DEFAULT NULL,
  `id_estado_licencia` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `adquisicion`
--

INSERT INTO `adquisicion` (`id_licencia`, `fecha_adquisicion`, `fecha_fin`, `id_tipo_licencia`, `id_estado_licencia`, `id_empresa`) VALUES
('cc7799b73b60e786485c', '2025-04-24 22:14:00', '2025-04-23 06:16:00', 1, 3, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id_empresa` int(11) NOT NULL,
  `nom_empresa` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id_empresa`, `nom_empresa`) VALUES
(1, 'Iglesia Adventista Del Séptimo Dia'),
(2, 'Mercacentro'),
(3, 'D1'),
(4, 'Justo Y Bueno');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_licencia`
--

CREATE TABLE `estado_licencia` (
  `id_estado_licencia` int(11) NOT NULL,
  `nom_estado_licencia` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_licencia`
--

INSERT INTO `estado_licencia` (`id_estado_licencia`, `nom_estado_licencia`) VALUES
(1, 'Activa'),
(2, 'Inactiva'),
(3, 'Caducada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personajes`
--

CREATE TABLE `personajes` (
  `id_personaje` int(11) NOT NULL,
  `fuerza` int(11) DEFAULT NULL,
  `nom_personaje` varchar(50) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `creado_por` bigint(20) DEFAULT NULL,
  `fecha_creado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personajes`
--

INSERT INTO `personajes` (`id_personaje`, `fuerza`, `nom_personaje`, `foto`, `creado_por`, `fecha_creado`) VALUES
(1, 87, 'Dr Sanchez', 'img/personajes/Dr._Sanchez_Sprite.png', 1104943524, '2025-04-25 00:21:06'),
(2, 55, 'Carol Flannel', 'img/personajes/Flannel_Carol_Sprite.png', 1104943524, '2025-04-25 00:21:46'),
(3, 45, 'Austin', 'img/personajes/Austin_Sprite.png', 1104943524, '2025-04-25 00:22:26'),
(4, 95, 'Polina Nomad', 'img/personajes/Nomad_Polina_Sprite.png', 1104943524, '2025-04-25 00:23:10'),
(5, 20, 'Agents', 'img/personajes/Agents_Sprite.png', 1104943524, '2025-04-25 00:23:40'),
(6, 65, 'SpecOps Pilot', 'img/personajes/Pilot_Sprite.png', 1104943524, '2025-04-25 00:24:09'),
(7, 15, 'Nurse Flannel', 'img/personajes/Nurse_Hirsch_Sprite.png', 1104943524, '2025-04-25 00:25:20'),
(8, 95, 'Charlotte', 'img/personajes/Sheriff_Charlotte_Sprite.png', 1104943524, '2025-04-25 00:26:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `renta`
--

CREATE TABLE `renta` (
  `id_renta` int(11) NOT NULL,
  `cedula` bigint(15) DEFAULT NULL,
  `direccion` varchar(50) DEFAULT NULL,
  `fecha_renta` datetime DEFAULT NULL,
  `id_personaje` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `renta`
--

INSERT INTO `renta` (`id_renta`, `cedula`, `direccion`, `fecha_renta`, `id_personaje`) VALUES
(1, 12324214, 'mz e casa 3', '2025-04-24 23:23:00', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_roles` int(11) NOT NULL,
  `nom_roles` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_roles`, `nom_roles`) VALUES
(1, 'Admin'),
(2, 'SuperAdmin'),
(3, 'Usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_licencia`
--

CREATE TABLE `tipo_licencia` (
  `id_tipo_licencia` int(11) NOT NULL,
  `nom_licencia` varchar(50) DEFAULT NULL,
  `dias_plazo` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_licencia`
--

INSERT INTO `tipo_licencia` (`id_tipo_licencia`, `nom_licencia`, `dias_plazo`) VALUES
(1, 'Completa', NULL),
(2, 'Demo', NULL),
(3, 'Snapshot', NULL),
(4, 'Pre-release', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `documento` bigint(15) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `correo` varchar(50) DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `id_roles` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`documento`, `nombre`, `correo`, `contrasena`, `fecha_registro`, `id_roles`, `id_empresa`) VALUES
(0, 'hola', 'hola@gmail.com', '$2y$10$u08N9ArBiM3aQEIX1rMFiuLpr.dyDysozRA43T0LRRY0.cVQ2Trlm', '2025-04-25 04:03:17', 3, 2),
(22, '22', '22@gmail.com', '$2y$10$rrMKcaMR2ZexkLFnATqXk.Zl2QOPhZwyLwRlXHQ9LEml5xDPtbG9q', '2025-04-25 05:37:53', 1, 1),
(1111, '1111', '1111@gmail.com', '$2y$10$Udg5oRTSaQvZ4r76ubT8Fe3ZsQLQTtAtRYJt6seY0vSFHbIB2P.oa', '2025-04-25 03:24:20', 1, 3),
(1234, '1234', '1234@gmail.com', '$2y$10$A/w3oYBTPdN5Shen8T74q.eJ0u4aEf1FOxE3aM2sInOlZlZqrjgGu', '2025-04-25 03:25:00', 1, 2),
(5555, '5555', '5555@gmail.com', '$2y$10$G2BCPQx9JnDAOHEXCSU3xuXkt9cHSZwvzecUu.7m5yXFaErINqvvS', '2025-04-25 03:58:47', 3, 2),
(111111, 'yo', 'yo@gmail.com', '$2y$10$bzfr2.HeAWB2zvh/Bv3yp.6Ws21rLA.8O.yMQRsCJCcdjitUyaUDC', '2025-04-25 03:16:54', NULL, 3),
(123456789, '12345679', '12345679@gmail.com', '$2y$10$oIhmNSwPuMmAPvmmD8ipsu./eK9xW9VHv8/P5jVZXRzZ9626eUHdO', '2025-04-25 03:55:48', 3, 2),
(1104943524, 'Alan', NULL, '$2y$10$cKSbE0ZLiTJ4oETKoL55ie0jrg0yXGkFq0dbczWynETblO.LPVYom', NULL, 2, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `adquisicion`
--
ALTER TABLE `adquisicion`
  ADD PRIMARY KEY (`id_licencia`),
  ADD KEY `id_tipo_licencia` (`id_tipo_licencia`),
  ADD KEY `id_estado_licencia` (`id_estado_licencia`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_empresa`);

--
-- Indices de la tabla `estado_licencia`
--
ALTER TABLE `estado_licencia`
  ADD PRIMARY KEY (`id_estado_licencia`);

--
-- Indices de la tabla `personajes`
--
ALTER TABLE `personajes`
  ADD PRIMARY KEY (`id_personaje`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `renta`
--
ALTER TABLE `renta`
  ADD PRIMARY KEY (`id_renta`),
  ADD KEY `id_personaje` (`id_personaje`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_roles`);

--
-- Indices de la tabla `tipo_licencia`
--
ALTER TABLE `tipo_licencia`
  ADD PRIMARY KEY (`id_tipo_licencia`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`documento`),
  ADD KEY `id_roles` (`id_roles`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estado_licencia`
--
ALTER TABLE `estado_licencia`
  MODIFY `id_estado_licencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `personajes`
--
ALTER TABLE `personajes`
  MODIFY `id_personaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `renta`
--
ALTER TABLE `renta`
  MODIFY `id_renta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_roles` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipo_licencia`
--
ALTER TABLE `tipo_licencia`
  MODIFY `id_tipo_licencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `adquisicion`
--
ALTER TABLE `adquisicion`
  ADD CONSTRAINT `adquisicion_ibfk_1` FOREIGN KEY (`id_tipo_licencia`) REFERENCES `tipo_licencia` (`id_tipo_licencia`),
  ADD CONSTRAINT `adquisicion_ibfk_2` FOREIGN KEY (`id_estado_licencia`) REFERENCES `estado_licencia` (`id_estado_licencia`),
  ADD CONSTRAINT `adquisicion_ibfk_3` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`);

--
-- Filtros para la tabla `personajes`
--
ALTER TABLE `personajes`
  ADD CONSTRAINT `personajes_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`documento`);

--
-- Filtros para la tabla `renta`
--
ALTER TABLE `renta`
  ADD CONSTRAINT `renta_ibfk_1` FOREIGN KEY (`id_personaje`) REFERENCES `personajes` (`id_personaje`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_roles`) REFERENCES `roles` (`id_roles`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
