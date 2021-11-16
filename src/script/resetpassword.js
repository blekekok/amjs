function requestPasswordReset() {

    let email = $("#email");
    
    if (requestResetPassword(email.val())) {
        sendSuccess("We have sent a reset password link to the provided e-mail. If there is an account associated with the e-mail, the e-mail will be received in the inbox.");
        email.val('');
    }
    
    return false;

}

function resetPassword() {

    let password = $("#password").val();
    let confirm_password = $("#confirm-password").val();

    if (password.length < 8)
        return sendError("Password must be at least 8 characters long");
    if (password !== confirm_password)
        return sendError("Please correctly confirm the password");

    return true;

}

function sendSuccess(content) {
    $("#success-message").html(content);
    $("#success-message").show();
    return false;
}

function sendError(content) {
    $("#error-message").html(content);
    $("#error-message").show();
    return false;
}

function requestResetPassword(email) {
    let responseData;

    $.post({
        url: 'resetpassword.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'request',
            email: email
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData.response;
}