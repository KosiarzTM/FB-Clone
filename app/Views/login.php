<?= view('partial/header') ?>

<div class="page_outer">
    <div class="page_container">
       
    <div class="tweet_newsfeed_container">
            <div class="tweet_newsfeed_header">
                <h2>Logowanie</h2>
            </div>
            <div class="tweet_newsfeed_stream">

                <div class="tweet_newsfeed_stream_rows_wrapper">
                    <div class="tweet_newsfeed_stream_rows login-wrapper">
                        <div class="information">
                            <div class="second_row login">
                                <form action="" method="post" id='login'>
                                LOGIN:
                                <input type='text' name='login'>
                                HASŁO:
                                <input type='password' name='password'>
                                <input type="submit" value="Wyślij">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?= view('partial/footer') ?>
