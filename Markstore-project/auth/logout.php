<?php
session_start();

// Барлық сессия айнымалыларын жою
$_SESSION = array();

// Сессияны жою
session_destroy();

// Басты бетке бағыттау
header("Location: /");
exit;
?>