<?php
// =================================================================
// 0. PHP DATABASE HANDLING AND DEBUGGING BLOCK
// This runs BEFORE the HTML is sent to the browser.
// =================================================================

// Display errors for troubleshooting (MUST BE REMOVED after successful testing!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// 1. DATABASE CONNECTION DETAILS (CRITICAL: VERIFY THESE!)
$servername = "localhost";
$username = "root";       
$password = "";           // CHECK: Use the correct password for your XAMPP root user
$dbname = "Campus_Connect"; // CHECK: Must match the database name exactly!

$signup_page = "signup.php"; // Self-referencing file
$login_page = "login.html";
$errorMessage = "";
$successRedirect = false; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 3. CAPTURE AND CLEAN INPUT DATA
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $position = trim($_POST['position'] ?? ''); // Capturing position
    $raw_password = $_POST['password'] ?? ''; 

    // Basic server-side validation 
    if (empty($fullname) || empty($email) || empty($position) || empty($raw_password)) {
        $errorMessage = "All fields are required.";
    } else {
        // 4. HASH THE PASSWORD
        $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

        // 5. DETERMINE USER ROLE (Matches your SQL ENUM: 'admin' or 'officer')
        $db_role = 'officer'; 
        $lower_position = strtolower($position);

        if (strpos($lower_position, 'admin') !== false) {
            $db_role = 'admin';
        } 
        
        // 6. CONNECT TO THE DATABASE
        $conn = new mysqli($servername, $username, $password, $dbname);

        // CRITICAL CHECK 1: Database Connection Failure
        if ($conn->connect_error) {
            die("❌ Connection to MySQL failed. Error: " . $conn->connect_error);
        }
        
        // 7. PREPARE SQL INSERT STATEMENT - TARGETING 'users' TABLE
        // Columns: fullname, email, password, role, is_approved (assuming your new SQL structure)
        $sql = "INSERT INTO usersinfo (fullname, email, password, role, is_approved) 
                VALUES (?, ?, ?, ?, 0)";
                
        $stmt = $conn->prepare($sql);
        
        // CRITICAL CHECK 2: Prepared Statement Failure
        if ($stmt === false) {
            $conn->close();
            die("❌ SQL PREPARATION FAILED. Check if table 'users' and columns exist. Error: " . $conn->error);
        }
        
        // BIND PARAMETERS: fullname, email, HASHED password, DB ENUM role
        $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $db_role);
        
        // 8. EXECUTE AND CHECK FOR ERRORS
        if ($stmt->execute()) {
            // SUCCESS: Data stored. Set flag to trigger JS alert/redirect.
            $successRedirect = true; 
            $stmt->close();
            $conn->close();
        } else {
            // CRITICAL CHECK 3: Execution Failure
            if ($conn->errno == 1062) {
                $errorMessage = "That email address is already registered. Please sign in.";
            } else {
                $errorMessage = "Insertion failed. MySQL Error: " . $stmt->error;
            }
            $stmt->close();
            $conn->close();
        }
    }
}
// Omit closing tag ?> to prevent header output errors
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Government Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* --- START OF ORIGINAL CSS (Verified Clean) --- */
        :root {
            --primary-red: #960b15;
            --cream: #fdfcd7;
            --orange-btn: #cf6828;
            --text-color: #4a4a4a;
            --line-inactive: #5c050b; 
            --line-active: #00ff7f; /* Bright Green for success */
            --error-color: #ff3333; /* Added a color for errors */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--primary-red);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow: hidden; 
        }

        /* Header Curve */
        .header-curve {
            position: absolute;
            top: -55px;
            left: 0;
            width: 100%;
            height: 35%; 
            background-color: var(--cream);
            border-bottom-left-radius: 99% 77%;
            border-bottom-right-radius: 99% 77%;
            z-index: 1;
        }

        .signup-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 400px;
            padding: 20px;
            padding-bottom: 0px;
            display: flex;
            flex-direction: column;
            align-items: center;
            top: -39px;
        }

        /* Logo */
        .logo-container {
            position: relative;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            margin-bottom: 40px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            top: 44px;
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Form Inputs */
        .form-group {
            width: 100%;
            margin-bottom: 15px;
            position: relative;
        }

        input {
            width: 100%;
            padding: 15px;
            border-radius: 10px;
            border: none;
            background-color: var(--cream);
            color: #AC1217;
            font-size: 14px;
            outline: none;
        }
        
        /* CSS for Validation Feedback */
        input.error-border {
            border: 2px solid var(--error-color);
        }

        input::placeholder {
            color: #AC1217;
        }

        input[name="fullname"]::placeholder {
            font-size: 12px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-red);
            cursor: pointer;
        }

        /* Password Validation Bars Section */
        .validation-row {
            display: flex;
            justify-content: space-between;
            width: 100%;
            gap: 10px;
            margin-bottom: 25px;
            margin-top: 5px;
        }

        .val-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .val-line {
            width: 100%;
            height: 4px;
            background-color: var(--line-inactive);
            border-radius: 2px;
            margin-bottom: 5px;
            transition: background-color 0.3s ease;
        }

        /* This is the class Javascript will add */
        .val-line.valid {
            background-color: var(--line-active);
            box-shadow: 0 0 5px var(--line-active); 
        }

        .val-text {
            color: #fffacb;
            font-size: 9px;
            text-align: center;
            white-space: nowrap;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        /* Optional: make text brighter when valid too */
        .val-text.valid-text {
            opacity: 1;
            font-weight: bold;
            color: var(--line-active);
        }

        /* Sign Up Button */
        .btn-signup {
            width: 100%;
            padding: 15px;
            border-radius: 25px;
            border: none;
            background-color: var(--orange-btn);
            color: #fffacb;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            transition: 0.3s;
        }

        .btn-signup:hover {
            background-color: #b5561e;
        }
        
        /* Message displayed below the button */
        #validationMessage {
            color: var(--error-color); 
            font-size: 14px;
            margin-top: 10px;
            font-weight: 600;
            text-align: center;
        }

        /* Footer */
        .footer-text {
            margin-top: 40px;
            margin-bottom: 20px;
            color: #fffacb;
            font-size: 12px;
            text-align: center;
        }

        .footer-text a {
            color: #fffacb;
            text-decoration: underline;
            font-weight: bold;
        }
        /* --- END OF ORIGINAL CSS --- */
    </style>
