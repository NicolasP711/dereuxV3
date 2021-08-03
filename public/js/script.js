setTimeout(function() {
    $('#successMessage').fadeOut('fast');
}, 10000);

// $('#edit_user_form_save').click(function()
// {
//     var nom = $('#edit_user_form_pseudonym').val();
//     var email = $('#edit_user_form_email').val();
//     const regex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;


//     if($('#edit_user_form_pseudonym').val() === ""){
//     alert("Veuillez renseigner un pseudonyme");
//     return false;
//     }


//     if(nom.length <= 1){
//         alert("Votre pseudonyme doit contenir au minimum 2 caractères");
//         return false;
//     } //Empêche le formulaire d'être soumis//Empêche le formulaire d'être soumis


//     if($('#edit_user_form_email').val() === ""){
//         alert("Veuillez renseigner une adresse email");
//         return false;
//         }


//     if(email.match(regex)){
//         return true;
//     } else {
//         alert("Veuillez renseigner un email valide");
//         return false;
//     }
// });