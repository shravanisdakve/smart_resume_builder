<?php
require_once __DIR__ . '/util.php';
require_login();
$uid = user_id();
$payload = json_input();
$title = trim($payload['title'] ?? '');
$data = $payload['data'] ?? [];
if ($title === '' || !is_array($data)) { http_response_code(400); echo json_encode(['error' => 'bad_request']); exit; }

$conn->begin_transaction();
try {
    $ins = $conn->prepare('INSERT INTO resumes (user_id, title) VALUES (?, ?)');
    $ins->bind_param('is', $uid, $title);
    if (!$ins->execute()) { throw new Exception('create_failed'); }
    $resume_id = $ins->insert_id;
    $ins->close();

    $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    $iv = $conn->prepare('INSERT INTO resume_versions (resume_id, version, data_json) VALUES (?, 1, ?)');
    $iv->bind_param('is', $resume_id, $json);
    if (!$iv->execute()) { throw new Exception('version_insert_failed'); }
    $version_id = $iv->insert_id;
    $iv->close();

    $upd = $conn->prepare('UPDATE resumes SET latest_version_id = ? WHERE id = ?');
    $upd->bind_param('ii', $version_id, $resume_id);
    $upd->execute();
    $upd->close();

    $conn->commit();
    echo json_encode(['ok' => true, 'resume_id' => $resume_id, 'version' => 1]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'server_error']);
}

