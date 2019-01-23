var lastEvent;
$(function() {
    toogle("signalingBacktracking");
    initDataBase();
    initEventName();
});

function initDataBase() {
    var url = "signalingBacktracking/getDataBase";
    var data = { "type": "ctrsystem" };
    $.get(url, data, function(data) {
        if (data == "login") {
            // alert("尚未登录");
            //  	window.location.href = "login";
            layer.open({
                title: "提示",
                content: "尚未登录，不能进行信令流程查询！",
                yes: function(index, layero) {
                    window.location.href = "login";
                    layer.close(index);
                },
                cancel: function(index, layero) {
                    window.location.href = "login";
                    layer.close(index);
                }
            });
            return;
        }
        data = eval(data);
        var database = $("#database").select2({
            placeholder: "请选择日期",
            data: data
        });
        //从失败原因分析跳转过来的参数
        if (window.location.search) {
            var task = window.location.search.split("&")[0].split("=")[1];
            var ueRef = window.location.search.split("&")[1].split("=")[1];
            $("#database").val(task);
            database.val(task).trigger("change");
            $("#ueref").val(ueRef);
            $("#ueRefChoosed").val(ueRef);
            getEventNameandEcgi(task);
            //queryProcess();
            //filterProcess();
            $("#eventChoosedChange").val("true");
            $("#sectionchoose").val("true");
            doSearchEvent();
        } else {
            getEventNameandEcgi(data[0].id);
        }
        $("#database").on("change", function(e) {
            $("#eventName").multiselect("dataprovider", null);
            $("#ecgi").multiselect("dataprovider", null);
            var db = $("#database").val();
            getEventNameandEcgi(db);
        });

    });
}

function initEventName() {
    $("#eventName").multiselect({
        //dropRight: true,
        buttonWidth: "100%",
        //enableFiltering: true,
        nonSelectedText: "事件名称",
        //filterPlaceholder:"搜索",
        nSelectedText: "项被选中",
        includeSelectAllOption: true,
        selectAllText: "全选/取消全选",
        allSelectedText: "已选中所有平台类型",
        maxHeight: 200,
        maxWidth: "100%"
    });
}

function getEventNameandEcgi(db) {
    var url = "signalingBacktracking/getEventNameandEcgi";
    var data = { "database": db };
    $.get(url, data, function(data) {
        if (data == "no database") {
            // alert("");
            layer.open({
                title: "提示",
                content: "没有此数据库！"
            });
            return;
        }
        data = eval("(" + data + ")");
        $("#eventName").multiselect("dataprovider", data.eventName);
        //$("#ecgi").multiselect("dataprovider", data.ecgi);
    });
}

function queryProcess() {
    $("#sectionchoose").val("false");
    $("#exportBtn").addClass("disabled");
    $("#eventChoosedChange").val("true");
    doSearchEvent();
}
$.extend($.fn.datagrid.methods, {
    fixRownumber: function(jq) {
        return jq.each(function() {
            var panel = $(this).datagrid("getPanel");
            var clone = $(".datagrid-cell-rownumber", panel).last().clone();
            clone.css({ "position": "absolute", left: -1000 }).appendTo("body");
            var width = clone.width("auto").width();
            if (width > 25) {
                $(".datagrid-header-rownumber,.datagrid-cell-rownumber", panel).width(width + 5);
                $(this).datagrid("resize");
                clone.remove();
                clone = null;
            } else {
                $(".datagrid-header-rownumber,.datagrid-cell-rownumber", panel).removeAttr("style");
                $(this).datagrid("resize");
            }
        });
    }
});

