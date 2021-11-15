function validate() {

    let email = $("#email").val();
    let username = $("#username").val();
    let password = $("#password").val();

    let emailRegex = /[a-zA-Z]+@[a-zA-Z]+\.[a-zA-Z]+/;
    let usernameRegex = /^[a-zA-Z0-9]*$/;

    if (!emailRegex.test(email)) 
        return sendError("Invalid e-mail format");
        
    if (!usernameRegex.test(username))
        return sendError("Username must only contain alphanumeric characters");
    if (username.length < 6 || username.length > 20) 
        return sendError("Username must be between 6 and 20 characters long");
    if (getData(username))
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

function getData(username="") {
    
    let responseData;

    $.ajax({
        url: 'register.php',
        dataType: 'json',
        async: false,
        data: {
            user_exist: username
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData.response;

}