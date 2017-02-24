jQuery( document ).ready(function(){
    if ( jQuery( "#languages-list" ).length ) {
        jQuery( "#languages-list .language-option" ).each(function(){
            jQuery( this ).on( "click", function(){
                currentPageID = jQuery( this ).attr( "current-page-id" );
                currentPageLanguage = jQuery( "#languages-list .active" ).attr( "language" );

                pageID = jQuery( this ).attr( "page-id" );
                parentID = jQuery( this ).attr( "parent-id" );
                language = jQuery( this ).attr( "language" );

                jQuery.ajax({
            		url : ajaxurl,
            		type : 'post',
            		data : {
            			action : "get_page_url",
                        current_page_id : currentPageID,
                        current_page_language : currentPageLanguage !== undefined && currentPageLanguage != null && currentPageLanguage != "" ? currentPageLanguage : "none",
            			page_id : pageID,
                        parent_id : parentID,
                        language : language
            		},
            		success : function( response ) {
                        window.location = response;
                    }
            	});
            } );
        });
    }
});
