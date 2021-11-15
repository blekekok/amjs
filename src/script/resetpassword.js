function requestPasswordReset() {

    let email = $("#email");
    
    if (requestResetPassword(email.val())) {
        sendSuccess("We have sent a reset password link to the provided e-mail. If there is an account associated with the e-mail, the e-mail will be received in the inbox.");
        email.val('');
    }
    
    return false;

}

function sendSuccess(content) {
    $("#success-message").html(content);
    $("#success-message").show();
    return false;
}

function requestResetPassword(email) {
    let responseData;

    $.post({
        url: 'resetpassword.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'reset',
            email: email
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData.response;
}