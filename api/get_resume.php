<?php
require_once __DIR__ . '/util.php';
require_login();
$uid = user_id();

$resume_id = intval($_GET['resume_id'] ?? 0);
if ($resume_id <= 0) { http_response_code(400); echo json_encode(['error' => 'bad_request']); exit; }

// Ensure ownership
$own = $conn->prepare('SELECT id, title, latest_version_id FROM resumes WHERE id = ? AND user_id = ?');
$own->bind_param('ii', $resume_id, $uid);
$own->execute();
$r = $own->get_result()->fetch_assoc();
$own->close();
if (!$r) { http_response_code(404); echo json_encode(['error' => 'not_found']); exit; }

// Get latest version
$vstmt = $conn->prepare('SELECT id, version, data_json, created_at FROM resume_versions WHERE resume_id = ? ORDER BY version DESC LIMIT 1');
$vstmt->bind_param('i', $resume_id);
$vstmt->execute();
$v = $vstmt->get_result()->fetch_assoc();
$vstmt->close();

if (!$v) { echo json_encode(['resume' => ['id' => $resume_id, 'title' => $r['title'], 'version' => 0, 'data' => new stdClass()]]); exit; }

echo json_encode(['resume' => [
    'id' => $resume_id,
    'title' => $r['title'],
    'version' => intval($v['version']),
    'data' => json_decode($v['data_json'], true)
]]);

