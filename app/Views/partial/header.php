<!DOCTYPE html>
<html lang="en">

<head>
    <title>FB-CLONE</title>
    <link type="text/css" rel="stylesheet" href="<?= base_url(); ?>/assets/css/style.css">
    <link type="text/css" rel="stylesheet" href="<?= base_url(); ?>/assets/css/reset.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        const BASE_URL = 'http://localhost/projekty/uczelnia/FB-Clone/public';
    </script>
</head>

<body>


    <div class="header_full">
        <div class="header_container">

            <div class='logoContainer'>
                <img src="<?= base_url(); ?>/assets/images/egg.png" class="twitter_logo"> NYAN!
            </div>

            <div class="header_right">
                <form class="header_search" action="">
                    <input class="search_input" type="text" placeholder="Search">
                    <button type="submit" id="Submit"> <i class="fa fa-search"></i> </button>
                </form>
                <div class="findList">                    
                </div>
            </div>

        </div>
    </div>

    <div class='notifyArea'></div>