<?php
require_once __DIR__ . '/util.php';
require_login();
$uid = user_id();
$payload = json_input();
$resume_id = intval($payload['resume_id'] ?? 0);
if ($resume_id <= 0) { http_response_code(400); echo json_encode(['error' => 'bad_request']); exit; }

$conn->begin_transaction();
try {
    // ownership
    $own = $conn->prepare('SELECT id, title FROM resumes WHERE id = ? AND user_id = ? FOR UPDATE');
    $own->bind_param('ii', $resume_id, $uid);
    $own->execute();
    $row = $own->get_result()->fetch_assoc();
    $own->close();
    if (!$row) { throw new Exception('not_found'); }

    // create new resume
    $new_title = $row['title'] . ' (Copy)';
    $ins = $conn->prepare('INSERT INTO resumes (user_id, title) VALUES (?, ?)');
    $ins->bind_param('is', $uid, $new_title);
    if (!$ins->execute()) { throw new Exception('create_failed'); }
    $new_id = $ins->insert_id; $ins->close();

    // copy all versions
    $vs = $conn->prepare('SELECT version, data_json FROM resume_versions WHERE resume_id = ? ORDER BY version ASC');
    $vs->bind_param('i', $resume_id);
    $vs->execute();
    $res = $vs->get_result();
    $last_vid = null;
    while ($v = $res->fetch_assoc()) {
        $iv = $conn->prepare('INSERT INTO resume_versions (resume_id, version, data_json) VALUES (?, ?, ?)');
        $iv->bind_param('iis', $new_id, $v['version'], $v['data_json']);
        $iv->execute();
        $last_vid = $iv->insert_id; $iv->close();
    }
    $vs->close();

    if ($last_vid) {
        $upd = $conn->prepare('UPDATE resumes SET latest_version_id = ? WHERE id = ?');
        $upd->bind_param('ii', $last_vid, $new_id);
        $upd->execute();
        $upd->close();
    }

    $conn->commit();
    echo json_encode(['ok' => true, 'resume_id' => $new_id]);
} catch (Exception $e) {
    $conn->rollback();
    $code = $e->getMessage();
    if ($code === 'not_found') { http_response_code(404); }
    else { http_response_code(500); }
    echo json_encode(['error' => $code]);
}

