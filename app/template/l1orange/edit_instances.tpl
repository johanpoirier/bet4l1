<div id="mainarea">
    <div class="maincontent">
        <div id="headline"><h1>Instances</h1></div>

        <div class="tag_cloud">
            <form name="add_instance" action="/?op=add_instance" method="post">
                <input type="hidden" name="id" id="id" value=""/>
                <table>
                    <tr>
                        <td width="40%">Nouvelle instance :</td>
                        <td width="40%">Parent :</td>
                    </tr>
                    <tr>
                        <td><input type="text" size="40" name="name" id="name"/></td>
                        <td>
                            <select name="parentId" id="parentId">
                                <option value="0"></option>
                                <!-- BEGIN instances -->
                                <option value="{instances.ID}">{instances.NAME}</option>
                                <!-- END instances -->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="checkbox" name="copyData" value="1" checked="checked" />
                            <label for="copyData">Copier les données de l'instance parente (joueurs, équipes, paramètres)</label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            <input type="submit" value="Ajouter"/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="tag_cloud">
            <form name="edit_instance" action="/?op=edit_instance" method="post">
                <input type="hidden" name="id" id="newId" value=""/>
                <table>
                    <tr>
                        <td width="45%">Nom :</td>
                        <td width="45%">Active :</td>
                    </tr>
                    <tr>
                        <td><input type="text" size="25" name="name" id="newName"/></td>
                        <td><input type="checkbox" name="active" id="active" value="1"/></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            <input type="submit" name="edit_instance" id="edit_instance" value="Modifier"/>
                            <input type="submit" name="edit_instance" id="del_instance" value="Supprimer"/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="tag_cloud">
            <table width="100%">
                <!-- BEGIN instances -->
                <tr onclick="getInstance({instances.ID})">
                    <td {instances.STYLE} id="instance_{instances.ID}">{instances.NAME}</td>
                    <td {instances.STYLE}>{instances.NB_PHASES} journée(s)</td>
                </tr>
                <!-- END instances -->
            </table>
        </div>
    </div>
</div>
