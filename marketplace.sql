-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-05-2026 a las 01:13:41
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
-- Base de datos: `marketplace`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensaje`
--

CREATE TABLE `mensaje` (
  `id_mensaje` int(11) NOT NULL,
  `id_remitente` int(10) UNSIGNED NOT NULL,
  `id_destinatario` int(10) UNSIGNED NOT NULL,
  `id_publicacion` int(10) UNSIGNED NOT NULL,
  `contenido` text NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensaje`
--

INSERT INTO `mensaje` (`id_mensaje`, `id_remitente`, `id_destinatario`, `id_publicacion`, `contenido`, `fecha_envio`) VALUES
(4, 1111, 1112, 106, 'lol', '2026-04-01 04:17:35'),
(5, 1111, 1112, 106, 'oye bro que pedo', '2026-04-01 04:30:40'),
(6, 1111, 1112, 106, 'aaaaaaaaaaaaaaa', '2026-04-01 04:30:47'),
(7, 1111, 1112, 110, 'hola?', '2026-04-01 10:43:30'),
(8, 1111, 1112, 106, 'nb', '2026-04-01 11:42:24'),
(9, 1111, 1112, 106, 'que onda we', '2026-04-01 11:56:30'),
(10, 1111, 1112, 106, 'a', '2026-04-01 11:56:32'),
(11, 1111, 1112, 106, 's', '2026-04-01 11:56:32'),
(12, 1111, 1112, 106, 'd', '2026-04-01 11:56:32'),
(13, 1111, 1112, 106, 'as', '2026-04-01 11:56:32'),
(14, 1111, 1112, 106, 'das', '2026-04-01 11:56:33'),
(15, 1111, 1112, 106, 'sda', '2026-04-01 11:56:33'),
(16, 1111, 1112, 106, 'sda', '2026-04-01 11:56:33'),
(17, 1111, 1112, 106, 'sd', '2026-04-01 11:56:33'),
(18, 1111, 1112, 106, 'asd', '2026-04-01 11:56:34'),
(19, 1111, 1112, 106, 'aas', '2026-04-01 11:56:34'),
(20, 1111, 1112, 106, 'd', '2026-04-01 11:56:34'),
(21, 1111, 1112, 106, 'f', '2026-04-01 11:56:35'),
(22, 1111, 1112, 106, 'a', '2026-04-01 11:56:36'),
(23, 1111, 1112, 106, 'lol', '2026-04-01 11:56:39'),
(24, 1111, 1112, 106, 'xd', '2026-04-01 11:58:20'),
(25, 1112, 1111, 106, 'xd?', '2026-04-01 11:59:28'),
(26, 1111, 1112, 106, 'asd', '2026-04-01 12:00:29'),
(27, 1112, 1111, 106, 'sss', '2026-04-01 12:00:36'),
(28, 1111, 1112, 106, 'qw', '2026-04-01 12:01:43'),
(29, 1111, 1112, 106, 'lll', '2026-04-01 12:02:00'),
(30, 1111, 1112, 106, 'jaja', '2026-04-01 12:02:03'),
(31, 1112, 1111, 106, 'que loco', '2026-04-01 12:02:06'),
(32, 1112, 1111, 106, 'Bro…..', '2026-04-01 12:05:21'),
(33, 1112, 1111, 106, 'Lol', '2026-04-01 12:05:36'),
(34, 1112, 1112, 106, 'Zzzz', '2026-04-01 12:06:20'),
(35, 1112, 1111, 106, 'as', '2026-04-09 03:29:49'),
(36, 1112, 1111, 106, 'holaaaaaaaaaaaaaaaa', '2026-04-09 03:31:23'),
(37, 1112, 1112, 106, 'hoalaa2', '2026-04-09 03:32:27'),
(38, 1112, 1111, 106, 'asdfghjkl', '2026-04-09 03:33:26'),
(39, 1112, 1112, 111, 'hola', '2026-04-09 03:47:32'),
(40, 1112, 1112, 111, 'aaa', '2026-04-09 03:47:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion`
--

CREATE TABLE `publicacion` (
  `id_publicacion` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(45) NOT NULL,
  `descripcion` varchar(350) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `estado` varchar(45) NOT NULL,
  `categoria` varchar(45) NOT NULL,
  `imagen` varchar(320) NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicacion`
--

INSERT INTO `publicacion` (`id_publicacion`, `titulo`, `descripcion`, `precio`, `estado`, `categoria`, `imagen`, `id_usuario`) VALUES
(100, 'mause', 'esta al 100%', 0.00, 'bueno', 'electronicos', '', 1111),
(101, 'mause', 'esta al 100%', 0.00, 'bueno', 'electronicos', '', 1111),
(102, 'Mochila urbana', '20L, resistente al agua, USB integrado', 520.00, '', 'Accesorios', '', 1111),
(103, 'Mochila urbana', '20L, resistente al agua, USB integrado', 520.00, '', 'Accesorios', '', 1111),
(104, 'Audífonos inalámbricos', 'Cancelación de ruido, 30h de batería', 1200.00, '', 'Electrónica', '', 1112),
(105, 'Lámpara de escritorio', 'Luz LED regulable, cuello flexible', 380.00, '', 'Hogar', '', 1111),
(106, 'Agenda 2025', 'Tapa dura, hojas con puntos', 180.00, '', 'Papelería', '', 1112),
(107, 'Mouse inalámbrico', 'Ergonómico, 1600 DPI, silencioso', 430.00, '', 'Electrónica', '', 1111),
(108, 'Termo de acero', '500ml, mantiene temperatura 12h', 290.00, '', 'Hogar', '', 1111),
(109, 'Porta laptop', 'Funda de tela, para 15 pulgadas', 210.00, '', 'Accesorios', '', 1111),
(110, 'Regla metálica 30cm', 'Aluminio anodizado, marcas grabadas', 75.00, '', 'Papelería', '', 1112),
(111, 'Monitor 24\"', 'Full HD IPS, 75Hz, sin bordes', 3200.00, '', 'Electrónica', '', 1112),
(112, 'cocaina', 'polvo magico', 0.01, '', 'Papelería', '../uploads/69d7eefca2465_marketplace_unison.png', 1112),
(113, 'lol', '', 10.00, 'Nuevo', 'Otros', '[\"..\\/uploads\\/69dc27d4ac7e7_descarga.png\"]', 1111),
(114, 'prueba2', 'prueba multiples fotos', 100.00, 'Nuevo', 'Otros', '[\"..\\/uploads\\/69dc281f8b6e6_descarga.png\",\"..\\/uploads\\/69dc281f8b996_marketplace_unison.jpg\"]', 1111),
(115, 'Nigas', 'Nigas en potencia', 1.00, 'Usado - Aceptable', 'Otros', '[\"..\\/uploads\\/6a0e2bd5dda66_907d4a04-2444-47cb-b93f-2a8b43e520a3.jpeg\",\"..\\/uploads\\/6a0e2bd5ddf73_1b885ba1-6603-4f96-b7fb-51ad8976a881.jpeg\",\"..\\/uploads\\/6a0e2bd5de1d9_f5823bb1-4914-4c44-8ca9-76ba6b663f87.jpeg\"]', 1114);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `red`
--

CREATE TABLE `red` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `tipo` varchar(45) DEFAULT NULL,
  `enlace` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `red`
--

INSERT INTO `red` (`id_usuario`, `tipo`, `enlace`) VALUES
(1111, 'whatsapp', '6666'),
(1111, 'instagram', '@peneRicolino');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resena`
--

CREATE TABLE `resena` (
  `id_resena` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_publicacion` int(10) UNSIGNED NOT NULL,
  `calificacion` int(10) UNSIGNED DEFAULT NULL,
  `comentario` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `correo` varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `departamento` varchar(60) NOT NULL,
  `horario` varchar(45) DEFAULT NULL,
  `foto_de_perfil` varchar(255) DEFAULT NULL,
  `tipo_usuario` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `correo`, `password`, `nombre`, `departamento`, `horario`, `foto_de_perfil`, `tipo_usuario`) VALUES
(1111, 'tetas@unison.mx', '1234', 'pene Rico', 'Sociales', ' matutino', '../uploads/avatar_1111_6a0f7f49549fd.jpg', 'vendedor'),
(1112, 'penes@gmail.com', '1234', 'pene?', 'fisica', NULL, NULL, 'vendedor'),
(1113, 'xdanielalberto@gmail.com', 'hola', 'daniel', 'Matemáticas', NULL, NULL, 'comprador'),
(1114, 'pene@unison.mx', 'abcd', 'pene', 'Otro', NULL, NULL, 'comprador'),
(1115, 'sddas@unison.mx', 'qwer', 'asda asdas', 'Matemáticas', NULL, NULL, 'comprador');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `mensaje`
--
ALTER TABLE `mensaje`
  ADD PRIMARY KEY (`id_mensaje`),
  ADD KEY `idx_remitente` (`id_remitente`),
  ADD KEY `idx_destinatario` (`id_destinatario`),
  ADD KEY `idx_publicacion` (`id_publicacion`);

--
-- Indices de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD PRIMARY KEY (`id_publicacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `red`
--
ALTER TABLE `red`
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `resena`
--
ALTER TABLE `resena`
  ADD PRIMARY KEY (`id_resena`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_publicacion` (`id_publicacion`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `mensaje`
--
ALTER TABLE `mensaje`
  MODIFY `id_mensaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  MODIFY `id_publicacion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT de la tabla `resena`
--
ALTER TABLE `resena`
  MODIFY `id_resena` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1116;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `mensaje`
--
ALTER TABLE `mensaje`
  ADD CONSTRAINT `fk_mensaje_destinatario` FOREIGN KEY (`id_destinatario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mensaje_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id_publicacion`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mensaje_remitente` FOREIGN KEY (`id_remitente`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `fk_publicacion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `red`
--
ALTER TABLE `red`
  ADD CONSTRAINT `fk_red_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `resena`
--
ALTER TABLE `resena`
  ADD CONSTRAINT `fk_resena_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id_publicacion`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_resena_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
