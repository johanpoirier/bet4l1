$.extend({
    getUrlVars: function () {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    },
    getUrlVar: function (name) {
        return $.getUrlVars()[name];
    }
});

function checkScore() {
    var scoreInput = $(this);
    if (!/^[0-9]{1,3}$/.test(scoreInput.val())) {
        alert("Veuillez entrer un score valide !");
        scoreInput.val("0");
    }
}

function toggle(id) {
    $("#" + id).toggle(100);
}

function saveTag(userTeamID) {
    if (!userTeamID) userTeamID = -1;
    tag = $("#tag").val();

    $.ajax({
        type: "POST",
        url: "/lib/ajax.php",
        data: "op=saveTag&userTeamID=" + userTeamID + "&tag=" + tag,
        success: function (data) {
            $("#tags").html(data);
            $("#tag").val("");
        }
    });

    return false;
}

function delTag(tagID, userTeamID) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
        if (!userTeamID) userTeamID = -1;
        $.ajax({
            type: "POST",
            url: "/lib/ajax.php",
            data: "op=delTag&tagID=" + tagID + "&userTeamID=" + userTeamID,
            success: function (data) {
                $("#tags").html(data);
            }
        });
    }
}

function selectListValue(id_liste, value) {
    $("#" + id_liste + " option[value='" + value + "']").attr('selected', 'selected');
}

function selectRadioValue(name, value) {
    $("input[name=" + name + "]").each(function () {
        if ($(this).val() == value) {
            $(this).attr("checked", "checked");
        }
    });
}

function getGame(id) {
    $.ajax({
        type: "GET",
        url: "/lib/ajax.php",
        data: "op=getGame&id=" + id,
        success: fillMatch
    });
}

var fillMatch = function (response) {
    var matchDatas = response.split("|");
    selectListValue('day', matchDatas[0]);
    selectListValue('month', matchDatas[1]);
    selectListValue('year', matchDatas[2]);
    $('#hour').val(matchDatas[3]);
    $('#minutes').val(matchDatas[4]);
    selectRadioValue('matchspecial', matchDatas[5]);
    selectListValue('phase', matchDatas[6]);
    selectListValue('teamA', matchDatas[7]);
    selectListValue('teamB', matchDatas[8]);
    $('#idMatch').val(matchDatas[9]);
};

function getUser(idUser) {
    $.ajax({
        type: "GET",
        url: "/lib/ajax.php",
        data: "op=getUser&id=" + idUser,
        success: fillUser
    });
}

var fillUser = function (response) {
    var userDatas = response.split("|");
    $('#name').val(userDatas[0]);
    $('#login').val(userDatas[1]);
    $('#mail').val(userDatas[2]);
    $('#admin').attr('checked', (userDatas[3] == 1));
    selectListValue('sltUserTeam', userDatas[4]);
};

function getInstance(id) {
    $.ajax({
        type: "GET",
        url: "/lib/ajax.php",
        data: "op=getInstance&id=" + id,
        success: fillInstance
    });
}

var fillInstance = function (response) {
    var instanceDatas = response.split("|");
    $('#newId').val(instanceDatas[0]);
    $('#newName').val(instanceDatas[1]);
    $('#active').attr('checked', (instanceDatas[4] == 1));
};

function loadInfos() {
    $.ajax({
        type: "GET",
        url: "/lib/ajax.php",
        data: "op=getInfos",
        success: function (data) {
            $('#infos').html(data);
        }
    });
}

function getTags(userTeamID, startTag) {
    if (!startTag) {
        startTag = 0;
    }
    if (!userTeamID) {
        userTeamID = -1;
    }
    $.ajax({
        type: "POST",
        url: "/lib/ajax.php",
        data: "op=getTags&start=" + startTag + "&userTeamID=" + userTeamID,
        success: function (data) {
            $('#tags').html(data);
        }
    });

    return false;
}

function generateUuid() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

function getUuid() {
    if (window.localStorage) {
        return window.localStorage.getItem('uuid');
    }
    return null;
}

function updateRanking() {
    $('.update-ranking').html('En cours...');
    $.ajax({
        type: 'GET',
        url: '/',
        data: 'op=update_ranking',
        success: function (data) {
            handleUpdateRankingResponse(data);
        }
    });
}

function handleUpdateRankingResponse(results) {
    if (results == 'OKOK') {
        $('.update-ranking').html('Classement à jour.');
    }
    else {
        $('.update-ranking').html('Erreur !');
        console.error('update ranking failed', results);
    }

}

function updateStats() {
    $('.update-stats button').html("Génération en cours...");
    $.ajax({
        type: 'GET',
        url: '/',
        data: 'op=update_stats',
        success: function (data) {
            handleUpdateStatsResponse(data);
        },
        error: function (XMLHttpRequest, textStatus) {
            alert(textStatus);
        }
    });
}

function handleUpdateStatsResponse(results) {
    if (results == 'OKOK') {
        $('.update-stats').html('Stats à jour.');
    }
    else {
        $('.update-stats').html('Erreur !');
        console.error('update stats failed', results);
    }
}
