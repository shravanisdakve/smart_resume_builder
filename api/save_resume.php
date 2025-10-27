<?php
require_once __DIR__ . '/util.php';
require_login();
$uid = user_id();

$payload = json_input();
$resume_id = intval($payload['resume_id'] ?? 0);
$title = trim($payload['title'] ?? '');
$data = $payload['data'] ?? [];

if (!is_array($data)) { http_response_code(400); echo json_encode(['error' => 'invalid_data']); exit; }

$conn->begin_transaction();

try {
    if ($resume_id > 0) {
        // Verify ownership
        $chk = $conn->prepare('SELECT id FROM resumes WHERE id = ? AND user_id = ? FOR UPDATE');
        $chk->bind_param('ii', $resume_id, $uid);
        $chk->execute();
        if (!$chk->get_result()->fetch_assoc()) { throw new Exception('not_found'); }
        $chk->close();

    } else {
        if ($title === '') { throw new Exception('title_required'); }
        // Create resume
        $ins = $conn->prepare('INSERT INTO resumes (user_id, title) VALUES (?, ?)');
        $ins->bind_param('is', $uid, $title);
        if (!$ins->execute()) { throw new Exception('create_failed'); }
        $resume_id = $ins->insert_id;
        $ins->close();
    }

    // Next version number
    $getv = $conn->prepare('SELECT IFNULL(MAX(version),0)+1 as nextv FROM resume_versions WHERE resume_id = ?');
    $getv->bind_param('i', $resume_id);
    $getv->execute();
    $nextv = intval($getv->get_result()->fetch_assoc()['nextv']);
    $getv->close();

    $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    $iv = $conn->prepare('INSERT INTO resume_versions (resume_id, version, data_json) VALUES (?, ?, ?)');
    $iv->bind_param('iis', $resume_id, $nextv, $json);
    if (!$iv->execute()) { throw new Exception('version_insert_failed'); }
    $version_id = $iv->insert_id;
    $iv->close();

    $upd = $conn->prepare('UPDATE resumes SET latest_version_id = ?, updated_at = NOW() WHERE id = ?');
    $upd->bind_param('ii', $version_id, $resume_id);
    $upd->execute();
    $upd->close();

    $conn->commit();
    echo json_encode(['ok' => true, 'resume_id' => $resume_id, 'version' => $nextv]);
} catch (Exception $e) {
    $conn->rollback();
    $code = $e->getMessage();
    if ($code === 'not_found') { http_response_code(404); }
    elseif ($code === 'title_required') { http_response_code(400); }
    else { http_response_code(500); }
    echo json_encode(['error' => $code]);
}

