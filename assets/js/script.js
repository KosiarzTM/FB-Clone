

//==================== LOGIN ==================== //
$("#login").submit((e)=>{
    e.preventDefault();

    let formData = {
        email:$("#login input[name='login']").val(),
        password:$("#login input[name='password']").val()
    };

    $.ajax({
        url: `${BASE_URL}/auth/login`,
        method: "POST",
        dataType: "json",
        data: formData,
        success: function(response) {
            token = response.token;
            localStorage.setItem('token',token)
            window.location = `${BASE_URL}/home/app`;

        },error: function(response) {
            console.log(response.responseJSON)
        }
    });
    
})

