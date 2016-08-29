<script type="text/javascript">
<!--
  function changePhase(phaseID) {
    window.location.href = "?op=view_ranking_phase&phase="+phaseID;
  }
//-->
</script>
<div id="mainarea">
    <div class="maincontent">
        <div id="headline">
            <table width="100%">
                <tr>
                    <td width="55%"><h1>Classement : {PHASE_NAME}</h1></td>
                    <td align="center" width="15%"><a href="/?op=view_ranking_phase"><strong>{GENERAL_CUP_LABEL}</strong></a></td>
                    <td align="center" width="15%"><a href="/?op=view_ranking_phase_perfect">{PERFECT_CUP_LABEL}</a></td>
                    <td align="center" width="15%"><a href="/?op=view_ranking_phase_lcp">{LCP_LABEL}</a></td>
                </tr>
                <tr>
                    <td colspan="4" align="left">
                        <span>Après {NB_GAMES_PLAYED} matchs joués sur {NB_GAMES_TOTAL}</span>
                        <a onclick="changePhase({NEXT_PHASE_ID})" style="float:right; cursor: pointer; visibility: {NEXT_PHASE_VISIBILITY};"><img height="16" src="{TPL_WEB_PATH}/images/next.gif" alt="next" /></a>
                        <select style="float:right;" name="sltPhase" onchange="changePhase(this.value)">
                            <!-- BEGIN phases -->
                            <option value="{phases.PHASE_ID}" {phases.SELECTED}>{phases.NAME}</option>
                            <!-- END phases -->
                        </select>
                        <a onclick="changePhase({PREVIOUS_PHASE_ID})" style="float:right; cursor: pointer; visibility: {PREVIOUS_PHASE_VISIBILITY};"><img height="16" src="{TPL_WEB_PATH}/images/previous.gif" alt="previous" /></a>                        
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="maincontent">
        <table>
            <tr>
                <td width="45" style="font-size:80%;text-align:center;"><i>Rang</i></td>
                <td width="215" style="font-size:80%"><i>Parieur</i></td>
                <td width="70" style="font-size:80%;text-align:center;"><i>Equipe</i></td>
                <td width="65" style="font-size:80%;text-align:center;"><i>Points</i></td>
                <td width="65" style="font-size:80%;text-align:center;"><i>R&eacute;sultats Exacts</i></td>
                <td width="65" style="font-size:80%;text-align:center;"><i>Perfects</i></td>
                <td width="65" style="font-size:80%;text-align:center;"><i>Bonus</i></td>
            </tr>
        </table>

        <!-- BEGIN users -->
        <div class="list_element">
            <table style="background-color:{users.COLOR};">
                <tr>
                    <td width="45" style="font-size:80%;text-align:center;"><strong>{users.RANK}</strong></td>
                    <td width="215" style="font-size:70%">
                        <strong>{users.VIEW_BETS}{users.LOGIN}</a></strong> {users.NB_MISS_BETS}</td>
                    <td width="70" style="font-size:70%;text-align:center;">{users.TEAM}</td>
                    <td width="65" style="font-size:70%;text-align:center;"><strong>{users.POINTS}</strong></td>
                    <td width="65" style="font-size:70%;text-align:center;">{users.NBRESULTS}</td>
                    <td width="65" style="font-size:70%;text-align:center;">{users.NBSCORES}</td>
                    <td width="65" style="font-size:70%;text-align:center;">{users.BONUS}</td>
                </tr>
            </table>
        </div>
        <!-- END users -->
    </div>

    <div id="rightcolumn">
        <div class="tag_cloud">
            <div class="rightcolumn_headline"><h1 style="color:black;">ChatBoard</h1></div>
            <div id="tag_0" styAle="text-align:center;"><br />
                <form onsubmit="return saveTag(-1);">
                    <input type="text" id="tag" value="" size="20" /><br />
                    <span style="font-size:8px;">(Entrée pour envoyer)</span><br /><br />
                </form>
            </div>
            <div id="tags">
                &nbsp;
            </div>
        </div>
    </div>
    <script type="text/javascript">
    <!--
            getTags();
    //-->
    </script>
</div>