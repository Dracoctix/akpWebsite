var titreValide = true;
var carteValide = true;
var idValide = true;
var prixValide = true;

function titreEstValide() {
    var longueur = $('#titre').val().length;
    return (longueur > 0 && longueur <= 255);
}

function carteEstValide() {
    var longueur = $('#carte').val().length;
    return (longueur > 0 && longueur <= 255);
}

function idEstValide() {
    var longueur = $('#idcarte').val().length;
    return (longueur > 0 && longueur <= 4);
}

function prixEstValide() {
    var longueur = $('#prix').val().length;
    return (longueur > 0 && longueur <= 9);
}

function testTitre() {
    var test = titreEstValide();
    if(test) {
        $('#titre').removeClass('is-invalid');
        $('#titreTexte').removeClass('invalid-feedback').addClass('form-text').addClass('text-muted');
    }
    else {
        $('#titre').addClass('is-invalid');
        $('#titreTexte').addClass('invalid-feedback').removeClass('form-text').removeClass('text-muted');
    }

    titreValide = test;
    return test;
}

function testCarte() {
    var test = carteEstValide();
    if(test) {
        $('#carte').removeClass('is-invalid');
        $('#carteTexte').removeClass('invalid-feedback').addClass('form-text').addClass('text-muted');
    } else {
        $('#carte').addClass('is-invalid');
        $('#carteTexte').addClass('invalid-feedback').removeClass('form-text').removeClass('text-muted');
    }

    carteValide = test;

    return test;
}

function testId() {
    var test = idEstValide();
    if(test) {
        $('#idcarte').removeClass('is-invalid');
        $('#idTexte').removeClass('invalid-feedback').addClass('form-text').addClass('text-muted');
    } else {
        $('#idcarte').addClass('is-invalid');
        $('#idTexte').addClass('invalid-feedback').removeClass('form-text').removeClass('text-muted');
    }

    idValide = test;

    return test;
}

function testPrix() {
    var test = prixEstValide();
    if(test) {
        $('#prix').removeClass('is-invalid');
        $('#prixTexte').removeClass('invalid-feedback').addClass('form-text').addClass('text-muted');
    }
    else {
        $('#prix').addClass('is-invalid');
        $('#prixTexte').addClass('invalid-feedback').removeClass('form-text').removeClass('text-muted');
    }
}

$('form').on('submit', function() {
    var titre = testTitre();
    var carte = testCarte();
    var id = testId();
    var prix = testPrix();
    return carte && id && titre && prix;
})
    .on('reset', function() {
        carteValide = idValide = titreValide = prixValide = true;
        $('#titre').removeClass('is-invalid');
        $('#titreTexte').removeClass('invalid-feedback').addClass('form-text').addClass('text-muted');
        $('#carte').removeClass('is-invalid');
        $('#carteTexte').removeClass('invalid-feedback').addClass('form-text').addClass('text-muted');
        $('#idcarte').removeClass('is-invalid');
        $('#idTexte').removeClass('invalid-feedback').addClass('form-text').addClass('text-muted');
        $('#prix').removeClass('is-invalid');
        $('#prixTexte').removeClass('invalid-feedback').addClass('form-text').addClass('text-muted');
    });

// On vérifie dans l'immédiat chacun des champs.
$('#titre').on('focusout', function() {
    testTitre();
})
    .on('change paste keyup', function() {
        if(!titreValide) {
            testTitre();
        }
    });

$('#carte').on('focusout', function() {
    testCarte();
})
    .on('change paste keyup', function() {
        if(!carteValide) {
            testCarte();
        }
    });

$('#idcarte').on('focusout', function() {
    testId();
})
    .on('change paste keyup', function() {
        if(!idValide) {
            testId();
        }
    });

$('#prix').on('focusout', function() {
    testPrix();
})
    .on('change paste keyup', function() {
        if(!prixValide) {
            testPrix();
        }
    });