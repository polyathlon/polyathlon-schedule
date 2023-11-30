function polyAjaxGetWithId( pid, action ){
    if( !pid ){
        return null;
    }

    var result;
    var sendData = {
        action: action,
        id: pid,
    };

    jQuery.ajax ({
            type		:	'get',
            data        :   sendData,
            url			: 	POLY_AJAX_URL,
            dataType	: 	'json',
            async       :   false,
            success		: 	function( response ){
                result = polyAjaxResponseValidate( response );
                if( result ){
                    var schedule = response.schedule;
                    result = response.schedule;
                }
            },
            error: function( response ){
                alert( JSON.stringify( response ) );
                result = null;
            }
     });

    return result;
}

function polyAjaxSave( data, action ){
    if( !data ){
        return null;
    }

    var result;
    var sendData = {
        action: action,
        data: JSON.stringify( data ),
    };

    jQuery.ajax ({
        type		:	'post',
        data        :   sendData,
        url			: 	POLY_AJAX_URL,
        dataType	: 	'html',
        async       :   false,
        success		:   function( response ){
            try{
                result = JSON.parse( response );
                result = polyAjaxResponseValidate( result );
            }catch( error ){
                result = null;
            }
        },
        error: function( response ){
            alert(JSON.stringify( response ));
            result = null;
        }
    });

    return result;
}

//Helper functions
function polyAjaxResponseValidate( response ){
    if( !response ) return null;

    if( response.status != 'success' ){
        alert( JSON.stringify( response.errormsg ) );
        return null;
    }

    return response;
}
