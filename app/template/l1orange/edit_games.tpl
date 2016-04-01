<div id="mainarea">
  <div class="maincontent">
    <div id="headline"><h1>Matchs</h1></div>

    <div class="tag_cloud">
      <form id="add_phase" action="?op=add_phase" method="post">
        Ajouter une phase : <input type="text" name="phase_label" size="12" value="" />
        <input type="submit" value="OK" />
      </form>
    </div>

    <div class="tag_cloud">
      <form id="move_phase" action="?op=move_phase" method="post">
        Déplacer la phase :
        <select name="phaseToMove" id="phaseToMove">
        <!-- BEGIN phases -->
            <option value="{phases.ID}"{phases.SELECTED}>{phases.NAME}</option>
        <!-- END phases -->
        </select>
        après la phase :
        <select name="phaseRef" id="phaseRef">
        <!-- BEGIN phases -->
            <option value="{phases.ID}">{phases.NAME}</option>
        <!-- END phases -->
        </select>
        <input type="submit" value="OK" />
      </form>
    </div>

  	<form id="add_team" action="?op=add_match" method="post">
      <input type="hidden" id="idMatch" name="idMatch" value="" />
      <div class="tag_cloud">
  		  <table width="100%">
    			<tr>
      			<td colspan="2" width="100%">Date :</td>
      		</tr>
    			<tr>
    				<td colspan="2">
    					<select name="day" id="day">
                <!-- BEGIN days -->
    						<option value="{days.VALUE}"{days.SELECTED}>{days.VALUE}</option>
    					  <!-- END days -->
    					</select>
    					<select name="month" id="month">
                <!-- BEGIN months -->
    						<option value="{months.VALUE}"{months.SELECTED}>{months.LABEL}</option>
    					  <!-- END months -->
    					</select>
    					<select name="year" id="year">
                <!-- BEGIN years -->
    						<option value="{years.VALUE}"{years.SELECTED}>{years.VALUE}</option>
    					  <!-- END years -->
    					</select>
                                        <input type="text" size="2" name="hour" value="20" id="hour" />h
    					<input type="text" size="2" name="minutes" id="minutes" value="00" />
    					<input type="radio" name="matchspecial" id="statusMatch" value="0" checked="checked" />match
    					<input type="radio" name="matchspecial" id="statusMatchBonus" value="1" />match bonus
    					<input type="radio" name="matchspecial" id="statusMatchLCP" value="2" />match {LCP}
    				</td>
          </tr>
          <tr>
            <td colspan="2">Phase :</td>
          </tr>
          <tr>
            <td>
              <select name="phase" id="phase">
                <!-- BEGIN phases -->
                <option value="{phases.ID}"{phases.SELECTED}>{phases.NAME}</option>
                <!-- END phases -->
              </select>
            </td>
          </tr>
          <tr>
    				<td>Equipe A :</td>
    				<td>Equipe B :</td>
    			</tr>
    			<tr>
    				<td id="teamsDivA">
    					<select name="teamA" id="teamA">
                <!-- BEGIN teams -->
    						<option value="{teams.ID}">{teams.NAME}</option>
                <!-- END teams -->
    					</select>
    				</td>
    				<td id="teamsDivB">
    					<select name="teamB" id="teamB">
                <!-- BEGIN teams -->
    						<option value="{teams.ID}">{teams.NAME}</option>
                <!-- END teams -->
    					</select>
    				</td>
    			</tr>
    			<tr>
    				<td colspan="2" style="text-align:center;">
              <input type="submit" name="add_match" id="add_match" value="Ajouter/Modifier" />
              <input type="submit" name="add_match" id="del_match" value="Supprimer" />
            </td>
    			</tr>
    		</table>
    	</div>
  	</form>

    <div class="tag_cloud">
      <!-- BEGIN phases -->
		  <span style="font-size: 150%">{phases.NAME}</span>
        <!-- BEGIN games -->
        <div id="match_{phases.games.ID}" class="edit_match" onclick="getGame({phases.games.ID})">
            {phases.games.DATE} : {phases.games.TEAM_NAME_A} - {phases.games.TEAM_NAME_B} {phases.games.IMG}
        </div>
        <!-- END games -->
  		<br />
  		<br />
      <!-- END phases -->
  	</div>
    <br />
  </div>
</div>
