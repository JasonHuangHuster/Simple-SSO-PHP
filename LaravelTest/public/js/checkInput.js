function addExamlistCheck() {


    return true;
}


function registCheck() {

    var username = $('[name="username"]').val();
    var password = $('[name="password"]').val();
    var cpassword = $('[name="comfirmPassword"]').val();
    if (username == '' || password == '' || cpassword == '') {

        alert('抱歉！用户名或者密码不能为空！');
        return false;
    } else if (password != cpassword) {
        alert('两次输入密码不一致！请重新输入！');

        return false;
    }

    return true;
}
