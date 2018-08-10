<section id="mainarea">
    <div class="maincontent">
        <div class="headline">
            <div class="headline-title">
                <h1>S'inscrire</h1>
            </div>
        </div>

        <div class="register">
            <form method="post" action="/?op=register">
                <span class="register-warning">{WARNING}</span>

                <input type="hidden" name="redirect" value="" />
                <input type="hidden" name="code" value="{CODE}" />

                <input type="text" name="name" id="name" value="" class="textinput" maxlength="60" placeholder="Votre nom de famille" />

                <input type="text" name="firstname" id="firstname" value="" class="textinput" maxlength="40" placeholder="Votre prénom" />

                <input type="email" name="email" id="email"  value="{EMAIL}" class="textinput" maxlength="100" placeholder="Votre adresse email" />

                <input type="text" name="login" id="login" value="" class="textinput" minlength="3" maxlength="100" placeholder="Votre login" />
                <span class="hint">(utilisé pour vous connecter au site)</span>

                <input type="password" name="password1" id="password1" class="textinput" minlength="6" maxlength="100" placeholder="Votre mot de passe" />
                <input type="password" name="password2" id="password1" class="textinput" minlength="6" maxlength="100" placeholder="Votre mot de passe à nouveau" />

                <input type="submit" value="S'inscrire"/>
            </form>
        </div>
    </div>
</section>
