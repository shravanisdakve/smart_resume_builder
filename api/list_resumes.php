<?php
require_once __DIR__ . '/util.php';
require_login();
$uid = user_id();

$sql = "SELECT r.id, r.title, r.is_public, r.public_slug, r.updated_at,
               (SELECT COUNT(1) FROM resume_versions v WHERE v.resume_id = r.id) AS version_count
        FROM resumes r WHERE r.user_id = ? ORDER BY r.updated_at DESC";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $row['public_url'] = ($row['is_public'] && $row['public_slug']) ? ("view.php?slug=" . $row['public_slug']) : null;
        $rows[] = $row;
    }
    echo json_encode(['resumes' => $rows]);
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['error' => 'query_failed']);
}

