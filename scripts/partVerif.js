var stockValide = true;
var stockChefValide = true;

function stockEstValide() {
    var longueur = $('#exemplaires').val().length;
    return (longueur > 0);
}

function stockChefEstValide() {
    var valeur = $('#exemplaireschef').val();
    var valeurNum = parseInt(valeur);
    var longueur = valeur.length;
    return (longueur > 0 &&  valeurNum <= parseInt($('#exemplaires').val()));
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

function testStockChef() {
    var test = stockChefEstValide();
    if(test) {
        $('#exemplaireschef').removeClass('is-invalid');
        $('#exemplaireschefText').removeClass('invalid-feedback').addClass('text-muted');
    } else {
        $('#exemplaireschef').addClass('is-invalid');
        $('#exemplaireschefText').addClass('invalid-feedback').removeClass('text-muted');
    }

    stockChefValide = test;

    return test;
}

$('form').on('submit', function() {
    var stock = testStock();
    var stockChef = testStockChef();
    return stock && stockChef;
})
    .on('reset', function() {
        stockValide = stockChefValide = true;
        $('#exemplaires').removeClass('is-invalid');
        $('#exemplaireschef').removeClass('is-invalid').attr('max', $('#exemplaires').val());
        $('#exemplaireschefText').removeClass('invalid-feedback').addClass('text-muted');
    });

// On vérifie dans l'immédiat chacun des champs.

$('#exemplaires').on('focusout', function() {
    testStock();
    if($('#exemplaireschef').val() !== '') {
        testStockChef();
    }
})
    .on('change paste keyup', function() {
        if(!stockValide) {
            testStock();
        }
        if(!stockChefValide) {
            testStockChef();
        }
        $('#exemplaireschef').attr('max', $('#exemplaires').val());
    });
$('#exemplaireschef').on('focusout', function() {
    testStockChef();
})
    .on('change paste keyup', function() {
        if(!stockChefValide) {
            testStockChef();
        }
    });