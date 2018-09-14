var showPerPage = 5;
var currentPage = 0;

function setDisplay(first, last) {
    $('#sectionCommentaire').children().css('display', 'none');
    $('#sectionCommentaire').children().slice(first, last).css('display', 'block');
}

function previous() {
    if($('.active').prev('.page_link').length) {
        goToPage(currentPage - 1);
    }
}

function next() {
    if($('.active').next('page_link').length) {
        goToPage(currentPage + 1);
    }
}

function goToPage(pageNumber) {
    currentPage = pageNumber;

    startFrom = currentPage * showPerPage;
    endOn = startFrom + showPerPage;

    setDisplay(startFrom, endOn);

    $('.active').removeClass('active');
    $('#id' + pageNumber).addClass('active');
}

$(document).ready(function() {
    var numberOfPages = Math.ceil($('#sectionCommentaire').children().length / showPerPage);
    var nav = '<ul class="pagination"><li class="page-item"><a class="page-link" href="javascript::previous();">&laquo;</a></li>';

    var i =  0;

    while(i < numberOfPages) {
        nav += '<li class="page_link page-item';
        if (!i) {
            nav += ' active';
        }
        nav += '" id="id' + i + '">';
        nav += '<a class="page-link" href="javascript:goToPage(' + i + ')">' + (i + 1) + '</a>';
        i++;
    }
    nav += '<li class="page-item"><a class="page-link" href="javascript:next();">&raquo;</a></li></ul>';

    $('#commentsNav').html(nav);
    setDisplay(0, showPerPage);
});