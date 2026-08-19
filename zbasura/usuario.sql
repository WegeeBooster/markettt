-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-03-2026 a las 22:04:18
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
-- Base de datos: `usuario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicaciones`
--

CREATE TABLE `publicaciones` (
  `id_publicacion` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(45) NOT NULL,
  `descripcion` varchar(350) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `estado` varchar(45) NOT NULL,
  `categoria` varchar(45) NOT NULL,
  `imagen` mediumblob NOT NULL,
  `usuario_id_usuario` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicaciones`
--

INSERT INTO `publicaciones` (`id_publicacion`, `titulo`, `descripcion`, `precio`, `estado`, `categoria`, `imagen`, `usuario_id_usuario`) VALUES
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
(112, 'Jareth pendejo', 'Pendejo', 0.01, 'inservible', 'penecito', '', 1111);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `redes`
--

CREATE TABLE `redes` (
  `id_red` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `tipo` varchar(45) DEFAULT NULL,
  `enlace` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `nombre` varchar(50) NOT NULL,
  `departamento` varchar(60) NOT NULL,
  `horario` varchar(45) DEFAULT NULL,
  `foto_de_perfil` mediumblob DEFAULT NULL,
  `tipo_usuario` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------------------------------

--
-- Estructura de la tabla para la `mensajes`
--

CREATE TABLE mensajes (
    id_mensaje INT AUTO_INCREMENT PRIMARY KEY,
    id_remitente INT NOT NULL,       -- El ID del usuario que está comprando/escribiendo
    id_destinatario INT NOT NULL,    -- El ID del vendedor
    id_producto INT NOT NULL,        -- Para saber por cuál producto están negociando
    contenido TEXT NOT NULL,         -- Lo que escribieron
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `correo`, `nombre`, `departamento`, `horario`, `foto_de_perfil`, `tipo_usuario`) VALUES
(1111, 'tetas@gmail.com', 'tetasmuygrandes', 'fisica', NULL, NULL, 'vendedor'),
(1112, 'penes@gmail.com', 'pene?', 'fisica', NULL, NULL, 'vendedor');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD PRIMARY KEY (`id_publicacion`),
  ADD KEY `usuario_id_usuario` (`usuario_id_usuario`);

--
-- Indices de la tabla `redes`
--
ALTER TABLE `redes`
  ADD PRIMARY KEY (`id_red`),
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
-- AUTO_INCREMENT de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  MODIFY `id_publicacion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT de la tabla `redes`
--
ALTER TABLE `redes`
  MODIFY `id_red` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resena`
--
ALTER TABLE `resena`
  MODIFY `id_resena` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1113;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD CONSTRAINT `publicaciones_ibfk_1` FOREIGN KEY (`usuario_id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `redes`
--
ALTER TABLE `redes`
  ADD CONSTRAINT `redes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `resena`
--
ALTER TABLE `resena`
  ADD CONSTRAINT `resena_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `resena_ibfk_2` FOREIGN KEY (`id_publicacion`) REFERENCES `publicaciones` (`id_publicacion`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
