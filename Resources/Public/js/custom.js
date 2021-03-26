$(function() {
    submitComment();
    hashValue();
    onFocusValidation();
    $parentCommentId = '';
    replyComment();
});

function replyComment() {
    $(document).on("click", '.comment-btn.reply', function(event) {
        var parentCommentId = $(this).parent().attr('id');
        $('#'+ parentCommentId + ' .comment-btn.reply').hide();
    });
}

// Scroll to paramlink
function hashValue() {
    // get hash value
    var hash = window.location.hash;
    // now scroll to element with that id
    if (hash != '') {
        $('html, body').stop().animate({
            scrollTop: ($(hash).offset().top)
        }, 2000);
    }
}

// Submit form using ajax
function submitComment() {

    // Submit comment
    $(document).on('submit', '.tx_nsnewscomments #comment-form', function(event) {
        var captcha = $('.tx_nsnewscomments #captcha').val();
        var ajaxURL = $(this).attr('action');
        var datatype = $('.tx_nsnewscomments #dataType').val();
        var commentHTML = $('.active-comment-form').html();
        if (!event.isDefaultPrevented()) {
            if (validateField()) {
                $.ajax({
                    type: 'POST',
                    url: ajaxURL,
                    dataType: datatype,
                    cache:true,
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('.tx_nsnewscomments #submit').attr('disabled', true);
                        $('.tx_nsnewscomments #submit').css('cursor', 'default');
                    },
                    success: function(response) {
                        // GET HTML for comment list
                        $(".tx_nsnewscomments #comments-list").load(location.href + " .tx_nsnewscomments #comments-list>*", function(responseTxt, statusTxt, jqXHR) {
                           if(statusTxt == "success"){
                                // Scroll to comment
                                $.each(response, function(key, val) {
                                    if (val.parentId == '') {
                                        $('.tx_nsnewscomments .thanksmsg').show();
                                        $('html, body').stop().animate({
                                            scrollTop: ($('.tx_nsnewscomments .thanksmsg').offset().top)
                                        }, 2000);
                                        setTimeout(function() {
                                            $('.tx_nsnewscomments .thanksmsg').fadeOut("slow");
                                        }, 7000);
                                    } else {

                                        $('.tx_nsnewscomments .thanksmsg-' + val.parentId).show();
                                        $('html, body').stop().animate({
                                            scrollTop: ($('.tx_nsnewscomments .thanksmsg-' + val.parentId).offset().top)
                                        }, 2000);
                                        setTimeout(function() {
                                            $('.tx_nsnewscomments .thanksmsg-' + val.parentId).fadeOut("slow");
                                        }, 7000);
                                        $('.tx_nsnewscomments #comments-' + val.parentId).fadeIn('slow');
                                        $('.tx_nsnewscomments #parentId').val('');
                                    }
                                });
                            }
                            if(statusTxt == "error"){
                                alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
                            }
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(textStatus + " " + errorThrown);
                    },
                    complete: function(response) {
                        $('.tx_nsnewscomments #comment-form')[0].reset();
                        var captcha = document.getElementById("captcha");
                        if(captcha){
                            refreshCaptcha();
                        }
                        $('.tx_nsnewscomments #submit').attr('disabled', false);
                        $('.tx_nsnewscomments #submit').css('cursor', 'pointer');
                        addForm();
                    },
                });
                event.preventDefault();
            } else {
                return false;
            }
        }
    });

    // Reply form
    $(document).on("click", '.reply', function(event) {
        var parentCommentId = $(this).parent().attr('id');
        var commentHTML = $('.active-comment-form').html();
        $('.active-comment-form .comment-form')[0].reset();
        $('.active-comment-form').html('');
        $('.active-comment-form').removeClass('active-comment-form');
        $(this).parent().parent().parent().parent().find('#reply-form-' + parentCommentId).append(commentHTML);
        $(this).parent().parent().parent().parent().find('#reply-form-' + parentCommentId).addClass('active-comment-form');
        $('#comment-form-close-btn').show();
        removeDefaultValidation();

        // Scroll to form position
        $('html, body').stop().animate({
            scrollTop: ($('.tx_nsnewscomments #reply-form-' + parentCommentId).offset().top)
        }, 1000);

        // Set hidden parentId
        $('.tx_nsnewscomments #parentId').val(parentCommentId);
        onFocusValidation();
    });

    // Close form
    $(document).on("click", ".tx_nsnewscomments #comment-form-close-btn", function(event) {
        var parentCommentIdClose = $('#parentId').val();;
        $('#'+ parentCommentIdClose + ' .comment-btn.reply').show();
        addForm();
    });
}

// Open form on close button click
function addForm() {
    var commentHTML = $('.active-comment-form').html();
    $('.tx_nsnewscomments .active-comment-form').html('');
    $('.tx_nsnewscomments .active-comment-form').removeClass('active-comment-form');
    $('.tx_nsnewscomments #form-comment-view').html(commentHTML);
    $('.tx_nsnewscomments #form-comment-view').addClass('active-comment-form');
    $('.tx_nsnewscomments #comment-form-close-btn').hide();
    $('.tx_nsnewscomments #parentId').val('');
    removeDefaultValidation();
    onFocusValidation();
}

// Custom Validation 
function validateField() {
    var flag = 1;
    var elementObj;
    var captcha = document.getElementById("captcha");
    var terms = document.getElementsByName('tx_nsnewscomments_newscomment[newComment][terms]').length;

    if (!$('.tx_nsnewscomments #name').val()) {
        $(".tx_nsnewscomments #name").parent().addClass('has-error');
        $('.tx_nsnewscomments #name_error').show();
        var flag = 0;
    } else {
        if (!validateName($('.tx_nsnewscomments #name').val())) {
            $(".tx_nsnewscomments #name_error_msg").show();
            $(".tx_nsnewscomments #name_error").hide();
            $(".tx_nsnewscomments #name").parent().addClass('has-error');
            var flag = 0;
        } else {
            $(".tx_nsnewscomments #name").parent().removeClass('has-error');
            $(".tx_nsnewscomments #name_error_msg").hide();
            $(".tx_nsnewscomments #name_error").hide();
        }
    }

    if (!$('.tx_nsnewscomments #email').val()) {
        $(".tx_nsnewscomments #email").parent().addClass('has-error');
        $(".tx_nsnewscomments #email_error").show();
        $(".tx_nsnewscomments #email_error_msg").hide();
        var flag = 0;
    } else {
        if (!validateEmail($('.tx_nsnewscomments #email').val())) {
            $(".tx_nsnewscomments #email_error_msg").show();
            $(".tx_nsnewscomments #email_error").hide();
            $(".tx_nsnewscomments #email").parent().addClass('has-error');
            var flag = 0;
        } else {
            $(".tx_nsnewscomments #email").parent().removeClass('has-error');
        }
    }

    if (!$('.tx_nsnewscomments #comment').val()) {
        $(".tx_nsnewscomments #comment").parent().addClass('has-error');
        $(".tx_nsnewscomments #comment_error").show();
        var flag = 0;
    } else {
        var length = $.trim($(".tx_nsnewscomments #comment").val()).length;
        if (length == 0) {
            $(".tx_nsnewscomments #comment_error").show();
            $(".tx_nsnewscomments #comment").parent().addClass('has-error');
            var flag = 0;
        } else {
            $(".tx_nsnewscomments #comment").parent().removeClass('has-error'); // remove it
        }
    }

    if(captcha){
        if (!$('.tx_nsnewscomments #captcha').val()) {
            $(".tx_nsnewscomments #captcha").parent().addClass('has-error');
            $(".tx_nsnewscomments #captcha_error").show();
            $(".tx_nsnewscomments #captcha_valid_error").hide();
            var flag = 0;
        } else {
            if (validateCaptcha($('.tx_nsnewscomments #captcha').val()) == 'true') {
                $(".tx_nsnewscomments #captcha").parent().removeClass('has-error'); // remove it
            } else {
                $(".tx_nsnewscomments #captcha_valid_error").show();
                $(".tx_nsnewscomments #captcha_error").hide();
                $(".tx_nsnewscomments #captcha").parent().addClass('has-error');
                var flag = 0;
            }
        }
    }

    if (terms) {
        if ( !$('.tx_nsnewscomments input[name="tx_nsnewscomments_newscomment[newComment][terms]"]:checked').length ) {
            $(".tx_nsnewscomments #terms").closest('.ns-form-group').addClass('has-error');
            $(".tx_nsnewscomments #terms_error").show();
            var flag = 0;
        } else {
            $(".tx_nsnewscomments #terms").closest('.ns-form-group').removeClass('has-error');
            $(".tx_nsnewscomments #terms_error").hide();
        }
    }

    if (flag == 1) {
        return true;
    }
}

// Custom validation for onfocus
function onFocusValidation() {

    $(".tx_nsnewscomments #name").focusout(function() {
        elementObj = $(this);
        if (elementObj.val() != '') {
            if (!validateName($('.tx_nsnewscomments #name').val())) {
                $(".tx_nsnewscomments #name_error_msg").show();
                $(".tx_nsnewscomments #name_error").hide();
                $(".tx_nsnewscomments #name").parent().addClass('has-error');
                var flag = 0;
            } else {
                elementObj.parent().removeClass('has-error');
                $(".tx_nsnewscomments #name_error_msg").hide();
                $(".tx_nsnewscomments #name_error").hide();
            }
        } else {
            $(".tx_nsnewscomments #name").parent().addClass('has-error');
            $(".tx_nsnewscomments #name_error").show();
            $(".tx_nsnewscomments #name_error_msg").hide();
        }
    });

    $(".tx_nsnewscomments #email").focusout(function() {
        elementObj = $(this);
        if (elementObj.val() != '') {
            if (!validateEmail($('.tx_nsnewscomments #email').val())) {
                $(".tx_nsnewscomments #email_error_msg").show();
                $(".tx_nsnewscomments #email_error").hide();
                $(".tx_nsnewscomments #email").parent().addClass('has-error');
                var flag = 0;
            } else {
                elementObj.parent().removeClass('has-error');
                $(".tx_nsnewscomments #email_error_msg").hide();
                $(".tx_nsnewscomments #email_error").hide();
            }
        } else {
            $(".tx_nsnewscomments #email").parent().addClass('has-error');
            $(".tx_nsnewscomments #email_error").show();
            $(".tx_nsnewscomments #email_error_msg").hide();
        }
    });

    $(".tx_nsnewscomments #comment").focusout(function() {
        elementObj = $(this);
        if (elementObj.val() != '') {
            var length = $.trim($(".tx_nsnewscomments #comment").val()).length;
            if (length == 0) {
                $(".tx_nsnewscomments #comment_error").show();
                $(".tx_nsnewscomments #comment").parent().addClass('has-error');
                var flag = 0;
            } else {
                $(".tx_nsnewscomments #comment").parent().removeClass('has-error'); // remove it
                $(".tx_nsnewscomments #comment_error").hide();
            }

        } else {
            $(".tx_nsnewscomments #comment").parent().addClass('has-error');
            $(".tx_nsnewscomments #comment_error").show();
        }
    });

    $(".tx_nsnewscomments #captcha").focusout(function() {
        elementObj = $(this);
        if (elementObj.val() != '') {
            var length = $.trim($(".tx_nsnewscomments #captcha").val()).length;
            if (length == 0) {
                $(".tx_nsnewscomments #captcha_error").show();
                $(".tx_nsnewscomments #captcha").parent().addClass('has-error');
                var flag = 0;
            } else {
                $(".tx_nsnewscomments #captcha").parent().removeClass('has-error'); // remove it
                $(".tx_nsnewscomments #captcha_error").hide();
                $(".tx_nsnewscomments #captcha_valid_error").hide();
            }
        } else {
            $(".tx_nsnewscomments #captcha").parent().addClass('has-error');
            $(".tx_nsnewscomments #captcha_error").show();
        }
    });

    $('.tx_nsnewscomments input[name="tx_nsnewscomments_newscomment[newComment][terms]"]').on('change', function(){
        if ( !$('.tx_nsnewscomments input[name="tx_nsnewscomments_newscomment[newComment][terms]"]:checked').length ) {
            $(".tx_nsnewscomments #terms").closest('.ns-form-group').addClass('has-error');
            $(".tx_nsnewscomments #terms_error").show();
            var flag = 0;
        } else {
            $(".tx_nsnewscomments #terms").closest('.ns-form-group').removeClass('has-error');
            $(".tx_nsnewscomments #terms_error").hide();
        }
    });

}

// Remove Default Validation in reply form
function removeDefaultValidation() {
    $(".tx_nsnewscomments #name").parent().removeClass('has-error'); // remove it
    $(".tx_nsnewscomments #name_error").hide();
    $(".tx_nsnewscomments #name_error_msg").hide();

    $(".tx_nsnewscomments #email").parent().removeClass('has-error');
    $(".tx_nsnewscomments #email_error").hide();
    $(".tx_nsnewscomments #email_error_msg").hide();

    $(".tx_nsnewscomments #comment").parent().removeClass('has-error');
    $(".tx_nsnewscomments #comment_error").hide();

    $(".tx_nsnewscomments #captcha").parent().removeClass('has-error');
    $(".tx_nsnewscomments #captcha_error").hide();
    $(".tx_nsnewscomments #captcha_valid_error").hide();
}

// Validate Captcha field using ajax request
function validateCaptcha(captcha) {

    var dataString = 'captcha=' + captcha;
    var url = $('.verification').val();
    var response = $.ajax({
        type: 'POST',
        async: false,
        url: url,
        data: dataString,
        success: function(response) {

        },
        error: function() {
            alert('Captcha not Mathched');
        }
    });
    return response.responseText;
}

// Validate Email field
function validateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return emailReg.test($email);
}

// Validate Name field
function validateName($name) {
    var nameReg = /[^0-9]/g;
    return nameReg.test($name);
}

// Referech Captcha
function refreshCaptcha() {
    var img = document.images['captchaimg'];
    img.src = img.src.substring(0, img.src.lastIndexOf("?")) + "?rand=" + Math.random() * 1000;
}