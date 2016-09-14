<section id="mainarea">
    <div class="maincontent">
        <div class="headline">
            <div class="headline-title">
                <h1>Classement {PERFECT_CUP_LABEL}</h1>
            </div>
            <div class="headline-menu">
                <a href="/?op=view_ranking">{GENERAL_CUP_LABEL}</a>
                <a href="/?op=view_ranking_visual">Relief</a>
                <a href="/?op=view_ranking_perfect"><strong>{PERFECT_CUP_LABEL}</strong></a>
                <a href="/?op=view_ranking_lcp">{LCP_LABEL}</strong></a>
            </div>
        </div>

        <table class="ranking">
            <tr>
                <th width="10%"><i>Rang</i></th>
                <th width="25%" class="aligned">Parieur</th>
                <th width="17%"><i>Equipe</i></th>
                <th width="16%"><i>Perfects</i></th>
                <th width="16%"><i>Nb de pronos</i></th>
                <th width="16%"><i>% de score exacts</i></th>
            </tr>

            <!-- BEGIN users -->
            <tr class="{users.CLASS} list_element" id="user{users.ID}">
                <td><strong>{users.RANK}</strong></td>
                <td class="aligned"><strong>{users.VIEW_BETS}{users.LOGIN}</a></strong> {users.NB_MISS_BETS}</td>
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
        getTags();
    </script>
</section>
