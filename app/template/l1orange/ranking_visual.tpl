<section id="mainarea">
    <div class="maincontent">
        <div class="headline">
            <div class="headline-title">
                <h1>Classement en relief</h1>
            </div>
            <div class="headline-menu">
                <a href="/?op=view_ranking">{GENERAL_CUP_LABEL}</a>
                <a href="/?op=view_ranking_visual"><strong>Relief</strong></a>
                <a href="/?op=view_ranking_perfect">{PERFECT_CUP_LABEL}</a>
                <a href="/?op=view_ranking_lcp">{LCP_LABEL}</a>
            </div>
        </div>

        <table class="ranking">
            <tr>
                <th width="10%">Rang</th>
                <th width="70%" class="aligned">Parieur(s)</th>
                <th width="20%">Nombre de points</th>
            </tr>

            <!-- BEGIN users -->
            <tr class="{users.CLASS} list_element">
                <td>{users.RANK}</td>
                <td class="user_visual aligned">{users.LOGIN}</td>
                <td>{users.POINTS}</td>
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