function doSearchEvent() {
    switchTab(table_tab_0, table_tab_1, "table");
    var task = $("#database").val();
    var params = {
        eventName: $("#eventName").val(),

        imsi: $("#imsi").val(),
        ueRef: $("#ueref").val(),
        enbS1apId: $("#enbs1apid").val(),
        mmeS1apId: $("#mmes1apid").val(),

        filterSection: $("#sectionchoose").val(),
        ueRefChoosed: $("#ueRefChoosed").val(),
        db: task,
        type: "event"
    };

    params.viewType = "table";

    $("#signalingTable").datagrid({
        url: "signalingBacktracking/getEventData",
        view: scrollview,
        rownumbers: true,
        singleSelect: true,
        autoRowHeight: false,
        pageSize: 50,
        loadMsg: "",
        onClickRow: function(rowIndex, rowData) {
            uechoosed(rowData.ueRef);
        },
        onDblClickRow: function(rowIndex, rowData) {
            eventMessageDetail(rowData.id);
        },
        columns: [
            [
                { field: "eventName", title: "Event Name", width: 250 },
                { field: "eventTime", title: "Event Time", width: 180 },
                { field: "imsi", title: "Imsi", width: 100 },
                { field: "mTmsi", title: "MTmsi", width: 100 },
                { field: "ueRef", title: "UE Ref", width: 100 },
                { field: "enbS1apId", title: "ENBS1APId", width: 100 },
                { field: "mmeS1apId", title: "MMES1APId", width: 100 },
                { field: "ecgi", title: "ECGI", width: 120 },
                { field: "gummei", title: "GUMMEI", width: 120 }
            ]
        ],
        queryParams: params,
        onLoadSuccess: function() {
            $(this).datagrid("fixRownumber");
            /*if($("#sectionchoose").val()=="true"){
             $("#exportBtn").removeClass("disabled");
             }*/
            $("#exportBtn").removeClass("disabled");
        }
    });

}


function doSearchEvent_chart() {
    //$("#eventContent").html("");
    var task = $("#database").val();
    var params = {
        eventName: $("#eventName").val(),

        imsi: $("#imsi").val(),
        ueRef: $("#ueref").val(),
        enbS1apId: $("#enbs1apid").val(),
        mmeS1apId: $("#mmes1apid").val(),

        //ecgi:$("#ecgi").val(),

        filterSection: "true",
        ueRefChoosed: $("#ueRefChoosed").val(),
        db: task,
        type: "event"
    };

    //var url="eventData.php";
    params.viewType = "flow";
    $.ajax({
        type: "post",
        url: "signalingBacktracking/getEventData",
        data: params,
        async: false,
        success: function(returnData) {
            //$("#signalingTable").addClass("hidden");
            //$("#signalingChart").removeClass("hidden");

            if (returnData == "false") {
                $("#signalingChart").html("数据库中无相应记录!");
            } else {
                $("#signalingChart").html("");
                drawHighchart(returnData);
                /*if($("#sectionchoose").val()=="true"){
                 $("#exportBtn").removeClass("disabled");
                 $("#eventChoosedChange").val("false");
                 }*/
                $("#exportBtn").removeClass("disabled");
                $("#eventChoosedChange").val("false");
            }
        }
    });

}

function uechoosed(ueRef) {
    $("#ueRefChoosed").val(ueRef);
    //$("#filterBtn").removeClass("disabled");
}

function eventMessageDetail(id) {
    var task = $("#database").val();
    var data = {
        "id": encodeURI(id),
        "db": encodeURI(task)
    };
    $.ajax({
        type: "get",
        url: "signalingBacktracking/showMessage",
        data: data,
        async: false,
        success: function(returnData) {
            //$("#message_modal").modal();
            $("#message").attr("src", returnData);
        }
    });
}

function filterProcess() {
    $("#sectionchoose").val("true");
    doSearchEvent();
}

function exportProcess() {
    var task = $("#database").val();
    var params = {
        eventName: $("#eventName").val(),

        imsi: $("#imsi").val(),
        ueRef: $("#ueref").val(),
        enbS1apId: $("#enbs1apid").val(),
        mmeS1apId: $("#mmes1apid").val(),

        //ecgi:$("#ecgi").val(),

        filterSection: $("#sectionchoose").val(),
        ueRefChoosed: $("#ueRefChoosed").val(),
        db: task,
        type: "event"
    };
    var url = "signalingBacktracking/getAllEventData";
    $.post(url, params, function(data) {
        data = JSON.parse(data);
        if (data.fileName) {
            download(data.fileName, "", "data:text/csv;charset=utf-8");
        } else {
            // alert("");
            layer.open({
                title: "提示",
                content: "出现异常，请重试"
            });
            return;
        }

    });


    // $.post(url, params, function (data) {
    // 	var filterData = eval("(" + data + ")").rows;

    // 	var Data = [];
    // 	var text = [];
    // 	var j = 0;
    // 	for (var field in filterData[0]) {
    // 		text[j++] = field;
    // 	}
    // 	for (var i in filterData) {
    // 		var row = [];
    // 		for (var j in text) {
    // 			if(isNaN(filterData[i][text[j]])){
    // 				row[j] = filterData[i][text[j]].replace(new RegExp(",", "g"), " ").replace(new RegExp("\n", "g"), " ");
    // 			}else{
    // 				row[j] = filterData[i][text[j]];
    // 			}
    // 		}
    // 		Data[i] = row.join(",");
    // 	}

    // 	var fileContent = text + "\n" + Data.join("\n");
    // 	var ueRef = $("#ueRefChoosed").val();
    // 	var url = "signalingBacktracking/exportCSV";
    // 	var data = {
    // 		"fileContent": fileContent,
    // 		"ueRef": ueRef
    // 	};
    // 	$.post(url, data, function (data) {
    // 		if (data) {
    // 			download(data, "", "data:text/csv;charset=utf-8");
    // 		} else {
    // 			// alert("");
    // 			layer.open({
    // 				title: "提示",
    // 				content: "出现异常，请重试"
    // 			});
    // 			return;
    // 		}

    // 	});
    // });
}

