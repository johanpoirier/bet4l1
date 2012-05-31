<script type="text/javascript">
<!--
  function changePhase(phaseID) {
    window.location.href = "?op=view_results&phase="+phaseID;
  }
//-->
</script>
<div id="mainarea">
  <div class="maincontent">
    <div id="headline">
      <h1 style="float:left;">Resultats et Cotes</h1>
      <select style="float:right;" name="sltPhase" onchange="changePhase(this.value)">
      <!-- BEGIN phases -->
        <option value="{phases.ID}"{phases.SELECTED}>{phases.NAME}</option>
      <!-- END phases -->
      </select>
      &nbsp;<br /><br />
    </div>
    <br />
    <div class="tag_cloud">
      <span style="font-size: 150%">{PHASE_NAME}</span>
      <table width="100%">
      <!-- BEGIN results -->
        {results.DATE}
        <tr>
          <td width="5%" align="left" style="white-space: nowrap; font-size: 7pt;" rowspan="5">{results.IMG}</td>
          <td id="m_{results.MATCH_ID}_team_A" width="32%" rowspan="3" style="text-align: right;background-color: <? echo $result['COLOR_A']; ?>;">{results.TEAM_NAME_A} <img src="{results.TEAM_IMG_A}" alt="{results.TEAM_NAME_A}" /></td>
          <td width="12%" style="text-align:center;font-weight:600;font-size:15px;">{results.SCORE_MATCH_A}</td>
          <td width="7%" style="text-align:center; font-weight:300; font-size:9px;" rowspan="2"></td>
          <td width="12%" style="text-align:center;font-weight:600;font-size:15px;">{results.SCORE_MATCH_B}</td>
          <td id="m_{results.MATCH_ID}_team_B" width="32%" rowspan="3" style="text-align: left; background-color: <? echo $result['COLOR_B']; ?>;"><img src="{results.TEAM_IMG_B}" alt="{results.TEAM_NAME_B}" /> {results.TEAM_NAME_B}</td>
        </tr>
        <tr>
          <td style="text-align:center;color:blue;font-weight:300;font-size:9px;">{results.A_AVG}</td>
          <td style="text-align:center;color:blue;font-weight:300;font-size:9px;">{results.B_AVG}</td>
        </tr>
        <tr>
          <td style="text-align:center;color:green;font-weight:300;font-size:9px;">{results.A_WINS}/1</td>
          <td style="text-align:center;color:green;font-weight:300;font-size:9px;">{results.NUL}/1</td>
          <td style="text-align:center;color:green;font-weight:300;font-size:9px;">{results.B_WINS}/1</td>
        </tr>
		    <tr>
          <td colspan="5" style="text-align:center;color:red;font-weight:300;font-size:9px;">
      			<a href="javascript:toggle('exact_bets_{results.MATCH_ID}');">
      				<span style="color:red;"><strong>{results.NB_EXACT_BETS}</strong></span>
            </a>
            <div id="exact_bets_{results.MATCH_ID}" style="display:none;">
              <strong>{results.EXACT_BETS}</strong>
          	</div>
          </td>
        </tr>
    		<tr>
          <td colspan="5" style="text-align:center;color:red;font-weight:300;font-size:9px;">
            <a href="javascript:toggle('good_bets_{results.MATCH_ID}');">
    				  <span style="color:red;">{results.NB_GOOD_BETS}</span>
    	  		</a>
            <div id="good_bets_{results.MATCH_ID}" style="display:none;">
    				  {results.GOOD_BETS}
            </div>
          </td>
        </tr>
      <!-- END results -->
      </table>
      <br />
      <br />
      <br />
    </div>
  </div>
  <br />
  <br />
  <br />
  <div id="rightcolumn">
    <div class="tag_cloud">
      <div class="rightcolumn_headline"><h1 style="color:black;">Classement {COMPETITION_NAME}</h1></div>
      <div id="pool_{POOL_NAME}_ranking">
        <table style="font-size:9px;">
          <tr>
            <td width="80%"><b>Equipe</b></td><td width="10%"><b>Pts</b></td><td width="10%"><b>Diff</b></td>
          </tr>
          <!-- BEGIN teams -->
          <tr{teams.STYLE}>
            <td id="team_{teams.ID}"><img width="15px" src="{teams.IMG}" alt="{teams.NAME}" /> {teams.NAME}</td>
            <td>{teams.POINTS}</td>
            <td>{teams.DIFF}</td>
          </tr>
          <!-- END teams -->
        </table>
      </div>
    </div>
    <br />
    <br />
  </div>
</div>
