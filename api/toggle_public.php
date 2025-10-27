<?php
require_once __DIR__ . '/util.php';
require_login();
$uid = user_id();
$payload = json_input();
$resume_id = intval($payload['resume_id'] ?? 0);
$make_public = !!($payload['is_public'] ?? false);
if ($resume_id <= 0) { http_response_code(400); echo json_encode(['error' => 'bad_request']); exit; }

// Check ownership
$own = $conn->prepare('SELECT id, public_slug FROM resumes WHERE id = ? AND user_id = ?');
$own->bind_param('ii', $resume_id, $uid);
$own->execute();
$row = $own->get_result()->fetch_assoc();
$own->close();
if (!$row) { http_response_code(404); echo json_encode(['error' => 'not_found']); exit; }

$slug = $row['public_slug'];
if ($make_public && !$slug) {
    // generate unique slug
    do {
        $candidate = slugify_random(12);
        $chk = $conn->prepare('SELECT id FROM resumes WHERE public_slug = ?');
        $chk->bind_param('s', $candidate);
        $chk->execute();
        $exists = $chk->get_result()->fetch_assoc();
        $chk->close();
    } while ($exists);
    $slug = $candidate;
}

$stmt = $conn->prepare('UPDATE resumes SET is_public = ?, public_slug = ? WHERE id = ? AND user_id = ?');
$is_pub = $make_public ? 1 : 0;
$stmt->bind_param('isii', $is_pub, $slug, $resume_id, $uid);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) { http_response_code(500); echo json_encode(['error' => 'update_failed']); exit; }

echo json_encode(['ok' => true, 'is_public' => $is_pub === 1, 'public_url' => ($is_pub ? ('view.php?slug=' . $slug) : null)]);

