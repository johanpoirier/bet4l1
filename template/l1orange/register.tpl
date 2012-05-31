<div id="mainarea">
    <div class="maincontent">
        <div id="headline"><h1>S'inscrire</h1></div>
        <div class="ppp">
            <center><span style="color:red;"><b>{WARNING}</b></span></center>

            <form method="post" action="/?op=register">

                <input type="hidden" name="redirect" value="" />
                <br />
                <div class="formfield"><b>Votre nom de famille</b></div>
                <input type="text" name="name" id="name" value="" class="textinput" maxlength="60" /><br /><br />
                <div class="formfield"><b>Votre prénom</b></div>
                <input type="text" name="firstname" id="firstname" value="" class="textinput" maxlength="40" /><br /><br />
                <div class="formfield"><b>Votre email</b></div>
                <input type="text" name="email" id="email"  value="" class="textinput" maxlength="100" /><br /><br />
                <div class="formfield"><b>Votre login</b> <i>(utilisé pour vous connecter au site)</i></div>
                <input type="text" name="login" id="login" value="" class="textinput" maxlength="100" /><br /><br />
                <div class="formfield"><b>Votre mot de passe</b></div>
                <input type="password" name="password1" id="password1" class="textinput" maxlength="20" /><br /><br />
                <div class="formfield"><b>Votre mot de passe à nouveau</b></div>
                <input type="password" name="password2" id="password1" class="textinput" maxlength="20" /><br /><br />

                <input class="imageinput" type="image" src="{TPL_WEB_PATH}/images/register3.gif" value="S'inscrire" />
            </form>
        </div>
    </div>
</div>
