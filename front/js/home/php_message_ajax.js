$('#popoverelement').hide();
$('#sidebar').hide();
$('#edit_profile_modal').hide();
// $('#chat_place').addClass('blur-md');
$('#profile_slide').hide();
$('#popoverelement_admin').hide();

$('#message_form').submit(function(e) {

    e.preventDefault();

    var message = $("#message_in").val();

    if (message !== "") {
        send_message();
    }

});

$('#message_form').keypress(function(e) {

    if (e.which == 13) {

        e.preventDefault();

        var message = $("#message_in").val();

        if (message !== "") {
            send_message();
        }
    }
});

function siedbar_show(){

    $('#sidebar').show();
    $('#chat_place').addClass('blur-md');
}

function siedbar_hide(){

    $('#sidebar').hide();
    $('#chat_place').removeClass('blur-md');
}

function profile_modal_show(){

    $('#sidebar').hide();
    $('#edit_profile_modal').show();
}

function profile_modal_hide(){

    $('#edit_profile_modal').hide();
    $('#chat_place').removeClass('blur-md');
}

function profile_back(){

    $('#edit_profile_modal').hide();
    $('#sidebar').show();
}

function profile_slide_show(){

    $('#profile_slide').show();
}

function profile_slide_hide(){

    $('#profile_slide').hide();
}

function image_options_toggle(){

    $('#options_img').toggle();
}

function delete_profile_pic(inp){

    let img_id = $(inp).attr("id");

    $.ajax({
        method: "POST",
        url: "../../../app/index/profile/delete_profile_pic.php",
        data: {
            image_id: img_id
        },

        success: function(result) {

            if (result === "1") {

                $(inp).parent().parent().remove();
                console.log(img_id)
            }

            console.log(img_id)
            console.log(result)
        },

        error: function(err) {
            console.error(err);
        },
    });
}

get_message();