function download(url) {
    var browerInfo = getBrowerInfo();
    if (browerInfo == "chrome") {
        download_chrome(url);
    } else if (browerInfo == "firefox") {
        download_firefox(url);
    }
}

function download_chrome(url) {
    var aLink = document.createElement("a");
    aLink.href = url;
    aLink.download = url;
    /*var evt = document.createEvent("HTMLEvents");
     evt.initEvent("click", false, false);
     aLink.dispatchEvent(evt);*/
    document.body.appendChild(aLink);
    aLink.click();
}

function download_firefox(url) {
    window.open(url);
}

function getBrowerInfo() {
    var uerAgent = navigator.userAgent.toLowerCase();
    var format = /(msie|firefox|chrome|opera|version).*?([\d.]+)/;
    var matches = uerAgent.match(format);
    return matches[1].replace(/version/, "'safari");
}

function getMessageMap() {
    return {
        "RRC": { "source": "eNB", "target": "UE", "color": "green" },
        "S1": { "source": "eNB", "target": "MME", "color": "#15A9C3" },
        "X2": { "source": "eNB", "target": "targeteNB", "color": "blue" },
        "INTERNAL": { "source": "eNB", "target": "eNB", "color": "gray" },
        "UE": { "source": "eNB", "target": "eNB", "color": "gray" }
    };
}

