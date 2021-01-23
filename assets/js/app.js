console.log(`${BASE_URL}/home/getPosts`)
$.ajax({
    url: `${BASE_URL}/home/getPosts`,
    method: "POST",
    dataType: "json",
    // data: formData,
    success: function(response) {
        console.log(response.data)
        let data = response.data;
        if(data.length) {

            let posts = '';
            $.each(data,(index,item)=>{
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
                    <div class="third_row">
                        <div class="expand_action_right">
                            <ul>
                                <li data-idPost="${item.idPost}"> <i class="fa fa-reply"></i> Komentarz</li>

                                <li> <i class="fa fa-star"></i> Nice!</li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>`

            posts += template;
            })

            $('.tweet_newsfeed_stream_rows_wrapper').append(posts)
        }
    },error: function(response) {
        console.log(response.responseJSON)
    }
});


$('body').on('click','li[data-idPost]',(e)=>{
    let target = e.currentTarget;
    console.log(target)
    let template = `<div class='modal'>
    <div class="modalbody">
        <textarea name="comment" cols="50" rows="10"></textarea>
        <span class="follow_button" data-id = "${target.dataset.idpost}"> <i class="fa  fa-plus"></i> ŚLIJ<span><br>
    </div>
</div> `;

$('body').append(template)
})



$('body').on('click','.modalbody .follow_button',(e)=>{
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
        beforeSend: function(xhr) {
            xhr.setRequestHeader("Bearer", localStorage.token);
        },
        success: function(response) {
            console.log(response)

            
        },error: function(response) {
            console.log(response.responseJSON)
        }
    });
    
});