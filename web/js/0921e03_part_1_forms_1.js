$(document).ready(function(){
    $("a.contentionCreate").click(function() {
        showPopup('contentionCreate');
        return false;
    });
    $("a.cancel").click(function() {
        console.log($(this).attr("rel"));
        cancelPopup(this.attr("rel"));
        return false;
    });
});

function showPopup(className){
    $(".button."+className).hide();
    $(".popup."+className).show()
        .append('<a href="#" class="cancel" rel="'+className+'">Cancel</a>');
    return false;
}

function cancelPopup(className){
    alert("Hey");
}