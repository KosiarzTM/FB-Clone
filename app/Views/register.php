<?= view('partial/header') ?>

<div class="page_outer">
    <div class="page_container">
       
    <div class="tweet_newsfeed_container">
            <div class="tweet_newsfeed_header">
                <h2>Rejestracja</h2>
            </div>
            <div class="tweet_newsfeed_stream">

                <div class="tweet_newsfeed_stream_rows_wrapper">
                    <div class="tweet_newsfeed_stream_rows login-wrapper">
                        <div class="information">
                            <div class="second_row login">
                                <form action="" method="post" id='register'>
                                LOGIN:
                                <input type='text' name='login'>
                                HASŁO:
                                <input type='password' name='password'>
                                POTWIERDŹ HASŁO:
                                <input type='password' name='password2'>
                                <input type="submit" class ="tweet_button" value="REJESTRUJ">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<script src="<?= base_url().'/assets/js/script.js' ?>"></script>

<?= view('partial/footer') ?>
