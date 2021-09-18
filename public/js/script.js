setTimeout(function() {
    $('#successMessage').fadeOut('fast');
}, 10000);

function toggleConnexionPassword()
{
    var input = document.getElementById("inputPassword");
    var toggle = document.getElementById("togglePassword");

    if (input.type === "password") {
        input.type = "text";
        toggle.classList.remove("fas", "fa-eye");
        toggle.classList.add("fas", "fa-eye-slash");
    } else {
        input.type = "password";
        toggle.classList.remove("fas", "fa-eye-slash");
        toggle.classList.add("fas", "fa-eye");
    }
}

function toggleCurrentPassword()
{
    var actualPassword = document.getElementById("edit_user_form_currentpassword");
    var toggle = document.getElementById("togglePassword");

    if (actualPassword.type === "password") {
        actualPassword.type = "text";
        toggle.classList.remove("fas", "fa-eye");
        toggle.classList.add("fas", "fa-eye-slash");
    } else {
        actualPassword.type = "password";
        toggle.classList.remove("fas", "fa-eye-slash");
        toggle.classList.add("fas", "fa-eye");
    }
}

function toggleCurrentPasswordEditPassword()
{
    var actualPassword = document.getElementById("edit_password_form_currentpassword");
    var toggle = document.getElementById("togglePassword");

    if (actualPassword.type === "password") {
        actualPassword.type = "text";
        toggle.classList.remove("fas", "fa-eye");
        toggle.classList.add("fas", "fa-eye-slash");
    } else {
        actualPassword.type = "password";
        toggle.classList.remove("fas", "fa-eye-slash");
        toggle.classList.add("fas", "fa-eye");
    }
}

function toggleRegisterPassword1()
{
    var actualPassword = document.getElementById("registration_form_plainPassword_password");
    var toggle = document.getElementById("togglePassword");

    if (actualPassword.type === "password") {
        actualPassword.type = "text";
        toggle.classList.remove("fas", "fa-eye");
        toggle.classList.add("fas", "fa-eye-slash");
    } else {
        actualPassword.type = "password";
        toggle.classList.remove("fas", "fa-eye-slash");
        toggle.classList.add("fas", "fa-eye");
    }
}

function toggleRegisterPassword2()
{
    var actualPassword = document.getElementById("registration_form_plainPassword_confirm");
    var toggle = document.getElementById("togglePassword1");

    if (actualPassword.type === "password") {
        actualPassword.type = "text";
        toggle.classList.remove("fas", "fa-eye");
        toggle.classList.add("fas", "fa-eye-slash");
    } else {
        actualPassword.type = "password";
        toggle.classList.remove("fas", "fa-eye-slash");
        toggle.classList.add("fas", "fa-eye");
    }
}

function toggleCurrentPassword3()
{
    var actualPassword = document.getElementById("edit_password_form_plainPassword_password");
    var toggle = document.getElementById("togglePassword1");

    if (actualPassword.type === "password") {
        actualPassword.type = "text";
        toggle.classList.remove("fas", "fa-eye");
        toggle.classList.add("fas", "fa-eye-slash");
    } else {
        actualPassword.type = "password";
        toggle.classList.remove("fas", "fa-eye-slash");
        toggle.classList.add("fas", "fa-eye");
    }
}

function toggleCurrentPassword4()
{
    var actualPassword = document.getElementById("edit_password_form_plainPassword_confirm");
    var toggle = document.getElementById("togglePassword2");

    if (actualPassword.type === "password") {
        actualPassword.type = "text";
        toggle.classList.remove("fas", "fa-eye");
        toggle.classList.add("fas", "fa-eye-slash");
    } else {
        actualPassword.type = "password";
        toggle.classList.remove("fas", "fa-eye-slash");
        toggle.classList.add("fas", "fa-eye");
    }
}