<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>{TITLE}</title>

        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <meta name="viewport" content="initial-scale=1.0, user-scalable=yes" />

        <link type="text/css" rel="stylesheet" href="{TPL_WEB_PATH}/pc.css" />

        <script type="text/javascript" src="/js/jquery-2.1.4.min.js"> </script>
        <script type="text/javascript" src="/js/jquery.flot.min.js"> </script>
        <script type="text/javascript" src="/js/main.js"> </script>
        <!-- BEGIN load_infos -->
        <script type="text/javascript">
            $(window).load(function(){
                loadInfos();
            });
        </script>
        <!-- END load_infos -->
    </head>
    <body>
        <main>
            <header>
                <div id="logo">
                    <a href="index.php"><img src="{TPL_WEB_PATH}/images/{LOGO}" alt="Logo" border="0" /></a>
                    {LOGOUT_LINK}
                </div>
                <div id="infos"></div>
            </header>
