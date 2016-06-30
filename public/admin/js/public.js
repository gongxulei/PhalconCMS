$(document).ready(function(){
    (function(){
        var alertTips = $.trim($('#alert-tips').html());
        if(typeof alertTips != 'undefined' && alertTips != null && alertTips){
            var classname = $('#alert-tips div').attr('class');
            switch(classname){
                case 'errorMessage':
                    $('#alert-tips div').addClass('alert').addClass('alert-danger');
                    break;
                case 'successMessage':
                    $('#alert-tips div').addClass('alert').addClass('alert-success');
                    break;
                case 'noticeMessage':
                    $('#alert-tips div').addClass('alert').addClass('alert-info');
                    break;
                case 'warningMessage':
                    $('#alert-tips div').addClass('alert').addClass('alert-warning');
                    break;
                default:
                    $('#alert-tips div').addClass('alert').addClass('alert-info');
                    break;
            }
            $('#alert-tips').slideToggle(0).delay(3000).slideToggle(300);
        }
    })();

    $('#login-btn').on('click', function(){
        var username = $.trim($('#username').val());
        var usernamePattern = /^[\w-]{4,20}$/i;
        if(!usernamePattern.test(username)){
            $('#alert-tips').html('用户名由4-20个英文字符、数字、中下划线组成');
            $('#alert-tips').slideToggle('fast').delay(3000).slideToggle(300);
            return false;
        }

        var password = $.trim($('#password').val());
        if(password.length < 6 || password.length > 32){
            $('#alert-tips').html('密码由6-32个字符组成');
            $('#alert-tips').slideToggle('fast').delay(3000).slideToggle(300);
            return false;
        }

        $('#login-form').submit();
    });

});