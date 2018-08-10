<section id="mainarea">
    <div class="maincontent">

        <div class="headline">
            <div class="headline-title"><h1>Résultats</h1></div>
            <div class="headline-menu">
                <span class="update-ranking"><button onclick="updateRanking()">Mettre à jour le classement</button></span>
                <span class="update-stats"><button onclick="updateStats()">Générer les stats</button></span>
            </div>
        </div>



        <form action="/?op=save_results" method="post" name="formResults">
            <div class="tag_cloud">
                <table width="100%">
                    <!-- BEGIN games -->
                    <!-- BEGIN date -->
                    <tr>
                        <td colspan="5" style="text-align: center;"><br/><i>{games.date.LABEL}</i></td>
                    </tr>
                    <!-- END date -->

                    <tr>
                        <td align="left" style="white-space: nowrap; font-size: 7pt;"></td>
                        <td id="m_{games.ID}_team_A" width="40%"
                            style="text-align: right; background-color: {games.COLOR_A};">{games.TEAM_NAME_A} <img
                                    src="{TPL_WEB_PATH}/images/fanions/{games.TEAM_IMG_A}.png"
                                    alt="{games.TEAM_NAME_A}"/></td>
                        <td width="10%" style="text-align:right;"><input type="number" min="0" max="99" size="2"
                                                                         name="iptScoreTeam_A_{games.ID}"
                                                                         id="scoreTeam_A_{games.ID}"
                                                                         value="{games.SCORE_A}"/></td>
                        <td width="10%" style="text-align: left;"><input type="number" min="0" max="99" size="2"
                                                                         name="iptScoreTeam_B_{games.ID}"
                                                                         id="scoreTeam_B_{games.ID}"
                                                                         value="{games.SCORE_B}"/></td>
                        <td id="m_{games.ID}_team_B" width="40%"
                            style="text-align: left; background-color: {games.COLOR_B};"><img
                                    src="{TPL_WEB_PATH}/images/fanions/{games.TEAM_IMG_B}.png"
                                    alt="{games.TEAM_NAME_B}"/> {games.TEAM_NAME_B}</td>
                    </tr>
                    <!-- END games -->
                </table>
                <br/><br/>
                <br/>
                <center>
                    <input type="image" src="{TPL_WEB_PATH}/images/submit.gif" name="iptSubmit"/>
                </center>
            </div>
        </form>
    </div>
</section>
