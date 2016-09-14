<section id="mainarea">
    <div class="maincontent">

        <div class="headline">
            <div class="headline-title">
                <h1>Classement général</h1> après {NB_MATCHES}
            </div>
            <div class="headline-menu">
                <a href="/?op=view_ranking"><strong>{GENERAL_CUP_LABEL}</strong></a>
                <a href="/?op=view_ranking_visual">Relief</a>
                <a href="/?op=view_ranking_perfect">{PERFECT_CUP_LABEL}</a>
                <a href="/?op=view_ranking_lcp">{LCP_LABEL}</a>
            </div>
        </div>

        <table class="ranking">
            <tr>
                <th width="10%"><i>Rang</i></th>
                <th width="25%" class="aligned">Parieur</th>
                <th width="15%"><i>Equipe</i></th>
                <th width="10%"><i>Points</i></th>
                <th width="10%"><i>Résultats</i></th>
                <th width="10%"><i>Perfects</i></th>
                <th width="10%"><i>Bonus</i></th>
                <th width="10%"><i>{LCP_SHORT_LABEL}</i></th>
            </tr>

            <!-- BEGIN users -->
            <tr class="{users.CLASS} list_element" id="user{users.ID}">
                <td><strong>{users.RANK}</strong> {users.LAST_RANK}</td>
                <td class="aligned">
                    <strong>{users.VIEW_BETS}{users.LOGIN}</a></strong> {users.NB_BETS}
                </td>
                <td>{users.TEAM}</td>
                <td><strong>{users.POINTS}</strong></td>
                <td>{users.NBRESULTS}</td>
                <td>{users.NBSCORES}</td>
                <td>{users.BONUS}</td>
                <td>{users.LCP}</td>
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
