function logout() {
    localStorage.token = null;
    window.location = `${BASE_URL}`;
}

function buildPosts() {
    $('.modal').remove();
    $('.tweet_newsfeed_stream_rows_wrapper').empty();
    $.ajax({
        url: `${BASE_URL}/home/getPosts`,
        method: "POST",
        dataType: "json",
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Bearer", localStorage.token);
        },
        success: function (response) {


            if (response.error != undefined && response.error == "Niepoprawny token")
                logout()

            let data = response.data;
            if (data.length) {

                let posts = '';
                $.each(data, (index, item) => {
                    let template = `<div class="tweet_newsfeed_stream_rows">
                    <div class="icon">
                        <img src="${BASE_URL.split('public')[0]}assets/images/egg.png" class="egg_img">
                    </div>
                    <div class="information">
                        <div class="top_row">
                            <h4 class="tweet_newsfeed_stream_rows_title">${item.name} ${item.surname}</h4>
                            <h3 class="tweet_newsfeed_stream_rows_title_info">${item.date} </h3>
                        </div>
                        <div class="second_row">
                            ${item.post}
                        </div>
                        <div class='commentsection'>`;
                    if (item.comments != undefined) {
                        $.each(item.comments, (comInd, com) => {
                            template += `
                                <div class="top_row">
                                <strong class='datecomment'>${com.date} </strong><br>
                                <h3 class="tweet_newsfeed_stream_rows_title_info">${com.comment} </h3>
                            </div>`;
                        })
                    }

                    template += `</div>
                        <div class="third_row">
                            <div class="expand_action_right">
                                <ul>
                                    <li data-idPost="${item.idPost}"> <i class="fa fa-comment-dots"></i> Komentarz</li>
    
                                    <li data-idPostLike="${item.idPost}"> <i class="fa fa-star"></i> Nice! (${item.likes})</li>
    
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>`

                    posts += template;
                })

                $('.tweet_newsfeed_stream_rows_wrapper').append(posts)
            }
        },
        error: function (response) {
            logout()
        }
    });
}



buildPosts();
$('body').on('click', 'li[data-idPost]', (e) => {
    let target = e.currentTarget;
    console.log(target)
    let template = `<div class='modal'>
    <div class="modalbody">
        <textarea name="comment" cols="50" rows="10"></textarea>
        <div class='modalButtons'>
            <span class="follow_button" data-id = "${target.dataset.idpost}"> <i class="fa  fa-plus"></i> ŚLIJ</span>
            <span class="follow_button cancel"> <i class="fa  fa-minus"></i> ANULUJ</span>
        </div>
    </div>
</div> `;

    $('body').append(template)
})



$('body').on('click', '.modalbody .follow_button[data-id]', (e) => {
    let target = e.currentTarget;
    let id = target.dataset.id
    let postContent = $('.modalbody textarea').val()

    let formData = {
        postId: id,
        commentContent: postContent
    }

    $.ajax({
        url: `${BASE_URL}/home/addComment`,
        method: "POST",
        dataType: "json",
        data: formData,
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Bearer", localStorage.token);
        },
        success: function (response) {
            console.log(response)
            if (response.message) {
                buildPosts();
            }

        },
        error: function (response) {
            console.log(response.responseJSON)
        }
    });

});

$('body').on('click', '.modalbody .follow_button.cancel', (e) => {
    $('.modal').remove();
});

$('body').on('click', '.new_tweet_container .tweet_button', (e) => {

    let formData = {
        postContent: $('.new_tweet_input').val()
    }

    $.ajax({
        url: `${BASE_URL}/home/addPost`,
        method: "POST",
        dataType: "json",
        data: formData,
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Bearer", localStorage.token);
        },
        success: function (response) {
            console.log(response)
            if (response.message) {
                buildPosts();
            }

        },
        error: function (response) {
            console.log(response.responseJSON)
        }
    });
});


$('body').on('click', 'li[data-idPostLike]', (e) => {

    let target = e.currentTarget;

    let formData = {
        postId: target.dataset.idpostlike
    }

    $.ajax({
        url: `${BASE_URL}/home/likePost`,
        method: "POST",
        dataType: "json",
        data: formData,
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Bearer", localStorage.token);
        },
        success: function (response) {
            buildPosts();
        },
        error: function (response) {
            console.log(response.responseJSON)
        }
    });
});


$(document).ready(() => {
    let user = JSON.parse(localStorage.getItem('user'))
    usser = user[0]
    $('.user_name').text(user.name ?? "" + " " + user.surname ?? "")
})

$('body').on('click', '.logout', (e) => {
    e.preventDefault();
    logout();
})

$('.search_input').on('keyup', (e) => {
    let target = e.currentTarget;

    if (target.value != '') {
        $.ajax({
            url: `${BASE_URL}/home/search`,
            method: "POST",
            dataType: "json",
            data: { user: target.value },
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Bearer", localStorage.token);
            },
            success: function (response) {
                if (response.data.length > 0) {
                    let template = '';
                    $('.findList').empty();
                    $('.findList').css('display', 'block');

                    $.each(response.data, (index, item) => {
                        template += `  <div class="second_block_recommendations_rows">
                    <div class="icon_left">
                        <img src="${BASE_URL.split('public')[0]}/assets/images/egg.png" class="egg_img">
                    </div>
                    <div class="info_right">
                        <h3 class="info_right_name">${item.name} ${item.surname}</h3>
                        <p class='findMail'>${item.email}</p>

                        <div class="acceptFriend">
                            <span class="follow_button" data-id="${item.idUser}"> <i class="fa  fa-times"></i> Zaproś!</span><br>
                        </div>
    
                    </div>
                </div>`;
                    })

                    $('.findList').append(template);
                }
            },
            error: function (response) {
                console.log(response.responseJSON)
            }
        });
    } else {
        $('.findList').empty();
        $('.findList').css('display', 'none');
    }
})

$('body').on('click','.findList .acceptFriend .follow_button',(e)=>{
    let target = e.currentTarget;
    let id = target.dataset.id;

    $.ajax({
        url: `${BASE_URL}/account/sendInvite`,
        method: "POST",
        dataType: "json",
        data: {idFriend:id},
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Bearer", localStorage.token);
        },
        success: function (response) {
            console.log(response)
        },
        error: function (response) {
            console.log(response.responseJSON)
        }
    });
})
