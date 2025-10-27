<?php
require_once __DIR__ . '/connect.php';

$slug = $_GET['slug'] ?? '';
if ($slug === '') { http_response_code(400); echo 'Bad request'; exit; }

// Find public resume by slug
$stmt = $conn->prepare('SELECT r.id, r.title FROM resumes r WHERE r.public_slug = ? AND r.is_public = 1');
$stmt->bind_param('s', $slug);
$stmt->execute();
$resume = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$resume) { http_response_code(404); echo 'Not found'; exit; }

// Latest version
$v = $conn->prepare('SELECT data_json, created_at FROM resume_versions WHERE resume_id = ? ORDER BY version DESC LIMIT 1');
$v->bind_param('i', $resume['id']);
$v->execute();
$ver = $v->get_result()->fetch_assoc();
$v->close();

$data = $ver ? json_decode($ver['data_json'], true) : [];

function val($arr, $key) { return isset($arr[$key]) ? htmlspecialchars((string)$arr[$key]) : ''; }
function name_from($d){
    $parts = [];
    foreach (['firstname','middlename','lastname'] as $k) { if (!empty($d[$k])) $parts[] = $d[$k]; }
    return htmlspecialchars(implode(' ', $parts));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($resume['title']); ?> — Resume</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        body{ background:#f7f7fb; }
        .wrap{ max-width:1000px; margin:40px auto; padding:0 16px; }
        .card{ background:#fff; border:1px solid #e5e7eb; border-radius:16px; overflow:hidden; box-shadow:0 15px 40px rgba(0,0,0,.08); }
        .header{ background:linear-gradient(135deg,#f8fbff,#eef6ff); padding:24px; }
        .name{ margin:0; font-size:28px; color:#1e2532; font-weight:800; }
        .meta{ margin:8px 0 0; color:#667085; }
        .body{ padding:24px; }
        h2{ font-size:18px; color:#1A91F0; margin:24px 0 8px; }
        ul{ margin:0; padding-left:20px; }
        .muted{ color:#667085; }
    </style>
    <meta name="robots" content="noindex">
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="header">
                <h1 class="name"><?php echo name_from($data) ?: htmlspecialchars($resume['title']); ?></h1>
                <div class="meta">
                    <?php echo val($data,'designation'); ?>
                    <?php if(val($data,'designation') && (val($data,'address')||val($data,'phoneno')||val($data,'email'))) echo ' • '; ?>
                    <?php echo implode(' • ', array_filter([val($data,'address'), val($data,'phoneno'), val($data,'email')])); ?>
                </div>
            </div>
            <div class="body">
                <?php if(val($data,'summary')): ?>
                    <h2>Summary</h2>
                    <p class="muted"><?php echo nl2br(val($data,'summary')); ?></p>
                <?php endif; ?>

                <?php if(!empty($data['experiences'])): ?>
                    <h2>Experience</h2>
                    <?php foreach ((array)$data['experiences'] as $exp): ?>
                        <div>
                            <strong><?php echo htmlspecialchars($exp['exp_title'] ?? ''); ?></strong>
                            <div class="muted"><?php echo htmlspecialchars(($exp['exp_organization'] ?? '').' | '.($exp['exp_location'] ?? '').' | '.($exp['exp_start_date'] ?? '').' - '.($exp['exp_end_date'] ?? '')); ?></div>
                            <?php if (!empty($exp['exp_description'])): ?>
                                <ul><li><?php echo implode('</li><li>', array_map('htmlspecialchars', explode("\n", $exp['exp_description']))); ?></li></ul>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if(!empty($data['educations'])): ?>
                    <h2>Education</h2>
                    <?php foreach ((array)$data['educations'] as $edu): ?>
                        <div>
                            <strong><?php echo htmlspecialchars($edu['edu_degree'] ?? ''); ?></strong>
                            <div class="muted"><?php echo htmlspecialchars(($edu['edu_school'] ?? '').' | '.($edu['edu_city'] ?? '').' | '.($edu['edu_start_date'] ?? '').' - '.($edu['edu_graduation_date'] ?? '')); ?></div>
                            <?php if (!empty($edu['edu_description'])): ?>
                                <ul><li><?php echo implode('</li><li>', array_map('htmlspecialchars', explode("\n", $edu['edu_description']))); ?></li></ul>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if(!empty($data['skills'])): ?>
                    <h2>Skills</h2>
                    <ul>
                        <?php foreach ((array)$data['skills'] as $sk): ?>
                            <li><?php echo htmlspecialchars(is_array($sk) ? ($sk['skill'] ?? '') : $sk); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

