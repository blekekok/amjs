$('#profile-button').on('click', () => {
    $('#profile-tab').show();
    $('#profile-button').prop('class', 'active');
    $('#management-tab').hide();
    $('#management-button').prop('class', '');
});

$('#management-button').on('click', () => {
    $('#profile-tab').hide();
    $('#profile-button').prop('class', '');
    $('#management-tab').show();
    $('#management-button').prop('class', 'active');
});

function profileValidate() {

    let username = $('#username-input').val();

    let usernameRegex = /^[a-zA-Z0-9]*$/;

    if (!usernameRegex.test(username))
        return sendError("Username must only contain alphanumeric characters");
    if (username.length < 6 || username.length > 20) 
        return sendError("Username must be between 6 and 20 characters long");
    if (getUsername(username))
        return sendError("Username is already taken");

}

function changeValidate() {

    let newPassword = $('#newPassword').val();
    let confirmPassword = $('#confirmPassword').val();
    let email = $('#email').val();
    let password = $('#password').val();

    let emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    email = email.replace(/ /g, "");

    if (newPassword.length > 0 || confirmPassword.length > 0) {
        if (newPassword.length < 8)
            return sendError("Password must be at least 8 characters long");
        if (newPassword !== confirmPassword)
            return sendError("Please correctly confirm the password");
    }
        
    if (!emailRegex.test(email)) 
    return sendError("Invalid e-mail format");
    if (getEmail(email))
        return sendError("Email address is already used.");

    if (!getAccountAuth(password))
        return sendError('Invalid password');

}

function sendError(message) {
    alert(message);
    return false;
}

function getUsername(username) {
    let responseData;

    $.post({
        url: 'account.php',
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
        url: 'account.php',
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

function getAccountAuth(pass) {
    let responseData;

    $.post({
        url: 'account.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'acc_auth',
            pass: pass
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData.response;
}