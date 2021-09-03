var is_delete = false;

$('.todo-name').css('color', 'transparent');

$('input[type=text]').not('.user-name').blur(function() {
    $(this).css('color', 'transparent');
});

$('input[type=text]').focus(function() {
    $(this).css('color', 'black');
});

$('.btn-check').click(function() {
    $(this).parent().toggleClass('completed');
})

$('.btn-remove').click(function() {
    is_delete = true;
    $(this).parent().parent().addClass('fall');
})

$('.btn-search').click(function() {
    $('.todo-search').val($('.todo-name').val());
})

$('.content-item').on('transitionend webkitTransitionEnd oTransitionEnd', function () {
    if(is_delete) {
        $(this).remove();
        isEmpty($('.app-content'));
    }
    is_delete=false;
});


$('.option-link').click(function() {
    $('.option-current').text($(this).text());
})

$('.options').click(function() {
    let optionList = $(this).find('.option-list');
    $('.fa-chevron-down').toggleClass('rotate');
    optionList.fadeToggle(100, "linear", function() {
        optionList.toggleClass('active');
    })
})

//Func
function isEmpty(el){
    if(!el.children().hasClass("content-item")) {
        $('.symbol-null').css('display', 'block');
    }
}

isEmpty($('.app-content'));

$(window).click(function(e) {
    if(!$(e.target).is('.options, .option-current, .fa-chevron-down, .option-link, .frm-filter')) {
        $('.option-list').fadeOut(100, "linear", function() {
            $('.option-list').removeClass('active');
        })
        $('.fa-chevron-down').removeClass('rotate');
    }

    if(!$(e.target).is('.btn-calendar, .fa-calendar, .choice-due, .todo-date, .options-date, .btn-handler')) {
        $('.choice-due').removeClass('active-calendar');
    }

    if($(e.target).is('.modal')) {
        $('.modal').removeClass('active-modal');
    }
});

$('.open-change').click(function() {
    $('.modal-change').addClass('active-modal');
})

$('.open-sign-out').click(function() {
    $('.modal-sign-out').addClass('active-modal');
})

$('.btn-cancel').click(function() {
    $('.modal').removeClass('active-modal');
})

function is_numeric(str){
    return /^\d+$/.test(str);
}

function isSameName(str) {
    let check = false;
    $('.item-name').each(function() {
        if($(this).text() === str) {
            check=true;
        } 
    })
    return check;
}

var isSubmit = true;
$('.btn-create').click(function() {
    $('.frm-create').submit(function(e){
        let textSubmit = $(this).find('input').val();

        let alert = $(`<div class="alert"><span></span>          
            <span class="btn-close">&times</span>    
            </div>`);
        if(is_numeric(textSubmit)) {
            isSubmit = false;
            $(alert).children('span:not(.btn-close)').text("Can't be a number");
        }   

        if(textSubmit === '') {
            isSubmit = false;
            $(alert).children('span:not(.btn-close)').text('Please fill in the name');
        }

        if(isSameName(textSubmit)) {
            isSubmit = false;
            $(alert).children('span:not(.btn-close)').text('This name is already on the list');
        }

        if(!isSubmit) {
            e.preventDefault();
            $('.app').append(alert);
            $('.btn-close').click(function() { $(this).parent().remove(); })
            
            setTimeout(() => { alert.remove(); }, 3000);
        }

        isSubmit = true;
    });
})

$('.option-link').click(function() {
    $('.todo-type').val($(this).text());    

    $('.frm-filter').submit();
})

$('.btn-calendar').click(function() {
    $('.active-calendar').removeClass('active-calendar');

    $(this).siblings('.choice-due').addClass('active-calendar');

    let dueTime = $(this).parent().siblings('.item-time').find('.due-time').text();

    if(dueTime !== '?') {
        $(this).siblings('.choice-due').find('input').val(dueTime.replace(/[/]/g, '-'));
    }
})

$('.btn-delete').click(function() {
    $(this).parent().siblings('input').val('');
})

$('.btn-options-login').click(function() {
    $('.login-list').toggleClass('active-login');
})

// $('.btn-change').click(function() {
//     $('.frm-change').submit(function(e) {
//         if($('.frm-change-error').length > 0) {
//             return false;
//         }else {
//             return true;
//         }
//     })
// })