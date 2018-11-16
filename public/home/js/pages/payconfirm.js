$(function () {
    $(".btn-code").click(function () {
        $(".timeout").show();
        var phoneVal = $("input[name='phone']").val();
        
        if(phoneVal == '') {
            $(".empty-phone").show()
        } else {
            $(this).hide();
            $(".input-phone").hide();
            $(".phonenum").html(phoneVal);
            $(".phonenum").css('display', 'inline-block');
            $(".empty-phone").hide()
        }
    })
})