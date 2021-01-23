

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
           let token = response.token;
            localStorage.setItem('token',token)
            window.location = `${BASE_URL}/home/app`;

        },error: function(response) {
            console.log(response.responseJSON)
        }
    });
    
})

//==================== REGISTER ==================== //
$("#register").submit((e)=>{
    e.preventDefault();
    let formData = {
        email: $("#register input[name='login']").val(),
        password: $("#register input[name='password']").val(),
        password_confirm: $("#register input[name='password2']").val()

    }

    $.ajax({
        url: `${BASE_URL}/auth/register`,
        method: "POST",
        dataType: "json",
        data: formData,
        success: function (response) {
           let token = response.token;
            localStorage.setItem('token',token)
            window.location = `${BASE_URL}/home/fillData`;

        }, error: function (response) {
            console.log(response.responseJSON)
        }
    });
})

//==================== FILL ==================== //

$("#fill").submit((e)=>{
    e.preventDefault();
    let formData = {
        name: $("#fill input[name='login']").val(),
        surname: $("#fill input[name='surname']").val(),
        phone: $("#fill input[name='phone']").val(),
        address: $("#fill input[name='address']").val(),
        city: $("#fill input[name='city']").val()

    }

    $.ajax({
        url: `${BASE_URL}/account/editAccount`,
        method: "POST",
        dataType: "json",
        data: formData,
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Bearer", localStorage.token);
        },
        success: function (response) {
        //    let token = response.token;
        //     localStorage.setItem('token',token)

            // window.location = `${BASE_URL}/home/app`;

        }, error: function (response) {
            console.log(response.responseJSON)
        }
    });
})