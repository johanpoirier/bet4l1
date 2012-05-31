<div id="mainarea">
    <div class="maincontent">
        <div id="headline">
            <table width="100%">
                <tr>
                    <td width="55%"><h1>Classement {LCP_LABEL}</h1></td>
                    <td align="center" width="15%"><a href="/?op=view_ranking">{GENERAL_CUP_LABEL}</a></td>
                    <td align="center" width="15%"><a href="/?op=view_ranking_perfect">{PERFECT_CUP_LABEL}</a></td>
                    <td align="center" width="15%"><a href="/?op=view_ranking_lcp"><strong>{LCP_LABEL}</strong></a></td>
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
