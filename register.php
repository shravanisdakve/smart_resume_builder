<?php
// Registration page (form + processing)
session_start();
require_once 'connect.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $gender    = trim($_POST['gender'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $passwordRaw = $_POST['password'] ?? '';
    $number    = trim($_POST['phone'] ?? '');

    if ($firstName === '' || $lastName === '' || $email === '' || $passwordRaw === '') {
        $error = 'Please fill in all required fields.';
    } else {
        // Check for duplicate email
        if ($stmt = $conn->prepare('SELECT id FROM registration WHERE email = ?')) {
            $stmt->bind_param('s', $email);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $error = 'An account with that email already exists.';
                }
            }
            $stmt->close();
        }

        if ($error === '') {
            $password = password_hash($passwordRaw, PASSWORD_DEFAULT);
            $sql = 'INSERT INTO registration (firstName, lastName, gender, email, password, number) VALUES (?, ?, ?, ?, ?, ?)';
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('ssssss', $firstName, $lastName, $gender, $email, $password, $number);
                if ($stmt->execute()) {
                    header('Location: login.php');
                    exit;
                } else {
                    $error = 'Error creating account. Please try again.';
                }
                $stmt->close();
            } else {
                $error = 'Error preparing statement.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Build Resume</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .register-container { background: white; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden; max-width: 1000px; width: 100%; display: flex; min-height: 720px; }
        .register-left { background: linear-gradient(135deg, #1A91F0 0%, #1170CD 100%); color: white; padding: 60px 40px; flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
        .register-right { padding: 60px 40px; flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .welcome-text { font-size: 2rem; font-weight: 700; margin-bottom: 20px; }
        .welcome-desc { font-size: 1.1rem; opacity: 0.9; line-height: 1.6; margin-bottom: 30px; }
        .feature-list { list-style: none; padding: 0; margin-top: 30px; }
        .feature-list li { padding: 10px 0; display: flex; align-items: center; gap: 10px; }
        .feature-list li:before { content: '✓'; color: #fff; font-weight: bold; font-size: 18px; }
        .register-title { font-size: 2.3rem; font-weight: 700; margin-bottom: 10px; color: #1e2532; }
        .register-subtitle { color: #656e83; margin-bottom: 20px; font-size: 1.05rem; }
        .form-group { margin-bottom: 18px; }
        .form-group label { font-weight: 600; color: #1e2532; margin-bottom: 8px; display: block; }
        .form-control { border: 2px solid #e1e5e9; border-radius: 10px; padding: 12px 14px; font-size: 16px; transition: all 0.2s ease; background: #f8f9fa; width: 100%; outline: none; }
        .form-control:focus { border-color: #1A91F0; box-shadow: 0 0 0 0.2rem rgba(26,145,240,0.15); background: white; }
        .btn-register { background: linear-gradient(135deg, #1A91F0 0%, #1170CD 100%); border: none; border-radius: 10px; padding: 14px 20px; font-size: 16px; font-weight: 600; color: white; width: 100%; transition: all 0.2s ease; text-transform: uppercase; letter-spacing: .5px; cursor: pointer; }
        .btn-register:hover { transform: translateY(-1px); box-shadow: 0 10px 20px rgba(26,145,240,0.25); }
        .btn-login { background: transparent; border: 2px solid #1A91F0; color: #1A91F0; border-radius: 10px; padding: 14px 20px; font-size: 16px; font-weight: 600; width: 100%; transition: all 0.2s ease; text-transform: uppercase; letter-spacing: .5px; margin-top: 12px; text-decoration: none; display: block; text-align: center; }
        .btn-login:hover { background: #1A91F0; color: white; }
        .error-banner { background: #fdecea; color: #b42318; border: 1px solid #f3c1bd; padding: 12px 14px; border-radius: 10px; margin-bottom: 16px; }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
        .gender-group { display: flex; gap: 20px; margin-top: 6px; }
        .gender-option { display: flex; align-items: center; gap: 8px; }
        .gender-option input[type="radio"]{ width: 18px; height: 18px; accent-color: #1A91F0; }
        .error-message { color: #dc3545; font-size: 14px; margin-top: 6px; display: none; }
        .form-control.error { border-color: #dc3545; }
        @media (max-width: 768px) {
            .register-container { flex-direction: column; margin: 10px; }
            .register-left, .register-right { padding: 40px 30px; }
            .register-left { order: 2; }
            .register-right { order: 1; }
            .form-row { flex-direction: column; gap: 0; }
        }
    </style>
    <link rel="icon" href="assets/images/curriculum-vitae.png">
</head>
<body>
    <div class="register-container">
        <div class="register-left">
            <div>
                <h2 class="welcome-text">Join Build Resume</h2>
                <p class="welcome-desc">Start building your professional resume today. Join thousands who already created winning resumes with our platform.</p>
                <ul class="feature-list">
                    <li>Free to use</li>
                    <li>Multiple templates</li>
                    <li>Download as PDF</li>
                    <li>Save and edit anytime</li>
                </ul>
            </div>
        </div>
        <div class="register-right">
            <h2 class="register-title">Create Account</h2>
            <p class="register-subtitle">Fill in your details to get started</p>
            <?php if ($error): ?>
                <div class="error-banner"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form id="registerForm" action="register.php" method="post" novalidate>
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                        <div class="error-message" id="firstNameError">First name is required</div>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name *</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                        <div class="error-message" id="lastNameError">Last name is required</div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <div class="gender-group">
                        <label class="gender-option"><input type="radio" name="gender" value="Male"> Male</label>
                        <label class="gender-option"><input type="radio" name="gender" value="Female"> Female</label>
                        <label class="gender-option"><input type="radio" name="gender" value="Other"> Other</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="error-message" id="emailError">Please enter a valid email address</div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        <div class="error-message" id="passwordError">Password must be at least 6 characters long</div>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password *</label>
                        <input type="password" class="form-control" id="confirmPassword" required minlength="6">
                        <div class="error-message" id="confirmPasswordError">Passwords do not match</div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Optional">
                    <div class="error-message" id="phoneError">Please enter a valid phone number</div>
                </div>
                <div class="form-group" style="display:flex;align-items:center;gap:8px">
                    <input type="checkbox" id="terms" required>
                    <label for="terms" style="margin:0">I agree to the <a href="#">terms and conditions</a></label>
                    <div class="error-message" id="termsError" style="margin-left:auto"></div>
                </div>
                <button type="submit" class="btn-register">Create Account</button>
            </form>
            <a href="login.php" class="btn-login">Already have an account? Login</a>
        </div>
    </div>
    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const firstName = document.getElementById('firstName');
            const lastName = document.getElementById('lastName');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');
            const phone = document.getElementById('phone');
            const terms = document.getElementById('terms');

            let isValid = true;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;

            // clear
            document.querySelectorAll('.error-message').forEach(e => e.style.display='none');
            document.querySelectorAll('.form-control').forEach(i => i.classList.remove('error'));

            if (!firstName.value || firstName.value.trim().length < 2) { document.getElementById('firstNameError').style.display='block'; firstName.classList.add('error'); isValid=false; }
            if (!lastName.value || lastName.value.trim().length < 2) { document.getElementById('lastNameError').style.display='block'; lastName.classList.add('error'); isValid=false; }
            if (!email.value || !emailRegex.test(email.value)) { document.getElementById('emailError').style.display='block'; email.classList.add('error'); isValid=false; }
            if (!password.value || password.value.length < 6) { document.getElementById('passwordError').style.display='block'; password.classList.add('error'); isValid=false; }
            if (confirmPassword.value !== password.value) { document.getElementById('confirmPasswordError').style.display='block'; confirmPassword.classList.add('error'); isValid=false; }
            if (phone.value && !phoneRegex.test(phone.value)) { document.getElementById('phoneError').style.display='block'; phone.classList.add('error'); isValid=false; }
            if (!terms.checked) { document.getElementById('termsError').textContent='You must agree to the terms.'; document.getElementById('termsError').style.display='block'; isValid=false; }

            if (!isValid) { e.preventDefault(); }
        });
    </script>
</body>
</html>
