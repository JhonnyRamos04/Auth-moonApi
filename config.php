<?php

// Configuración de la Base de Datos
define('DB_HOST', 'localhost'); // o la IP de tu servidor de BD
define('DB_NAME', 'tu_base_de_datos');
define('DB_USER', 'tu_usuario_de_bd');
define('DB_PASS', 'tu_contraseña_de_bd');

// Clave secreta para firmar los JWT (JSON Web Tokens)
// ¡CAMBIA ESTO POR UNA CADENA LARGA Y SEGURA!
define('JWT_SECRET', 'tu_clave_secreta_super_larga_y_dificil_de_adivinar');
