async function doAjax(params) {
    let defaults = {
        endpoint: '',
        data: {}
    };

    let options = Object.assign(defaults, params);

    return $.ajax({
        url: `${BASE_URL}${options.endpoint}`,
        method: "POST",
        dataType: "json",
        data: options.data,
        beforeSend: function(xhr) {
            // if(session !)
            // xhr.setRequestHeader("Bearer", session);
        },
        success: function(response) {
            return response

            // if(response.responseJSON)
        },error: function(response) {
            console.log(response.responseJSON)
        }
    });
}
