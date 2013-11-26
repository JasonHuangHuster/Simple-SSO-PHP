$(function(){

$('.previous').addClass('disabled');

$('#fat-btn')
    .click(function () {
        var btn = $(this)
        btn.button('loading')
        setTimeout(function () {
            btn.button('reset')
        }, 3000)
    });

function distributePapers() {

    $.ajax({
         url:"distributePapers",
         async:false,
         dataType:"json",
         success:function(data){
            alert(data.msg);
            if(data.status==1)
            {
             location.reload() ;    
            }
            
         }

    });
   
    return false;
}



});

