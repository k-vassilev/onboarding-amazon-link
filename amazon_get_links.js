jQuery('input#submit').on('click', (e) => {
    const link_target = document.querySelector('input#amazon_link').value;
    const cache_duration_option = document.querySelector('select#cache_duration').value;

    //Defines the data to work with
    let data = {
        action: 'amazon_get_links',
        amazon_link: link_target, 
        cache_duration_option: cache_duration_option
    }
    if ( data != null ) {
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: ajax_object.ajax_url,
            data: data,
            success: function (response) {
                console.log(response.data);
                //adds the html form the response.data to the div element
                jQuery("#my_amazon_div").html(response.data);
            },
            error: function (response) {
                console.log(response);
            }
        });
    };
});


