<div id="mainarea">
	<div class="maincontent">
		<div id="headline">
			<h1>Mon Compte</h1>
		</div>
	</div>

  <form id="formProfile" method="POST" action="/?op=update_profile">
    <div class="maincontent">
      <p>
        <span class="rule_title">I. Mes coordonnées</span>
        <br />
        <br />
        <u>Login</u> : {LOGIN}
        <br />
        <br />
        <u>Nom</u> : <input type="text" name="name" value="{NAME}" size="30" />
        <br />
        <br />
        <u>Equipe</u> : {TEAM}
        <br />
        <br />
        <u>E-Mail</u> : <input type="text" name="email" value="{EMAIL}" size="40" />
        <br />
        <br />
        <u>Nouveau mot de passe</u> : <input type="password" name="pwd1" /> (à confirmer pour changer : <input type="password" name="pwd2" />)
      </p>
      <br />
      <br />
      <p>
        <span class="rule_title">II. Mes préférences</span>
        <br />
        <br />
        <input type="checkbox" name="mail_1" value="1" {MAIL_1}/>Recevoir un email de rappel 24H avant le début des matchs si je n'ai pas pronostiqué
        <br />
        <br />
        <input type="checkbox" name="mail_2" value="1" {MAIL_2}/>Recevoir les résultats à la suite du dernier match de chaque journée
      </p>
      <p align="center">
        <font color="#ff0000">{MESSAGE}</font>
        <br /><br />
        <input type="image" src="{IMG_PATH}/submit.gif" name="submit" alt="Valider" />
      </p>
    </div>
  </form>
</div>
