function validate() {

    let email = $("#email").val();
    let username = $("#username").val();
    let password = $("#password").val();
    let confirm_password = $("#confirm-password").val();

    let emailRegex = /[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z]+/;
    let usernameRegex = /^[a-zA-Z0-9]*$/;

    if (!emailRegex.test(email)) 
        return sendError("Invalid e-mail format");
    if (getEmail(email))
        return sendError("Email address is already used.");

    if (!usernameRegex.test(username))
        return sendError("Username must only contain alphanumeric characters");
    if (username.length < 6 || username.length > 20) 
        return sendError("Username must be between 6 and 20 characters long");
    if (getUsername(username))
        return sendError("Username is already taken");

    if (password.length < 8)
        return sendError("Password must be at least 8 characters long");
    if (password !== confirm_password)
        return sendError("Please correctly confirm the password");
    
    return true;

}

function sendError(content) {
    $("#error-message").html(content);
    $("#error-message").show();
    return false;
}

function getUsername(username) {
    let responseData;

    $.post({
        url: 'login.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'username_exist',
            username: username
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData.response;
}

function getEmail(email) {
    let responseData;

    $.post({
        url: 'login.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'email_exist',
            email: email
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData.response;
}