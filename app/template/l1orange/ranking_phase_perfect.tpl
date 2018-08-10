<section id="mainarea">
    <div class="maincontent">

        <div class="headline">
            <div class="headline-title">
                <h1>{PERFECT_CUP_LABEL} : {PHASE_NAME}</h1>
            </div>
            <div class="headline-menu">
                <a href="/?op=view_ranking_phase">{GENERAL_CUP_LABEL}</a>
                <a href="/?op=view_ranking_phase_perfect"><strong>{PERFECT_CUP_LABEL}</strong></a>
                <a href="/?op=view_ranking_phase_lcp">{LCP_LABEL}</a>
                <div>
                    <a onclick="changePhase({NEXT_PHASE_ID})" style="float:right; cursor: pointer; visibility: {NEXT_PHASE_VISIBILITY};"><img height="16" src="{TPL_WEB_PATH}/images/next.gif" alt="next" /></a>
                    <select style="float:right;" name="sltPhase" onchange="changePhase(this.value)">
                        <!-- BEGIN phases -->
                        <option value="{phases.PHASE_ID}" {phases.SELECTED}>{phases.NAME}</option>
                        <!-- END phases -->
                    </select>
                    <a onclick="changePhase({PREVIOUS_PHASE_ID})" style="float:right; cursor: pointer; visibility: {PREVIOUS_PHASE_VISIBILITY};"><img height="16" src="{TPL_WEB_PATH}/images/previous.gif" alt="previous" /></a>
                </div>
            </div>
        </div>

        <table class="ranking">
            <tr>
                <th width="10%">Rang</th>
                <th width="25%" class="aligned">Parieur</th>
                <th width="15%">Equipe</th>
                <th width="15%">Perfects</th>
                <th width="15%">Nb de pronos</th>
                <th width="20%">% de score exacts</th>
            </tr>

            <!-- BEGIN users -->
            <tr style="background-color:{users.COLOR};" class="list_element" id="user{users.ID}">
                <td><strong>{users.RANK}</strong></td>
                <td class="aligned">
                    <strong>{users.VIEW_BETS}{users.LOGIN}</a></strong> {users.NB_MISS_BETS}</td>
                <td>{users.TEAM}</td>
                <td><strong>{users.NBSCORES}</strong></td>
                <td>{users.NBPRONOS}</td>
                <td>{users.SCORERATE}</td>
            </tr>
            <!-- END users -->
        </table>
    </div>

    <aside>
        <div class="headline">
            <div class="headline-title">
                <h2>TagBoard</h2>
            </div>
        </div>
        <div class="tag_cloud tagboard">
            <div id="tag_0" class="tagboard-form">
                <form onsubmit="return saveTag('');">
                    <input type="text" id="tag" value="" size="18" />
                    <span>(Entrée pour envoyer)</span>
                </form>
            </div>
            <div id="tags"></div>
        </div>
    </aside>

    <script type="text/javascript">
        function changePhase(phaseID) {
            window.location.assign('?op=view_ranking_phase_perfect&phase=' + phaseID);
        }
        getTags();
    </script>
</section>
