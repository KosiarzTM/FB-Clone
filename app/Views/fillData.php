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
                                <form action="" method="post" id='fill'>
                                IMIE:
                                <input type='text' name='name'>
                                NAZWISKO:
                                <input type='text' name='surname'>
                                TELEFON:
                                <input type='text' name='phone'>
                                ADRES:
                                <input type='text' name='address'>
                                MIASTO:
                                <input type='text' name='city'>
                                <input type="submit" class ="tweet_button" value="ZMIEŃ">
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
