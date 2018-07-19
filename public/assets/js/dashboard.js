var data = {
    token : $("meta[name='csrf-token']").attr('content')
};

(function ($) {
    $.fn.serializeFormJSON = function () {

        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
})(jQuery);

// login
$(function () {
    $("form#login").validator().submit(function (e) {
        var form = $(this) ;
        if (!e.isDefaultPrevented()) {
            e.preventDefault() ;
            NProgress.start() ;
            var action = $(this).attr('action') ;
            $.ajax({
                url : action ,
                type : "POST" ,
                dataType : "json" ,
                data : $(this).serializeFormJSON() ,
                headers : {
                    "X-CSRF-TOKEN" : data.token
                } ,
                error : function (jqXHR, exception ) {
                    response = jqXHR.responseJSON.errors ;
                    for(i in response)
                    {
                        var input = $("input[name='"+i+"']" , form ) ;
                        var formgroup = input.closest(".form-group") ;
                        formgroup.addClass('has-error has-danger') ;
                        setTimeout(function () {
                            Snackbar.show({
                                text: response[i] ,
                                pos: 'bottom-right',
                                showAction: false ,
                                actionText: "Dismiss",
                                duration: 3000,
                                textColor: '#fff',
                                backgroundColor: '#383838'
                            });
                        },100) ;
                    }
                } ,
                success : function () {
                    window.location.reload(true) ;
                }
            });
            NProgress.done() ;
        }
    });
});

//logout
$(function () {
    $("a#logout").click(function (e) {
        e.preventDefault() ;
        var url = $(this).attr('href') ;
        NProgress.start() ;
        $.ajax({
            url : url,
            dataType : "JSON" ,
            type : "POST" ,
            headers : {
                "X-CSRF-TOKEN" : data.token
            },
            success : function (response) {
                Snackbar.show({
                    text: response.message ,
                    pos: 'bottom-right',
                    showAction: false ,
                    actionText: "Dismiss",
                    duration: 3000,
                });
                if (response.status)
                {
                    setTimeout(function () {
                        window.location.reload(true);
                    } , 1000 );
                }
            }
        });
        NProgress.done() ;
    })
});