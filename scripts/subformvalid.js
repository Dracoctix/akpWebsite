var pValide = true;
var mValide = true;
var passValide = true;
var cValide = true;
function isEmail(email){
    // On vérifie l'URL grâce à une regex.
    return /(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/.test( email );
}

function pseudoValide() {
    var longueur = $('#username').val().length;
    return longueur >= 3 && longueur <= 255;
}

function mailValide() {
    return isEmail($('#email').val());
}

function passwordValide() {
    var longueur = $('#password').val().length;
    return longueur > 0 && longueur <= 255;
}

function confirmValide() {
    return $('#password').val() === $('#confirmation').val();
}

function testPseudo() {
    var test = pseudoValide();
    if(test) {
        $('#username').removeClass('is-invalid');
        $('#usernameText').removeClass('invalid-feedback').addClass('text-muted');
    } else {
        $('#username').addClass('is-invalid');
        $('#usernameText').addClass('invalid-feedback').removeClass('text-muted');
    }

    pValide = test;

    return test;
}

function testEmail() {
    var test = mailValide();
    if(test) {
        $('#email').removeClass('is-invalid');
        $('#emailText').removeClass('invalid-feedback').addClass('text-muted');
    }
    else {
        $('#email').addClass('is-invalid');
        $('#emailText').addClass('invalid-feedback').removeClass('text-muted');
    }

    mValide = test;
    return test;
}

function testPassword() {
    var test = passwordValide();
    if(test) {
        $('#password').removeClass('is-invalid');
        $('#passwordText').removeClass('invalid-feedback').addClass('text-muted');
    } else {
        $('#password').addClass('is-invalid');
        $('#passwordText').addClass('invalid-feedback').removeClass('text-muted');
    }

    passValide = test;
    return test;
}

function testConfirmation() {
    var test = confirmValide();
    if(test) {
        $('#confirmation').removeClass('is-invalid');
        $('#confirmationText').removeClass('invalid-feedback').addClass('text-muted');
    } else {
        $('#confirmation').addClass('is-invalid');
        $('#confirmationText').addClass('invalid-feedback').removeClass('text-muted');
    }

    cValide = test;
    return test;
}

$('form').on('submit', function() {
    var pseudo = testPseudo();
    var email = testEmail();
    var pass = testPassword();
    var confirmation = testConfirmation();
    return pseudo && email && pass && confirmation;
})
    .on('reset', function() {
        pValide = mValide = passValide = cValide = true;
        $('#email').removeClass('is-invalid');
        $('#emailText').removeClass('invalid-feedback').addClass('text-muted');
        $('#username').removeClass('is-invalid');
        $('#usernameText').removeClass('invalid-feedback').addClass('text-muted');
        $('#password').removeClass('is-invalid');
        $('#passwordText').removeClass('invalid-feedback').addClass('text-muted');
        $('#confirmation').removeClass('is-invalid');
        $('#confirmationText').removeClass('invalid-feedback').addClass('text-muted');
    });

// On vérifie dans l'immédiat le champ username.
$('#username').on('focusout', function() {
    testPseudo();
})
    .on('change paste keyup', function() {
        if(!pValide) {
            testPseudo();
        }
    });

$('#email').on('focusout', function() {
    testEmail();
})
    .on('change paste keyup', function() {
        if(!mValide) {
            testEmail();
        }
    });

$('#password').on('focusout', function() {
    testPassword();
})
    .on('change paste keyup', function() {
        if(!passValide) {
            testPassword();
        }
    });
$('#confirmation').on('focusout', function() {
    testConfirmation();
})
    .on('change paste keyup', function() {
        if(!cValide) {
            testConfirmation();
        }
    });