<?php

    function CreateSession() {

        if (!$_SESSION['session_key']) {

            $_SESSION['role'] = 'guest';

            return;
        }

    }

    function UserExist($conn, $username) {
        $query = $conn->prepare('SELECT username FROM users WHERE username LIKE ?;');
        $query->bind_param('s', $username);
        $query->execute();

        $result = $query->get_result();

        header('Content-Type: application/json');

        if (!$result) {
            return json_encode(array('error' => 'Unable to retrieve user data'));
        }

        return json_encode(array('response' => boolval($result->num_rows)));
    }

    function EmailExist($conn, $email) {
        $query = $conn->prepare('SELECT username FROM users WHERE email LIKE ?;');
        $query->bind_param('s', $email);
        $query->execute();

        $result = $query->get_result();

        header('Content-Type: application/json');

        if (!$result) {
            return json_encode(array('error' => 'Unable to retrieve user data'));
        }

        return json_encode(array('response' => boolval($result->num_rows)));
    }

    function AccountAuth($conn, $user, $pass) {
        $query = $conn->prepare('SELECT password_hash FROM users WHERE username LIKE ? OR email LIKE ?;');
        $query->bind_param('ss', $user, $user);
        $query->execute();

        $result = $query->get_result();

        header('Content-Type: application/json');

        if (!$result) {
            return json_encode(array('error' => 'Unable to retrieve user data'));
        }

        if ($result->num_rows < 1) return array('response' => false);

        $data = $result->fetch_assoc();

        return json_encode(array('response' => boolval(password_verify($pass, $data['password_hash']))));
    }

    function CreateNewUser($conn, $username, $email, $pass) {

        $query = $conn->prepare('INSERT INTO users (username, email, password_hash, verification_token, verification_timestamp) VALUES (?,?,?,?,CURRENT_TIMESTAMP);');

        $hash = getPasswordHash($pass);
        
        // Verification Token
        $verification_token = bin2hex(openssl_random_pseudo_bytes(32));
        
        $query->bind_param('ssss', $username, $email, $hash, $verification_token);
        
        if (!$query->execute()) return false;

        SendVerificationEmail($email, $username, $verification_token);

        return true;

    }

    function VerifyUser($conn, $username, $token) {
        
        $configs = include('src/php/config.php');

        $query = $conn->prepare('SELECT username FROM users WHERE username LIKE ? AND verification_token LIKE ?  AND verified != 1 AND TIMESTAMPDIFF(SECOND, verification_timestamp, NOW()) < ?;');
        $query->bind_param('ssi', $username, $token, $configs['VERIFY_TOKEN_TIMEOUT']);
        $query->execute();

        $result = $query->get_result();

        if (!$result || $result->num_rows < 1) return false;

        $query = $conn->prepare('UPDATE users SET verified=1,verification_token=NULL,verification_timestamp=NULL WHERE username LIKE ? AND verification_token LIKE ?;');
        $query->bind_param('ss', $username, $token);

        if (!$query->execute()) return false;

        return true;

    }

    function CheckResetPasswordToken($conn, $email, $token) {

        $configs = include('src/php/config.php');

        $query = $conn->prepare('SELECT email FROM users WHERE email LIKE ? AND resetpassword_token LIKE ? AND TIMESTAMPDIFF(SECOND, resetpassword_timestamp, NOW()) < ?;');
        $query->bind_param('ssi', $email, $token, $configs['RESETPASSWORD_TOKEN_TIMEOUT']);
        $query->execute();

        $result = $query->get_result();
        
        if (!$result || $result->num_rows < 1) return false;
        
        return true;
    }

    function ChangeUserPassword($conn, $email, $pass, $token) {

        if (!CheckResetPasswordToken($conn, $email, $token)) return false;

        $query = $conn->prepare('UPDATE users SET password_hash=?,resetpassword_token=NULL,resetpassword_timestamp=NULL WHERE email LIKE ? AND resetpassword_token LIKE ?;');

        $hash = getPasswordHash($pass);

        $query->bind_param('sss', $hash, $email, $token);

        if (!$query->execute()) return false;

        return true;

    }

    function RequestResetPassword($conn, $email) {

        $query = $conn->prepare('UPDATE users SET resetpassword_token=?,resetpassword_timestamp=CURRENT_TIMESTAMP WHERE email LIKE ?;');
        
        // Reset password token
        $resetpassword_token = bin2hex(openssl_random_pseudo_bytes(32));

        $query->bind_param('ss', $resetpassword_token, $email);

        if (!$query->execute()) return json_encode(array('response' => false));

        SendResetPasswordEmail($conn, $email, $resetpassword_token);

        return json_encode(array('response' => true));

    }

    function SendResetPasswordEmail($conn, $email, $token) {

        $configs = include('src/php/config.php');

        // Load email template from template file
        $template = 'src/php/resetpassword-template.php';
        $message = file_get_contents($template);

        // Replace all variables
        $temp_var = array('{SITE_ADDRESS}', '{EMAIL}', '{TOKEN}');
        $replace_var = array($configs['SITE_ADDRESS'], $email, $token);
        $message = str_replace($temp_var, $replace_var, $message);

        // Set headers
        $headers = 'From: blekekokkovlek@gmail.com\r\n';
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        // Send Email
        $subject = "Reset Password";
        mail($email, $subject, $message, $headers);
    }

    function AuthenticateUser($conn, $user, $pass) {
        $auth = AccountAuth($conn, $user, $pass);
        return $auth;
    }

    function SendVerificationEmail($email, $username, $token) {

        $configs = include('src/php/config.php');

        // Load email template from template file
        $template = 'src/php/email-verification-template.php';
        $message = file_get_contents($template);

        // Replace all variables
        $temp_var = array('{SITE_ADDRESS}', '{USERNAME}', '{TOKEN}');
        $replace_var = array($configs['SITE_ADDRESS'], $username, $token);
        $message = str_replace($temp_var, $replace_var, $message);

        // Set headers
        $headers = 'From: blekekokkovlek@gmail.com\r\n';
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        // Send Email
        $subject = "Verify your account";
        mail($email, $subject, $message, $headers);
    }
 
    function getPasswordHash($password) {
     
        $configs = include('src/php/config.php');

        // Password Hashing with BCrypt
        $options = [
            'cost' => $configs['PASSWORD_HASH_COST']
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);

    }

?>