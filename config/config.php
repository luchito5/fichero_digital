<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'amemt';
const DB_USER = 'root';
const DB_PASS = '';

const SESSION_NAME = 'AMEMT_SESSION';
const APP_NAME = 'AMEMT - Fichero Digital';
const BASE_URL = '/fichero_digital/public';
const APP_TIMEZONE = 'America/Argentina/Buenos_Aires';

// Seguridad del fichaje: cada N fichajes (entradas + salidas) del día se corta el fichaje.
const FICHAJE_EVENTOS_POR_CORTE = 10;
const FICHAJE_CORTE_SEGUNDOS = 180;

date_default_timezone_set(APP_TIMEZONE);
