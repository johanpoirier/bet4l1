<script type="text/javascript">
<!--
  function changePhase(phaseID) {
    window.location.href = "?op=view_ranking_phase_lcp&phase="+phaseID;
  }
//-->
</script>
<section id="mainarea">
    <div class="maincontent">
        <div class="headline">
            <table width="100%">
                <tr>
                    <td width="55%"><h1>{LCP_LABEL} : {PHASE_NAME}</h1></td>
                    <td align="center" width="15%"><a href="/?op=view_ranking_phase">{GENERAL_CUP_LABEL}</a></td>
                    <td align="center" width="15%"><a href="/?op=view_ranking_phase_perfect">{PERFECT_CUP_LABEL}</a></td>
                    <td align="center" width="15%"><a href="/?op=view_ranking_phase_lcp"><strong>{LCP_LABEL}</strong></a></td>
                </tr>
                <tr>
                    <td colspan="4" align="left">
                        <a onclick="changePhase({PREVIOUS_PHASE_ID})" style="float:left; cursor: pointer; visibility: {PREVIOUS_PHASE_VISIBILITY};"><img height="16" src="{TPL_WEB_PATH}/images/previous.gif" alt="previous" /></a>
                        <select style="float:left;" name="sltPhase" onchange="changePhase(this.value)">
                            <!-- BEGIN phases -->
                            <option value="{phases.PHASE_ID}" {phases.SELECTED}>{phases.NAME}</option>
                            <!-- END phases -->
                        </select>
                        <a onclick="changePhase({NEXT_PHASE_ID})" style="float:left; cursor: pointer; visibility: {NEXT_PHASE_VISIBILITY};"><img height="16" src="{TPL_WEB_PATH}/images/next.gif" alt="next" /></a>
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
                <td width="90" style="font-size:80%;text-align:center;"><i>Equipe</i></td>
                <td width="60" style="font-size:80%;text-align:center;"><i>Total</i></td>
                <td width="60" style="font-size:80%;text-align:center;"><i>Points</i></td>
                <td width="60" style="font-size:80%;text-align:center;"><i>Malus</i></td>
                <td width="60" style="font-size:80%;text-align:center;"><i>Match</i></td>
            </tr>
        </table>

        <!-- BEGIN users -->
        <div class="list_element">
            <table{users.BG_COLOR}>
                <tr>
                    <td width="45" style="font-size:80%;text-align:center;"><strong>{users.RANK}</strong> {users.LAST_RANK}</td>
                    <td width="215" style="font-size:70%">
                        <strong>{users.VIEW_BETS}{users.LOGIN}</a></strong></td>
                    <td width="90" style="font-size:70%;text-align:center;">{users.TEAM}</td>
                    <td width="60" style="font-size:70%;text-align:center;"><strong>{users.LCP_TOTAL}</strong></td>
                    <td width="60" style="font-size:70%;text-align:center;">{users.LCP_POINTS}</td>
                    <td width="60" style="font-size:70%;text-align:center;">{users.LCP_BONUS}</td>
                    <td width="60" style="font-size:70%;text-align:center;">{users.LCP_MATCH}</td>
                </tr>
            </table>
        </div>
        <!-- END users -->
    </div>

    <aside>
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
</section>
