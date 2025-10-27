<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Resumes</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .page { padding: 30px 0; }
        .header { display:flex; align-items:center; justify-content:space-between; gap:12px; }
        .resumes { margin-top: 24px; display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:16px; }
        .card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,.05); display:flex; flex-direction:column; gap:10px; }
        .card h3{ margin:0; font-size:18px; color:#1e2532; }
        .muted{ color:#656e83; font-size:13px; }
        .row{ display:flex; align-items:center; justify-content:space-between; gap:8px; }
        .actions{ display:flex; gap:8px; flex-wrap:wrap; }
        .btn-sm{ padding:8px 10px; font-size:13px; border-radius:8px; }
        .btn-ghost{ border:1px solid #e1e5e9; color:#1e2532; background:#fff; }
        .btn-ghost:hover{ border-color:#1A91F0; color:#1A91F0; }
        .btn-blue{ background:#1A91F0; color:#fff; border:1px solid #1A91F0; }
        .btn-blue:hover{ background:#1170CD; border-color:#1170CD; }
        .topbar{ background:#fff; box-shadow: rgba(0,0,0,0.08) 0px 3px 8px; }
        .topbar .container{ display:flex; align-items:center; justify-content:space-between; height:64px; }
        .brand{ display:flex; gap:10px; align-items:center; color:#1e2532; font-weight:700; text-decoration:none; }
        .brand img{ width:28px; height:28px; }
    </style>
</head>
<body class="bg-bright">
    <nav class="topbar">
        <div class="container">
            <a class="brand" href="index.html">
                <img src="assets/images/curriculum-vitae.png" alt="">
                <span>build <span class="text-blue">resume.</span></span>
            </a>
            <div>
                <a href="resume.php" class="btn btn-secondary">Builder</a>
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container page">
        <div class="header">
            <h2>Your Resumes</h2>
            <div>
                <button id="createBtn" class="btn btn-primary">Create New</button>
            </div>
        </div>
        <div id="list" class="resumes"></div>
    </div>

    <script>
        async function fetchJSON(url, options){
            const res = await fetch(url, Object.assign({ headers: { 'Content-Type':'application/json' }}, options||{}));
            if(!res.ok) throw new Error('Request failed');
            return res.json();
        }

        function cardTemplate(item){
            const pub = item.is_public == 1;
            return `
                <div class="card">
                    <h3>${item.title}</h3>
                    <div class="row"><span class="muted">Versions: ${item.version_count}</span><span class="muted">Updated: ${new Date(item.updated_at).toLocaleString()}</span></div>
                    <div class="actions">
                        <a class="btn btn-blue btn-sm" href="resume.php?resume_id=${item.id}">Open</a>
                        <button class="btn btn-ghost btn-sm" data-action="clone" data-id="${item.id}">Clone</button>
                        <button class="btn btn-ghost btn-sm" data-action="toggle" data-id="${item.id}" data-public="${pub}">${pub ? 'Make Private' : 'Make Public'}</button>
                        ${pub && item.public_slug ? `<a class="btn btn-ghost btn-sm" target="_blank" href="${item.public_url}">View Link</a>` : ''}
                    </div>
                </div>`;
        }

        async function refresh(){
            const data = await fetchJSON('api/list_resumes.php');
            const list = document.getElementById('list');
            list.innerHTML = data.resumes.map(cardTemplate).join('');
        }

        document.addEventListener('click', async (e)=>{
            const btn = e.target.closest('button');
            if(!btn) return;
            const action = btn.getAttribute('data-action');
            const id = parseInt(btn.getAttribute('data-id'));
            if(action === 'clone') {
                await fetchJSON('api/clone_resume.php', { method:'POST', body: JSON.stringify({ resume_id: id }) });
                refresh();
            } else if(action === 'toggle') {
                const isPub = btn.getAttribute('data-public') === 'true' || btn.getAttribute('data-public') === '1';
                await fetchJSON('api/toggle_public.php', { method:'POST', body: JSON.stringify({ resume_id: id, is_public: !isPub }) });
                refresh();
            }
        });

        document.getElementById('createBtn').addEventListener('click', async ()=>{
            const title = prompt('Title for your new resume');
            if(!title) return;
            const data = JSON.parse(localStorage.getItem('resumeDraft') || '{}');
            const res = await fetchJSON('api/create_resume.php', { method:'POST', body: JSON.stringify({ title, data }) });
            window.location.href = 'resume.php?resume_id=' + res.resume_id;
        });

        refresh();
    </script>
</body>
</html>

