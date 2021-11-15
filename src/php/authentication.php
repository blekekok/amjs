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

    function CreateNewUser($conn, $username, $email, $password) {

        $query = $conn->prepare('INSERT INTO users (username, email, password_hash, verification_token, verification_timestamp) VALUES (?,?,?,?,CURRENT_TIMESTAMP);');

        // Password Hashing with BCrypt
        $options = [
            'cost' => 16
        ];
        $hash = password_hash($password, PASSWORD_BCRYPT, $options);
        
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

    function AuthenticateUser() {

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

?>