function drawHighchart(returnData) {
    var location = { "UE": "180", "eNB": "400", "MME": "620", "targeteNB": "840" };
    var map = getMessageMap();
    var yinterval = 60;
    var yelement = 50;
    //var returnData = window.localStorage.getItem("drawData");
    var data = JSON.parse(returnData);
    data.records = data.rows;
    var rowlen = data.records.length;
    var height = 100 + yinterval * rowlen;

    var message = [];
    var box = [];
    $("#signalingChart").highcharts({
        title: {
            text: null
        },
        exporting: {
            enabled: false
        }, //隐藏导出图片  
        credits: {
            enabled: false
        }, //隐藏highcharts的站点标志  
        chart: {
            width: 900,
            height: height,
            backgroundColor: "white",
            events: {
                load: function() {
                    // Draw the flow chart
                    var ren = this.renderer,
                        colors = Highcharts.getOptions().colors;

                    ren.path(["M", Number.parseInt(location.UE), 70, "V", height - 10])
                        .attr({
                            "stroke-width": 2,
                            stroke: colors[0]
                        }).add();
                    ren.path(["M", Number.parseInt(location.eNB), 70, "V", height - 10])
                        .attr({
                            "stroke-width": 2,
                            stroke: colors[0]
                        }).add();
                    ren.path(["M", Number.parseInt(location.MME), 70, "V", height - 10])
                        .attr({
                            "stroke-width": 2,
                            stroke: colors[0]
                        }).add();
                    ren.path(["M", Number.parseInt(location.targeteNB), 70, "V", height - 10])
                        .attr({
                            "stroke-width": 2,
                            stroke: colors[0]
                        }).add();
                    ren.label("UE", location.UE - 20, 30)
                        .attr({
                            fill: colors[0],
                            stroke: "white",
                            "stroke-width": 2,
                            padding: 10,
                            r: 5
                        })
                        .css({
                            color: "white",
                            fontSize: "16px"
                        })
                        .add()
                        .shadow(true);
                    ren.label("eNB", location.eNB - 25, 30)
                        .attr({
                            fill: colors[0],
                            stroke: "white",
                            "stroke-width": 2,
                            padding: 10,
                            r: 5
                        })
                        .css({
                            color: "white",
                            fontSize: "16px"
                        })
                        .add()
                        .shadow(true);
                    ren.label("MME", location.MME - 30, 30)
                        .attr({
                            fill: colors[0],
                            stroke: "white",
                            "stroke-width": 2,
                            padding: 10,
                            r: 5
                        })
                        .css({
                            color: "white",
                            fontSize: "16px"
                        })
                        .add()
                        .shadow(true);
                    ren.label("Target eNB", location.targeteNB - 55, 30)
                        .attr({
                            fill: colors[0],
                            stroke: "white",
                            "stroke-width": 2,
                            padding: 10,
                            r: 5
                        })
                        .css({
                            color: "white",
                            fontSize: "16px"
                        })
                        .add()
                        .shadow(true);

                    for (var i = 0; i < rowlen; i++) {
                        var typearr = data.records[i].eventName.split("_");
                        var type = typearr[0];

                        var id = data.records[i].id;
                        var eventName = data.records[i].eventName;
                        var direction = data.records[i].direction;
                        var eventTime = data.records[i].eventTime;
                        var ueRef = data.records[i].ueRef;
                        var ecgi = data.records[i].ecgi;
                        var y = yelement + (i + 1) * yinterval;
                        var source, target;
                        if (direction == "EVENT_VALUE_SENT") {
                            source = parseInt(location[map[type].source]);
                            target = parseInt(location[map[type].target]);
                        } else {
                            source = parseInt(location[map[type].target]);
                            target = parseInt(location[map[type].source]);
                        }
                        var linetri;
                        if (target > source) {
                            linetri = ["M", source, y, "L", target, y, "L", (target - 5), (y - 5), "M", target, y, "L", (target - 5), (y + 5)];
                        } else if (target < source) {
                            linetri = ["M", source, y, "L", target, y, "L", (target + 5), (y - 5), "M", target, y, "L", (target + 5), (y + 5)];
                        } else {
                            linetri = ["M", (target + 150), y, "L", (target - 150), y];
                        }
                        box[i] = ren.path(linetri)
                            .attr({
                                "stroke-width": 2,
                                stroke: map[type].color,
                                "target": id,
                                "title": ueRef
                            }).add();
                        box[i].on("click", function() {
                            uechoosed($(this).attr("title"));
                        });
                        box[i].on("dblclick", function() {
                            eventMessageDetail($(this).attr("target"));
                        });
                        var mid = (source + target) / 2;
                        var ymessage = y;

                        message[i] = ren.label(eventName, mid - 100, ymessage - 20)
                            .attr({
                                "target": id,
                                "title": ueRef
                            })
                            .css({
                                color: "black",
                                fontSize: "10px"
                            })
                            .add();
                        message[i].on("click", function() {
                            uechoosed($(this).attr("title"));
                        });
                        message[i].on("dblclick", function() {
                            eventMessageDetail($(this).attr("target"));
                        });
                        var time = ren.label(eventTime, 5, y - 10)
                            .css({
                                color: "black",
                                fontSize: "8px"
                            })
                            .add();
                        if (type == "RRC") {
                            var ueid = ren.label("(UE:" + ueRef + ")", mid - 100, ymessage)
                                .attr({
                                    "target": id,
                                    "title": ueRef
                                })
                                .css({
                                    color: "gray",
                                    fontSize: "8px"
                                })
                                .add();
                            ueid.on("click", function() {
                                uechoosed($(this).attr("title"));
                            });
                            ueid.on("dblclick", function() {
                                eventMessageDetail($(this).attr("target"));
                            });
                        }
                        if (type == "S1") {
                            var ecgi = ren.label("(plmnId:" + ecgi + ")", mid - 100, ymessage)
                                .attr({
                                    "target": id,
                                    "title": ueRef
                                })
                                .css({
                                    color: "gray",
                                    fontSize: "8px"
                                })
                                .add();
                            ecgi.on("click", function() {
                                uechoosed($(this).attr("title"));
                            });
                            ecgi.on("dblclick", function() {
                                eventMessageDetail($(this).attr("target"));
                            });
                        }
                    }
                }
            }
        }
    });
}

function switchTab(div1, div2, type) {
    $(div2).removeClass("active");
    $(div1).addClass("active");
    if (type == "chart") {
        if ($("#ueRefChoosed").val()) {
            doSearchEvent_chart();
        }
    }
}

function exportProcessPicture() {
    var chart = $("#signalingChart").highcharts();
    chart.exportChart({
        exportFormat: "PNG"
    });
}