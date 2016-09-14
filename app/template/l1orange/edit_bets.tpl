<section id="mainarea">
    <div class="maincontent">
        <div class="headline">
            <div class="headline-title"><h1>Pronostics de {USER_LOGIN}</h1></div>
            <div class="headline-menu">
                <div>
                    <a onclick="changePhase({PREVIOUS_PHASE_ID})" style="visibility: {PREVIOUS_PHASE_VISIBILITY};">
                        <img height="16" src="{TPL_WEB_PATH}/images/previous.gif" alt="previous"/>
                    </a>
                    <select name="sltPhase" onchange="changePhase(this.value)">
                        <!-- BEGIN phases -->
                        <option value="{phases.ID}"{phases.SELECTED}>{phases.NAME}</option>
                        <!-- END phases -->
                    </select>
                    <a onclick="changePhase({NEXT_PHASE_ID})" style="visibility: {NEXT_PHASE_VISIBILITY};">
                        <img height="16" src="{TPL_WEB_PATH}/images/next.gif" alt="next"/>
                    </a>
                </div>
            </div>
        </div>

        <div class="tag_cloud uniq">
            <form action="?op=save_pronos" method="post" name="formPronos">
                <input type="hidden" name="userId" value="{USER_ID}"/>
                <table width="100%">
                    <!-- BEGIN bets -->
                    {bets.DATE}
                    <tr style="line-height: 18px;">
                        <td width="5%" align="left" style="white-space:nowrap;font-size:7pt;"
                            rowspan="2">{bets.IMG}</td>
                        <td id="m_{bets.MATCH_ID}_team_A" width="32%" rowspan="2" style="text-align: right;"
                            class="{bets.CLASS_A}">
                            {bets.TEAM_NAME_A}
                            <img src="{bets.TEAM_IMG_A}" alt="{bets.TEAM_NAME_A}"/>
                        </td>
                        <td width="12%" style="text-align:right;">
                            <input type="number" min="0" max="99" size="2" name="iptScoreTeam_A_{bets.MATCH_ID}"
                                   id="iptScoreTeam_A_{bets.MATCH_ID}" value="{bets.SCORE_BET_A}" {bets.DISABLED} />
                        </td>
                        <td width="7%" style="text-align:center; font-weight:300; font-size:9px; color:{bets.COLOR};"
                            rowspan="2">
                            {bets.POINTS}<br/>
                          <span style="color:grey;">
                            {bets.DIFF}
                          </span>
                        </td>
                        <td width="12%" style="text-align:left;">
                            <input type="number" min="0" max="99" size="2" name="iptScoreTeam_B_{bets.MATCH_ID}"
                                   id="iptScoreTeam_B_{bets.MATCH_ID}" value="{bets.SCORE_BET_B}" {bets.DISABLED} />
                        </td>
                        <td id="m_{bets.MATCH_ID}_team_B" width="32%" rowspan="2" style="text-align: left;"
                            class="{bets.CLASS_B}">
                            <img src="{bets.TEAM_IMG_B}" alt="{bets.TEAM_NAME_B}"/>
                            {bets.TEAM_NAME_B}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;color:blue;font-weight:300;font-size:9px;">{bets.SCORE_MATCH_A}</td>
                        <td style="text-align:center;color:blue;font-weight:300;font-size:9px;">{bets.SCORE_MATCH_B}</td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <!-- END bets -->
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="5">{SUBMIT}</td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

    <aside>
        <div class="tag_cloud">
            <div><h3>Classement virtuel</h3></div>
            <div id="pool_{POOL_NAME}_ranking">
                <table class="ranking-pool">
                    <tr>
                        <td width="10%"></td>
                        <td width="70%"><b>Equipe</b></td>
                        <td width="10%"><b>Pts</b></td>
                        <td width="10%"><b>Diff</b></td>
                    </tr>
                    <!-- BEGIN teams -->
                    <tr>
                        <td class="{teams.CLASS}">{teams.RANK}</td>
                        <td id="team_{teams.ID}"><img width="15px" src="{teams.IMG}" alt="{teams.NAME}"/> {teams.NAME}
                        </td>
                        <td>{teams.POINTS}</td>
                        <td>{teams.DIFF}</td>
                    </tr>
                    <!-- END teams -->
                </table>
            </div>
        </div>
    </aside>
</section>

<script type="text/javascript">
    function changePhase(phaseID) {
        url = "?op=edit_pronos&phase=" + phaseID
        user = $.getUrlVar('user');
        if (user) {
            url += "&user=" + user;
        }
        window.location.href = url;
    }

    $("form").find("input[type='number']").change(checkScore);
</script>
