<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html');
    exit;
}
// Serve the existing HTML builder UI
readfile(__DIR__ . '/resume.html');
