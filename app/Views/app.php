<?= view('partial/header') ?>
<script src="<?= base_url().'/assets/js/app.js' ?>"></script>


<div class="page_outer">
    <div class="page_container">
        <div class="profile_container">

            <div class="top_block">
                <div class="mini_profile">
                    <img src="<?= base_url(); ?>/assets/images/egg.png" class="user_photo">
                    <h3 class="user_name">Imie Nazwisko</h3>
                </div>

                <div class="quick_tweet_box">
                    <form class="new_tweet_container" action="/search">
                        <textarea class="new_tweet_input" cols="33" rows="2" type="text" placeholder=" Compose New Tweet..."></textarea>
                        <li class="tweet_button"> <i class="fa fa-pencil-square-o"></i> Wyślij</li>
                    </form>
                </div>
            </div>

            <div class="second_block">
                <div class="second_block_title">
                    <h2>Zaproszenia </h2>
                </div>
                <div class="second_block_recommendations">
                    <div class="second_block_recommendations_rows">
                        <div class="icon_left">
                            <img src="<?= base_url(); ?>/assets/images/egg.png" class="egg_img">
                        </div>
                        <div class="info_right">
                            <h3 class="info_right_name">Makers Academy</h3>
                            <div class="acceptFriend">
                            <span class="follow_button"> <i class="fa fa-plus-circle"></i> Akceptuj</span><br>
                            <span class="follow_button"> <i class="fa fa-minus-circle"></i> Odrzuć</span>
                            </div>
                       
                        </div>
                    </div>
                </div>
                <div class="second_block_footer">
                </div>
            </div>

            <div class="third_block">
                <div class="second_block_recommendations">
                    <div class="second_block_recommendations_rows">
                        <div class="icon_left">
                            <img src="<?= base_url(); ?>/assets/images/egg.png" class="egg_img">
                        </div>
                        <div class="info_right">
                            <h3 class="info_right_name">Makers Academy</h3>
                            <div class="acceptFriend">
                            <span class="follow_button"> <i class="fa  fa-times"></i> Usuń znajomego</span><br>
                            </div>
                       
                        </div>
                    </div>
                </div>
                <div class="second_block_footer">
                </div>
            </div>

            <div class="fourth_block">
            </div>

        </div>

        <div class="tweet_newsfeed_container">
            <div class="tweet_newsfeed_header">
                <h2>Posty</h2>
            </div>
            <div class="tweet_newsfeed_stream">

                <div class="tweet_newsfeed_stream_rows_wrapper">

                </div>

            </div>
        </div>
        <br clear='all'>

    </div>
</div>

<?= view('partial/footer') ?>