function validate() {

    let user = $("#user").val();
    let password = $("#password").val();

    let emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    user = user.replace(/ /g, "");

    if (emailRegex.test(user)) {
        if (!getEmail(user)) 
            return sendError('E-mail is not associated with an account.');
    } else {
        if (!getUsername(user))
            return sendError('Username does not exist.');
    }

    $('login-button').prop('disabled', true);
    if (!getAccountAuth(user, password)) {
        $('login-button').prop('disabled', false);
        return sendError('Invalid password');
    }
    
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

function getAccountAuth(user, pass) {
    let responseData;

    $.post({
        url: 'login.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'acc_auth',
            user: user,
            pass: pass
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData.response;
}