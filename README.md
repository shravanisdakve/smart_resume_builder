## Build Resume — Professional Resume Builder

A simple, session-based resume builder with a modern UI. Users can register, log in, create their resume with a multi-step form, preview templates, and export to PDF.

### Features
- Account registration and login (PHP + MySQL)
- Session-protected resume builder (`resume.php`)
- Multi-step form with client-side validation
- Live preview and template thumbnails (`templates.html`)
- Section reordering (SortableJS) and PDF export (html2pdf)
- Local draft autosave via `localStorage`

### Requirements
- PHP 8.0+ (CLI or web server)
- MySQL 5.7+/8.0+
- A modern browser with network access for CDNs

### Quick Start
1) Configure database
   - Edit `connect.php` and set `DB_SERVER`, `DB_USERNAME`, `DB_PASSWORD`, `DB_NAME` to your environment.
   - Create database + tables:
     ```bash
     mysql -u <user> -p < schema.sql
     ```

2) Run the PHP development server
   ```bash
   php -S localhost:8000 -t .
   ```

3) Open the app
   - Register: `http://localhost:8000/register.php`
   - Login: `http://localhost:8000/login.html`
   - After login you’ll be redirected to `resume.php` (the builder).

### Project Structure
- `index.html` — marketing homepage
- `login.php` — login page + server-side validation
- `register.php` — registration page + server-side create
- `resume.php` — session gate; serves `resume.html`
- `resume.html` — full resume builder UI
- `logout.php` — ends session and redirects to login
- `reset-password.php` — stub endpoint for password reset
- `templates.html` — template thumbnail previews (reads localStorage)
- `schema.sql` — MySQL schema
- `assets/` — static assets (CSS/JS/images)

### Notes on Auth & Sessions
- Passwords are hashed with `password_hash()` and verified with `password_verify()`.
- Prepared statements are used for DB access.
- `resume.php` checks `$_SESSION['loggedin']` before serving the builder.
- Use HTTPS in production and consider setting secure, same-site cookies at the server/reverse proxy.

### CDN Dependencies
These are loaded in `resume.html`:
- jQuery 3.6.x
- jquery.repeater 1.2.x
- html2pdf 0.10.x
- SortableJS 1.15.x

If you need offline or restricted environments, download these libraries and place them under `assets/vendor/`, then update the corresponding `<script>` tags in `resume.html` to point to local files.

### Troubleshooting
- DB connection error: verify credentials in `connect.php` and import `schema.sql`.
- Headers already sent / redirect not working: ensure no output is sent before `header()` calls.
- Template thumbnails blank: ensure `templates.html` exists (it does) and that your browser allows localStorage.
- PDF export not working: check network access to the html2pdf CDN or switch to a local copy.

### Next Steps (Optional)
- Implement token-based password reset (email link).
- Vendor CDN scripts locally and add integrity checks for production.
- Add CSRF protection and stronger session hardening.
- Convert builder to `resume.php` fully (server-side include) if you want stricter access controls.

