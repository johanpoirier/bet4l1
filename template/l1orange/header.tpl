<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
    <head>
        <title>{TITLE}</title>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <link type="text/css" rel="stylesheet" href="{TPL_WEB_PATH}/pc.css" />
        <script type="text/javascript" src="/js/jquery.js"> </script>
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
        <div id="main">
            <div id="head1">
                <div id="logo">
                    <a href="index.php"><img src="{TPL_WEB_PATH}/images/{LOGO}" alt="Logo" border="0" /></a>
                    {LOGOUT_LINK}
                </div>
                <div id="infos"></div>
            </div>
            <br />

