function logout() {
    localStorage.clear()
    window.location = `${BASE_URL}`;
}

function buildFriends() {
    $.ajax({
        url: `${BASE_URL}/account/getFriends`,
        method: "POST",
        dataType: "json",
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Bearer", localStorage.token);
        },
        success: function (response) {

            if (response.data.length > 0) {
                let template = '';
                localStorage.getItem('friendCount', response.data.length)
                $.each(response.data, (index, item) => {
                    template += `                    <div class="second_block_recommendations_rows">
                    <div class="icon_left">
                        <img src="${BASE_URL.split('public')[0]}/assets/images/egg.png" class="egg_img">
                    </div>
                    <div class="info_right">
                        <h3 class="info_right_name">${item.name} ${item.surname}</h3>
                        <div class="acceptFriend flb">
                            <span class="follow_button decline" data-friendid='${item.idUser}'> <i class="fa  fa-times"></i> Exterminate! Exterminate!</span><br>
                        </div>

                    </div>
                </div>`;
                })
                $('.friendList .second_block_recommendations').empty();
                $('.friendList .second_block_recommendations').append(template);
            } else {
                $('.friendList .second_block_recommendations').empty();
            }
        },
        error: function (response) {
            console.log(response.responseJSON)
        }
    });
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
                localStorage.setItem('postcount', data.length)
                let posts = '';
                $.each(data, (index, item) => {
                    let template = `<div class="tweet_newsfeed_stream_rows">
                    <div class="icon">
                        <img src="${BASE_URL.split('public')[0]}assets/images/egg.png" class="egg_img">
                    </div>
                    <div class="information">
                        <div class="top_row postInfo">
                            <div>
                            <h4 class="tweet_newsfeed_stream_rows_title">${item.name} ${item.surname}</h4>
                            <h3 class="tweet_newsfeed_stream_rows_title_info">${item.date} </h3>
                            </div>
                            <div class='actions'>
                            <span class="editpost" data-postid='${item.idPost}'> <i class="fa fa-edit"></i></span>
                                <span class="rmpost" data-postid='${item.idPost}'> <i class="fa fa-trash-alt"></i></span>
                            </div>
                        </div>
                        <div class="second_row postContent">
                            <p>${item.post}<p>
                        </div>
                        <div class='commentsection'>`;
                    if (item.comments != undefined) {
                        $.each(item.comments, (comInd, com) => {
                            template += `
                                <div class="top_row">
                                 <span class='comName'>${com.name} ${com.surname}</span> <strong class='datecomment'>${com.date}</strong><br>
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

function getInvites() {

    $.ajax({
        url: `${BASE_URL}/account/getInvites`,
        method: "POST",
        dataType: "json",
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Bearer", localStorage.token);
        },
        success: function (response) {
            $('.invitations .second_block_recommendations').empty();
            if (response.data.length > 0) {
                let template = '';
                localStorage.setItem('inviteCount', response.data.length)
                $.each(response.data, (index, item) => {
                    template += `<div class="second_block_recommendations_rows">
                    <div class="icon_left">
                        <img src="${BASE_URL.split('public')[0]}/assets/images/egg.png" class="egg_img">
                    </div>
                    <div class="info_right">
                        <h3 class="info_right_name">${item.name} ${item.surname}</h3>
                        <div class="acceptFriend flb">
                            <span class="follow_button accept" data-friendid='${item.idUser}'> <i class="fa fa-plus-circle"></i> Akceptuj</span><br>
                            <span class="follow_button decline" data-friendid='${item.idUser}'> <i class="fa fa-minus-circle"></i> Odrzuć</span>
                        </div>
                
                    </div>
                </div>`;
                })
                $('.invitations .second_block_recommendations').empty();
                $('.invitations .second_block_recommendations').append(template);
            }
        },
        error: function (response) {

            console.log(response.responseJSON)
        }
    });
}

function showModal(target,content = '',editPost = false) {
    let template = `<div class='modal'>
    <div class="modalbody">
        <textarea name="comment" cols="50" rows="10"></textarea>
        <div class='modalButtons'>
            <span class="follow_button"${editPost ? " edit-id" : " data-id"}  = "${target}"> <i class="fa  fa-plus"></i> ŚLIJ</span>
            <span class="follow_button cancel"> <i class="fa  fa-minus"></i> ANULUJ</span>
        </div>
    </div>
</div> `;

    $('body').append(template)

    if(content != '') {
        $('.modalbody textarea').val(content.trim())
    }
}

//=========================

buildPosts();
getInvites();
buildFriends();

// 
setInterval(() => {
    if (localStorage.getItem('inviteCount') != $(".invitations .second_block_recommendations")[0].children.length)
        getInvites();
    if (localStorage.getItem('friendCount') != $(".friendList .second_block_recommendations")[0].children.length)
        buildFriends();
    if (localStorage.getItem('postcount') != $('.tweet_newsfeed_stream_rows_wrapper')[0].children.length)
        buildPosts();
}, 5000);

$('body').on('click', 'li[data-idPost]', (e) => {
    let target = e.currentTarget;
    showModal(target.dataset.idpost);
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
                notify('Dodano komentarz', 'success')

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
                notify(response.message, 'success')
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
            notify(response.message, 'success')
            buildPosts();
        },
        error: function (response) {
            console.log(response.responseJSON)
        }
    });
});

$(document).ready(() => {
    let user = JSON.parse(localStorage.getItem('user'))
    $('.user_name').text((user.name ?? "") + " " + (user.surname ?? ""))
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
                            <span class="follow_button" data-id="${item.idUser}"> <i class="fa  fa-heart"></i> Zaproś!</span><br>
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

$('body').on('click', '.findList .acceptFriend .follow_button', (e) => {
    let target = e.currentTarget;
    let id = target.dataset.id;

    console.log(target, id)
    $.ajax({
        url: `${BASE_URL}/account/sendInvite`,
        method: "POST",
        dataType: "json",
        data: { idFriend: id },
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Bearer", localStorage.token);
        },
        success: function (response) {
            notify(response.message, 'success')
            getInvites();
        },
        error: function (response) {
            console.log(response.responseJSON)
        }
    });
})

$('body').on('click', '.flb .follow_button', (e) => {

    let target = e.currentTarget;
    let endpoint = `${BASE_URL}/account/acceptInvite`

    if ($(target).hasClass('decline')) {
        endpoint = `${BASE_URL}/account/removeFriend`
    }

    let formData = {
        idFriend: target.dataset.friendid
    }

    $.ajax({
        url: endpoint,
        method: "POST",
        dataType: "json",
        data: formData,
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Bearer", localStorage.token);
        },
        success: function (response) {
            notify(response.message, 'success')

            getInvites()
            buildFriends()
        },
        error: function (response) {

            console.error(response.responseJSON)
        }
    });
})

$('body').on('click', '.rmpost', (e) => {
    $.ajax({
        url: `${BASE_URL}/home/removePost`,
        method: "POST",
        dataType: "json",
        data: { postId: e.currentTarget.dataset.postid },
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Bearer", localStorage.token);
        },
        success: function (response) {
            notify(response.message, 'success')

            buildPosts();
        },
        error: function (response) {
            console.log(response.responseJSON)
        }
    });
})

$('body').on('click', '.editpost', (e) => {
    let content = $(e.currentTarget).parents('.information').find('.postContent p').text();
    showModal(e.currentTarget.dataset.postid,content,true)
})

$('body').on('click', '.modalbody .follow_button[edit-id]', (e) => {
    let formData = {
        postContent: $('.modalbody [name="comment"]').val(),
        postId: $(e.currentTarget).attr('edit-id')
    }

    $.ajax({
        url: `${BASE_URL}/home/editPost`,
        method: "POST",
        dataType: "json",
        data: formData,
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Bearer", localStorage.token);
        },
        success: function (response) {
            notify(response.message, 'success')
            buildPosts();
        },
        error: function (response) {
            console.log(response.responseJSON)
        }
    });
})