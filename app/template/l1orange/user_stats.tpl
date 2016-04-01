<div id="mainarea">
    <div class="maincontent">
        <div id="headline">
            <h1>Statistiques de {USER_LOGIN}</h1>
        </div>
    </div>
    <div class="maincontent">
        <!-- BEGIN stats -->
        <div class="tag_cloud">
            <span style="font-size: 150%">{stats.TYPE}</span>
            <br/><br/>
            <div id="stats_{stats.ID}" style="height: 300px;"></div>
            <script type="text/javascript">
                var data = {stats.DATA};
                $.plot("#stats_{stats.ID}", data,
                {
                    xaxis: {
                        ticks: {stats.XSERIE}
                    },
                    yaxis: {
                        min: {stats.YMIN},
                        max: {stats.YMAX},
                        {stats.INVERSE}
                        tickSize: {stats.YTICKS}
                    },
                    grid: {
                        backgroundColor: "#ffffff",
                        hoverable: true
                    }
                });

                function showTooltip(x, y, contents) {
                    $("<div id='tooltip'>" + contents + "</div>").css({
                        position: "absolute",
                        display: "none",
                        top: y - 30,
                        left: x - 10,
                        border: "1px solid #fdd",
                        padding: "2px",
                        "background-color": "#fee",
                        opacity: 0.80
                    }).appendTo("body").fadeIn(200);
                }

                var previousPoint = null;
                $("#stats_{stats.ID}").bind("plothover", function (event, pos, item) {
                    if (item) {
                        if (previousPoint != item.dataIndex) {

                            previousPoint = item.dataIndex;

                            $("#tooltip").remove();
                            var x = item.datapoint[0].toFixed(2),
                                    y = item.datapoint[1].toFixed(2);

                            showTooltip(item.pageX, item.pageY, parseInt(y));
                        }
                    } else {
                        $("#tooltip").remove();
                        previousPoint = null;
                    }
                });
            </script>
        </div>
        <!-- END stats -->
    </div>
    <br/>
    <br/>
    <br/>

    <div id="rightcolumn"></div>
</div>
