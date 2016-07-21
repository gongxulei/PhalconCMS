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

    $('.tree-grid').treegrid({
        expanderExpandedClass: 'glyphicon glyphicon-minus',
        expanderCollapsedClass: 'glyphicon glyphicon-plus',
    });

    $('#login-btn').on('click', function(){
        var username = $.trim($('#username').val());
        var usernamePattern = /^[\w-]{4,20}$/i;
        if(!usernamePattern.test(username)){
            tips_message('用户名由4-20个英文字符、数字、中下划线组成');
            return false;
        }

        var password = $.trim($('#password').val());
        if(password.length < 6 || password.length > 32){
            tips_message('密码由6-32个字符组成');
            return false;
        }

        $('#login-form').submit();
    });

    $('#category-btn').on('click', function(){
        var categoryName = $.trim($('#category-name').val());
        var categoryNamePattern = /^[\u4e00-\u9fa5\w-]+$/i;
        if(!categoryNamePattern.test(categoryName)){
            tips_message('分类名称由中英文字符、数字、下划线和横杠组成');
            return false;
        }

        var categorySlug = $.trim($('#category-slug').val());
        var categorySlugPattern = /^[\w-]+$/i;
        if(categorySlug == true && !categorySlugPattern.test(categorySlug)){
            tips_message('分类缩略名由英文字符、数字、下划线和横杠组成');
            return false;
        }

        $('#category-form').submit();
    });

    //$('#category-checkbox').on('click', function(event){
    //    var ischeck = $(this).prop('checked');
    //    $('#category-list-box :checkbox').prop('checked', ischeck);
    //    event.stopPropagation();
    //});

    $('.delete-category').on('click', function(){
        var dataUrl = $.trim($(this).attr('data-url'));
        if(!window.confirm('确定要删除选中分类吗？此操作不可挽回')){
            return false;
        }
        window.location.href = dataUrl;
    });

});

function tips_message(message, level){
    var str = '';
    switch(level){
        case 'error':
            str = '<div class="alert alert-danger">' + message + '</div>';
            break;
        case 'success':
            str = '<div class="alert alert-success">' + message + '</div>';
            break;
        case 'notice':
            str = '<div class="alert alert-info">' + message + '</div>';
            break;
        case 'warning':
            str = '<div class="alert alert-warning">' + message + '</div>';
            break;
        default:
            str = '<div class="alert alert-danger">' + message + '</div>';
            break;
    }
    $('#alert-tips').html(str);
    $('#alert-tips').slideToggle('fast').delay(3000).slideToggle(300);
}
