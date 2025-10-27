<?php
session_start();
require_once __DIR__ . '/../connect.php';

function require_login() {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        http_response_code(401);
        echo json_encode(['error' => 'unauthorized']);
        exit;
    }
}

function user_id() {
    return $_SESSION['id'] ?? null;
}

function json_input() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function slugify_random($len = 10) {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $s = '';
    for ($i = 0; $i < $len; $i++) { $s .= $alphabet[random_int(0, strlen($alphabet)-1)]; }
    return $s;
}

header('Content-Type: application/json');

