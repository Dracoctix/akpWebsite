var commentValide = true;
function testComment() {
    var test = ($('#commentContent').val().length > 3);
    if(test) {
        $('#commentContent').removeClass('is-invalid');
        $('#commentContentText').removeClass('invalid-feedback').addClass('text-muted');
    } else {
        $('#commentContent').addClass('is-invalid');
        $('#commentContentText').addClass('invalid-feedback').removeClass('text-muted');
    }

    commentValide = test;
    return test;
}

$('form').on('submit', function() {
   return testComment();
});

$('#commentContent').on('focusout', function() {
    testComment();
})
    .on('change paste keyup', function() {
        if(!commentValide) {
            testComment();
        }
    });