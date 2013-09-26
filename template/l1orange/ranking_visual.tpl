<div id="mainarea">
    <div class="maincontent">
        <div id="headline">
            <table width="100%">
                <tr>
                    <td width="55%"><h1>Classement en relief {INSTANCE_NAME}</h1></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="maincontent">
        <table>
            <tr>
                <td width="80" style="font-size:80%;text-align:center;"><i>Rang</i></td>
                <td width="420" style="font-size:80%"><i>Parieur(s)</i></td>
                <td width="120" style="font-size:80%;text-align:center;"><i>Nombre de points</i></td>
            </tr>
        </table>

        <!-- BEGIN users -->
        <div class="list_element">
            <table class="{users.CLASS}">
                <tr>
                    <td width="80" style="font-size:80%;text-align:center;">{users.RANK}</td>
                    <td width="420" style="font-size:80%"><strong>{users.LOGIN}</strong></td>
                    <td width="120" style="font-size:70%;text-align:center;"><strong>{users.POINTS}</strong></td>
                </tr>
            </table>
        </div>
        <!-- END users -->
    </div>
</div>