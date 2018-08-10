<section id="mainarea">
  <div class="maincontent">
    <div class="headline">
      <h1>Règlement</h1>
    </div>
  </div>
	<div class="maincontent">
    <p>
      <span class="rule_title">I. Réalisation des votes</span>
      <br />
      Le pronostic d'un match peut être effectué jusqu'à 15 minutes avant le début du match.<br />
      Pour être valide, le pronostic doit comporter le score marqué de chaque équipe.<br />
      <br />
      <br />
    </p>

		<p>
      <span class="rule_title">II. Attribution des points</span>
      <br />
      <p>
        <span class="rule_subtitle">A. Points "résultat" et "score"</span> :
        <ul class="rules">
          <li>Points "résultat" accordés lorsque le joueur a désigné (par le score pronostiqué) le vainqueur du match ou un match nul le cas échéant, ce quels que soient les scores pronostiqué et réel.</li>
          <li>Points "score" accordés lorsque le score pronostiqué et le score réel sont identiques.</li>
        </ul>
        <br />
      </p>

      <p>
        <span class="rule_subtitle">B. Match "Bonus" et match "A l'envers"</span> :
		<ul class="rules">
          <li>Le match "Bonus" permet de doubler les points obtenus avec le pronostic de celui-ci. Il entre en compte dans le classement de la "yannou12" cup.</li>
          <li>Le match "A l'envers" entre en compte dans le classement de la "levieux" cup.</li>
        </ul>
        <br />
	  </p>

      <p>
        <span class="rule_subtitle">C. Synthèse des points par journée</span> :
        <table class="rules">
  			  <tr>
				  <th>Type de match</th>
  				  <th>Pts "résultat match"</th>
  				  <th>Pts "score match"</th>
  				  <th>Total pts / match</th>
  				  <th>Nb de matchs / journée</th>
  				  <th>Total de pts / journée</th>
  			  </tr>
  		 	  <tr>
				  <td>Classique</td>
  				  <td>{NB_PTS_RESULTAT}</td>
  				  <td>{NB_PTS_SCORE}</td>
  				  <td>{NB_TOTAL_MATCH}</td>
  				  <td>{NB_MATCHS_REGULAR}</td>
  				  <td>{TOTAL_PTS}</td>
  			  </tr>
  		 	  <tr>
				  <td>Bonus</td>
  				  <td>{NB_PTS_RESULTAT_BONUS}</td>
  				  <td>{NB_PTS_SCORE_BONUS}</td>
  				  <td>{NB_TOTAL_MATCH_BONUS}</td>
  				  <td>1</td>
  				  <td>{TOTAL_PTS_BONUS}</td>
  			  </tr>
  		 	  <tr>
  				  <td></td>
  				  <td></td>
  				  <td></td>
  				  <td></td>
  				  <td></td>
  				  <td>{TOTAL_PHASE}</td>
  			  </tr>
      	</table>
        <br />
      </p>
    </p>
	</div>
</section>
