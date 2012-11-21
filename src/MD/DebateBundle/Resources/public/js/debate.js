$(document).ready(function(){
    $(".aff-section").each(function() {
        affHeight = $(this).height();
        negHeight = $(this).siblings(".neg-section").height();
        if (affHeight > negHeight) {
            $(this).sibling(".neg-section").height(affHeight);
        }
        else {
            $(this).height(negHeight);
        }
    });
});