</head>
<body>

    <div class="header-curve"></div>

    <div class="signup-wrapper">
        <div class="logo-container">
            <img src="images/SSGLOGO.png" alt="Logo" onerror="this.style.display='none'">
        </div>
        
        <form action="signup.php" method="POST" id="signupForm">

            <div class="form-group">
                <input type="email" id="emailInput" name="email" placeholder="Email" required>
            </div>

            <div class="form-group">
                <input type="text" id="fullnameInput" name="fullname" placeholder="Fullname (Lastname, Firstname, M.I.)" required>
            </div>

            <div class="form-group">
                <input type="text" id="roleInput" name="role" placeholder="Role (Officer or Admin)" required>
            </div>

            <div class="form-group">
                <input type="password" id="passwordInput" name="password" placeholder="Password" required>
                <i class="fa-solid fa-eye-slash password-toggle" id="togglePassword"></i>
            </div>

            <div class="validation-row">
                <div class="val-item">
                    <div class="val-line" id="line-length"></div>
                    <span class="val-text" id="text-length">min 8 letters</span>
                </div>
                <div class="val-item">
                    <div class="val-line" id="line-caps"></div>
                    <span class="val-text" id="text-caps">at least 1 capital letter</span>
                </div>
                <div class="val-item">
                    <div class="val-line" id="line-number"></div>
                    <span class="val-text" id="text-number">at least 1 number</span>
                </div>
            </div>

            <button type="submit" class="btn-signup" id="signupButton">Sign up</button>
        </form>
        
        <div id="validationMessage">
            <?php 
                // Display PHP error messages if the script failed to store data
                if (!empty($errorMessage)) {
                    echo htmlspecialchars($errorMessage);
                }
            ?>
        </div>

        <div class="footer-text">
            Do you have already account? <a href="login.html">Sign in</a>
        </div>
    </div>

    <script>
        // Check for submission status immediately when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            const status = params.get('status');
            const messageDiv = document.getElementById('validationMessage');

            if (status === 'success') {
                // SUCCESS: Show alert and redirect
                alert("Account created successfully!");
                window.location.href = 'login.html'; 
                return;
            } else if (status === 'duplicate') {
                messageDiv.textContent = "That email address is already registered. Please sign in.";
            } else if (status === 'db_error' || status === 'insertfail') {
                messageDiv.textContent = "Server error: Database operation failed. Please ensure XAMPP is running.";
            } else if (status === 'fields_error') {
                messageDiv.textContent = "Please fill out all fields.";
            }
            // Clear the status parameter from the URL for a cleaner look
            if (status) {
                history.replaceState(null, '', window.location.pathname);
            }
        });

        // 1. Get all Input and Validation Elements
        const emailInput = document.getElementById('emailInput');
        const fullnameInput = document.getElementById('fullnameInput');
        const roleInput = document.getElementById('roleInput'); // Changed from positionInput
        const passwordInput = document.getElementById('passwordInput');
        const togglePassword = document.querySelector('#togglePassword');
        const signupButton = document.getElementById('signupButton');
        const validationMessage = document.getElementById('validationMessage');
        const signupForm = document.getElementById('signupForm'); 
        
        // Password validation bar elements
        const lineLength = document.getElementById('line-length');
        const lineCaps = document.getElementById('line-caps');
        const lineNumber = document.getElementById('line-number');
        const textLength = document.getElementById('text-length');
        const textCaps = document.getElementById('text-caps');
        const textNumber = document.getElementById('text-number');
        
        // --- VALIDATION FUNCTIONS ---
        
        function checkPasswordValidity(val) {
            const hasMinLength = val.length >= 8;
            const hasCapital = /[A-Z]/.test(val);
            const hasNumber = /\d/.test(val);
            return {
                hasMinLength,
                hasCapital,
                hasNumber,
                isCompletelyValid: hasMinLength && hasCapital && hasNumber
            };
        }

        function isValidEmail(email) {
            return email.includes('@');
        }
        
        // Allows letters, spaces, hyphens, apostrophes, commas, and periods.
        function isValidFullname(name) {
            return /^[a-zA-Z\s-',.]+$/.test(name); 
        }

        // Allows letters, spaces, and hyphens (no numbers/symbols).
        function isValidRole(role) {
            return /^[a-zA-Z\s-]+$/.test(role);
        }

        // --- END VALIDATION FUNCTIONS ---

        // 2. Password Visibility Toggle
        togglePassword.addEventListener('click', function (e) {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // 3. Password Validation Bar Logic (Real-time Feedback)
        passwordInput.addEventListener('input', function() {
            const val = this.value;
            const validation = checkPasswordValidity(val);

            // Update Length
            lineLength.classList.toggle('valid', validation.hasMinLength);
            textLength.classList.toggle('valid-text', validation.hasMinLength);

            // Update Caps
            lineCaps.classList.toggle('valid', validation.hasCapital);
            textCaps.classList.toggle('valid-text', validation.hasCapital);

            // Update Number
            lineNumber.classList.toggle('valid', validation.hasNumber);
            textNumber.classList.toggle('valid-text', validation.hasNumber);
        });
        
        // 4. Input Validation and Submission Logic
        signupButton.addEventListener('click', function(e) {
            // Prevent default HTML submission while we run JS validation
            e.preventDefault(); 
            
            // Clear previous errors
            validationMessage.textContent = '';
            
            let isValid = true;
            let firstErrorInput = null;
            let errorMessage = 'Please fill out all required fields.'; 

            // Clear error styles
            const allInputs = [emailInput, fullnameInput, roleInput, passwordInput]; // Updated input list
            allInputs.forEach(input => input.classList.remove('error-border')); 

            // --- A. EMAIL VALIDATION ---
            if (emailInput.value.trim() === '') {
                isValid = false;
                emailInput.classList.add('error-border');
                if (!firstErrorInput) { firstErrorInput = emailInput; errorMessage = 'Email field cannot be empty.'; }
            } else if (!isValidEmail(emailInput.value.trim())) {
                isValid = false;
                emailInput.classList.add('error-border');
                if (!firstErrorInput) { firstErrorInput = emailInput; errorMessage = 'Email must contain the @ symbol.'; }
            }

            // --- B. FULLNAME VALIDATION ---
            if (fullnameInput.value.trim() === '' && isValid) {
                isValid = false;
                fullnameInput.classList.add('error-border');
                if (!firstErrorInput) { firstErrorInput = fullnameInput; errorMessage = 'Fullname field cannot be empty.'; }
            } else if (!isValidFullname(fullnameInput.value.trim()) && isValid) {
                isValid = false;
                fullnameInput.classList.add('error-border');
                if (!firstErrorInput) { firstErrorInput = fullnameInput; errorMessage = 'Fullname must only contain letters, spaces, hyphens, apostrophes, commas, or periods.'; }
            }

            // --- C. ROLE VALIDATION (Changed from Position) ---
            if (roleInput.value.trim() === '' && isValid) {
                isValid = false;
                roleInput.classList.add('error-border');
                if (!firstErrorInput) { firstErrorInput = roleInput; errorMessage = 'Role field cannot be empty.'; }
            } else if (!isValidRole(roleInput.value.trim()) && isValid) {
                isValid = false;
                roleInput.classList.add('error-border');
                if (!firstErrorInput) { firstErrorInput = roleInput; errorMessage = 'Role must only contain letters, spaces, or hyphens (no numbers or symbols).'; }
            }

            // --- D. PASSWORD VALIDATION ---
            if (passwordInput.value.trim() === '' && isValid) {
                isValid = false;
                passwordInput.classList.add('error-border');
                if (!firstErrorInput) { firstErrorInput = passwordInput; errorMessage = 'Password field cannot be empty.'; }
            } else if (passwordInput.value.trim() !== '' && isValid) {
                const passwordChecks = checkPasswordValidity(passwordInput.value);
                if (!passwordChecks.isCompletelyValid) {
                    isValid = false;
                    passwordInput.classList.add('error-border');
                    if (!firstErrorInput) { firstErrorInput = passwordInput; errorMessage = 'Password must meet all criteria (min 8 chars, 1 capital, 1 number).'; }
                }
            }
            
            // FINAL ACTION: Submit to Server or Display Error
            if (isValid) {
                // SUCCESS: Submits the data to the dedicated processor (process_signup.php)
                signupForm.submit(); 
            } else {
                // FAILURE: Displays specific error message and focuses on the first invalid field.
                validationMessage.textContent = errorMessage;
                
                if (firstErrorInput) {
                    firstErrorInput.focus();
                }
            }
        });
    </script>

</body>
</html>