var carteValide = true;
var idValide = true;
var stockValide = true;
var objectifValide = true;

function carteEstValide() {
    var longueur = $('#cardName').val().length;
    return (longueur > 0 && longueur <= 255);
}

function idEstValide() {
    var longueur = $('#cardId').val().length;
    return (longueur > 0 && longueur <= 4);
}

function stockEstValide() {
    var longueur = $('#exemplaires').val().length;
    return (longueur > 0 && longueur <= 7);
}

function objectifEstValide() {
    var longueur = $('#objectif').val().length;
    return (longueur > 0 && longueur <= 7);
}

function testCarte() {
    var test = carteEstValide();
    if(test) {
        $('#cardName').removeClass('is-invalid');
    } else {
        $('#cardName').addClass('is-invalid');
    }

    carteValide = test;

    return test;
}

function testId() {
    var test = idEstValide();
    if(test) {
        $('#cardId').removeClass('is-invalid');
    } else {
        $('#cardId').addClass('is-invalid');
    }

    idValide = test;

    return test;
}

function testStock() {
    var test = stockEstValide();
    if(test) {
        $('#exemplaires').removeClass('is-invalid');
    } else {
        $('#exemplaires').addClass('is-invalid');
    }

    stockValide = test;

    return test;
}

function testObjectif() {
    var test = objectifEstValide();
    if(test) {
        $('#objectif').removeClass('is-invalid');
    } else {
        $('#objectif').addClass('is-invalid');
    }

    objectifValide = test;

    return test;
}

$('form').on('submit', function() {
    var carte = testCarte();
    var id = testId();
    var stock = testStock();
    var objectif = testObjectif();
    return carte && id && stock && objectif;
})
    .on('reset', function() {
        carteValide = idValide = stockValide = objectifValide = true;
        $('#cardName').removeClass('is-invalid');
        $('#cardId').removeClass('is-invalid');
        $('#exemplaires').removeClass('is-invalid');
        $('#objectif').removeClass('is-invalid');
    });

// On vérifie dans l'immédiat chacun des champs.
$('#cardName').on('focusout', function() {
    testCarte();
})
    .on('change paste keyup', function() {
        if(!carteValide) {
            testCarte();
        }
    });

$('#cardId').on('focusout', function() {
    testId();
})
    .on('change paste keyup', function() {
        if(!idValide) {
            testId();
        }
    });

$('#exemplaires').on('focusout', function() {
    testStock();
})
    .on('change paste keyup', function() {
        if(!stockValide) {
            testStock();
        }
    });
$('#objectif').on('focusout', function() {
    testObjectif();
})
    .on('change paste keyup', function() {
        if(!objectifValide) {
            testObjectif();
        }
    });