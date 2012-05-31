<div id="mainarea">
    <div class="maincontent">
        <div id="headline">
            <h1>Parieurs</h1>
        </div>
    </div>

    <div class="maincontent">
        <div class="tag_cloud">
            <form name="add_user" action="/?op=add_user" method="post">
                <input type="hidden" id="idUser" value="" />
                <input type="hidden" id="realname" value="" />
                <table>
                    <tr><td width="45%">Login :</td><td width="45%">Pass :</td></tr>
                    <tr>
                        <td><input type="text" size="25" name="login" id="login" /></td>
                        <td><input type="text" size="25" name="pass" id="pass" /></td>
                    </tr>
                    <tr><td>Nom :</td><td>Mail :</td></tr>
                    <tr>
                        <td><input type="text" size="25" name="name" id="name" /></td>
                        <td><input type="text" size="25" name="mail" id="mail" /></td>
                    </tr>
                    <tr><td>Equipe :</td><td>&nbsp;</td></tr>
                    <tr>
                        <td>
                            <select name="sltUserTeam" id="sltUserTeam">
                                <option value=""></option>
                                <!-- BEGIN teams -->
                                <option value="{teams.ID}">{teams.NAME}</option>
                                <!-- END teams -->
                            </select>
                        </td>
                        <td>Admin <input type="checkbox" name="admin" id="admin" value="1"/></td>
                    </tr>
                    <tr><td colspan=2 style="text-align:center;">
                            <input type="submit" name="add_user" id="add_user" value="Ajouter / Modifier" />
                            <input type="submit" name="add_user" id="del_user" value="Supprimer" />
                        </td></tr>
                </table>
            </form>
        </div>

        <!-- BEGIN teams -->
        <div id="{teams.ID}">
            <div class="tag_cloud" id="list_users">
                <h3>{teams.NAME}</h3>
                <!-- BEGIN users -->
                <div id="user_{teams.users.ID}" onclick="getUser({teams.users.ID})">
                    {teams.users.LOGIN} ({teams.users.NAME})
                </div>
                <!-- END users -->
            </div>
        </div>
        <!-- END teams -->

        <div class="tag_cloud">
            <form name="add_csv_users" action="/?op=import_csv_file" method="post" enctype="multipart/form-data">
                Importer un fichier csv ('login;pass;nom;teamname;admin') : <br /><br />
                <input type="file" name="csv_file" size="40" />&nbsp;<input type="submit" name="submit" value="Ok" />
            </form>
        </div>
        <br />
        <br />
    </div>

    <div class="maincontent">
        <div id="headline"><h1>Equipes</h1></div>
    </div>

    <div class="maincontent">
        <div class="tag_cloud" id="list_user_teams">
            <!-- BEGIN teams -->
            <div id="user_team{teams.ID}">
                {teams.NAME}
            </div>
            <!-- END teams -->
        </div>

        <div class="tag_cloud">
            <form name="add_user_team" action="/?op=add_user_team" method="post">
                <table>
                    <tr><td width="40%">Nom :</td></tr>
                    <tr><td><input type="text" size="25" name="user_team_name" id="user_team_name" /></td></tr>
                    <tr>
                        <td style="text-align:center;">
                            <input type="submit" name="add_user_team" id="add_user_team" value="Ajouter" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
