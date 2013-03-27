/*

$(document).ready(function(){
    // General "Create" button for creating new contention, etc.
    $("a.create").click(function() {
        return showPopup($(this));
    });
    // General "Cancel" button for canceling a create form
    $(".cancel").click(function() {
        return cancelPopup($(this));
    });
});

function showPopup(create){
    if (create.parents(".neg-section").length == 0) {
        // This is an aff create button
        affValue = 1;
    }
    else {
        // This is a neg create button
        affValue = 0;
    }
    formSection = create.parents(".debate-section").find(".section-new");
    formSection.find("#contention_aff input[value="+affValue+"]").click();
    formSection.slideDown();
    return false;
}

function cancelPopup(cancel){
    if (cancel.parents.length == 0) {
        return true;
    }
    else {
        cancel.parents(".section-new").slideUp();
        return false;
    }
}*/