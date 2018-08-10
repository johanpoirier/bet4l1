<section id="mainarea">
    <div class="maincontent">
        <div class="headline">
            <table width="100%">
                <tr>
                    <td width="55%"><h1>{INSTANCE_NAME}</h1></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="maincontent">
        <table>
            <tr>
                <td width="45" style="font-size:80%;text-align:center;"><i>Rang</i></td>
                <td width="200" style="font-size:80%"><i>Parieur</i></td>
                <td width="70" style="font-size:80%;text-align:center;"><i>Equipe</i></td>
                <td width="55" style="font-size:80%;text-align:center;"><i>Points</i></td>
                <td width="55" style="font-size:80%;text-align:center;"><i>R&eacute;sultats</i></td>
                <td width="55" style="font-size:80%;text-align:center;"><i>Perfects</i></td>
                <td width="55" style="font-size:80%;text-align:center;"><i>Bonus</i></td>
                <td width="55" style="font-size:80%;text-align:center;"><i>{LCP_SHORT_LABEL}</i></td>
            </tr>
        </table>

        <!-- BEGIN users -->
        <div class="list_element">
            <table{users.BG_COLOR}>
                <tr>
                    <td width="45" style="font-size:80%;text-align:center;"><strong>{users.RANK}</strong> {users.LAST_RANK}</td>
                    <td width="200" style="font-size:70%">
                        <strong>{users.LOGIN}</strong> {users.NB_BETS}
                    </td>
                    <td width="70" style="font-size:70%;text-align:center;">{users.TEAM}</td>
                    <td width="55" style="font-size:70%;text-align:center;"><strong>{users.POINTS}</strong></td>
                    <td width="55" style="font-size:70%;text-align:center;">{users.NBRESULTS}</td>
                    <td width="55" style="font-size:70%;text-align:center;">{users.NBSCORES}</td>
                    <td width="55" style="font-size:70%;text-align:center;">{users.BONUS}</td>
                    <td width="55" style="font-size:70%;text-align:center;"><i>{users.LCP}</i></td>
                </tr>
            </table>
        </div>
        <!-- END users -->
    </div>
</section>