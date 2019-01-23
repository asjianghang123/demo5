$(document).ready(function() {
    toogle("siteManage");
    setTree4G();
    //setTree2G();

});

function setTree4G() {
    $.post("siteManage/TreeQuery", null, function(data) {
        var options = {
            bootstrap2: false,
            showTags: true,
            levels: 2,
            data: data,
            onNodeSelected: function(event, data) {
                /*if (data.value == 'city') {
                	layer.open({
                		title: "提示",
                		content: "请选择城市",
                		fixed: false
                	});
                	return;
                }*/
                $("#cityValue").val(data.value);
                var type = $("#siteType").val();
                if (type == "siteManage") {
                    doQuery4G(data.value);
                    getAbnormalStationCounts(data.value);
                    if (data.value != 'city') {
                        //$("#majorActivities_box").show();
                        //$("#majorActivities_2G_box").hide();
                        //queryMajorActivities(data.value);
                        $("#newSiteLte_box").show();
                        doQueryNewSite(data.value);
                        $("#IP_box").show();
                        //queryIP(data.value);
                        doQueryIpListFile(data.value);
                    }

                } else if (type == "2GSiteManage") {
                    doQuery2G(data.value);
                    $("#newSiteLte_box").hide();
                    $("#IP_box").hide();
                    //$("#majorActivities_box").hide();
                    //$("#majorActivities_2G_box").show();
                    //queryMajorActivities_2G(data.value);
                } else if (type == "otherSiteManage") {
                    doQueryOther4G(data.value);
                    $("#newSiteLte_box").hide();
                    $("#IP_box").hide();
                    //$("#majorActivities_box").hide();
                    //$("#majorActivities_2G_box").hide();
                } else if (type == "3GSiteManage") {
                    doQuery3G(data.value);
                    $("#newSiteLte_box").hide();
                    $("#IP_box").hide();
                    //$("#majorActivities_box").hide();
                }
            }
        };
        $("#cityTree").treeview(options);
    });
}

//清空模板树
function clear4GQuery() {
    $("#paramsQuery4G").val("");
    setTree4G();
}

function clear2GQuery() {
    $("#paramsQuery2G").val("");
    setTree2G();
}

//筛选模板树
function search4GQuery() {
    var pattern = $("#paramsQuery4G").val();
    $("#cityTree").on("searchComplete", function(event, data) {
        var moData = [];
        for (var i in data) {
            var obj = {
                id: data[i].id,
                text: data[i].text,
                value: data[i].value
            };
            moData.push(obj);
        }
        var options = {
            bootstrap2: false,
            showTags: true,
            levels: 2,
            data: moData,
            onNodeSelected: function(event, data) {
                $("#cityValue").val(data.value);
                doQuery4G(data.value);
            }
        };

        $("#cityTree").treeview(options);
    });
    $("#cityTree").treeview("search", [pattern, {
        ignoreCase: true, // case insensitive
        exactMatch: false, // like or equals
        revealResults: true // reveal matching nodes
    }]);

}

function search2GQuery() {
    var pattern = $("#paramsQuery2G").val();
    $("#2GQueryTree").on("searchComplete", function(event, data) {
        var moData = [];
        for (var i in data) {
            var obj = {
                id: data[i].id,
                text: data[i].text,
                value: data[i].value
            };
            moData.push(obj);
        }
        var options = {
            bootstrap2: false,
            showTags: true,
            levels: 2,
            data: moData,
            onNodeSelected: function(event, data) {
                $("#cityValue").val(data.value);
                doQuery2G(data.value);
            }
        };

        $("#2GQueryTree").treeview(options);
    });
    $("#2GQueryTree").treeview("search", [pattern, {
        ignoreCase: true, // case insensitive
        exactMatch: false, // like or equals
        revealResults: true // reveal matching nodes
    }]);

}


function doQuery4G(value) {
    var params = {
        value: value
    };
    var fieldArr = [];
    var text = "id,ecgi,ECI,cellName,cellNameChinese,siteName,siteNameChinese,duplexMode,rsi,tac,longitude,latitude,dir,pci,earfcn,siteType,cellType,tiltM,tiltE,antHeight,dualBandNetwork,CANetwork,address,band,channelBandWidth,noofTxAntennas(Site),highTraffic,highInterference,HST,cluster,subNetwork,currentOSS,覆盖属性,是否在网,city,importDate";
    var textArr = text.split(",");
    for (var k in textArr) {
        if (textArr[k] != 'id') {
            fieldArr[fieldArr.length] = {
                field: textArr[k],
                title: textArr[k],
                align: "left"
            };
        }
    }
    $("#siteTable").bootstrapTable("destroy");
    $("#siteTable").bootstrapTable({
        url: "siteManage/QuerySite4G",
        search: true,
        //searchText:'输入小区名',
        searchOnEnterKey: true,
        method: "get",
        striped: true,
        dataType: "json",
        pagination: true,
        pageList: [10, 20, 50, 100],
        pageSize: 10,
        pageNum: 1,
        idField: "id",
        sidePagination: "server", //设置为服务器端分页
        queryParams: function queryParams(p) { //设置查询参数 
            params.page = p.pageNumber;
            params.limit = p.pageSize;
            params.searchText = p.searchText;
            $("#searchText_site").val(p.searchText);
            return params;
        },
        queryParamsType: null,
        cache: false,
        showColumns: false,
        showToggle: false, //是否显示详细视图和列表视图的切换按钮
        cardView: false,
        detailView: true,
        detailFormatter: function detailFormatter(index, row) {
            var html = [];
            $.each(row, function(key, value) {
                html.push("<p><b>" + key + ":</b> " + value + "</p>");
            });
            return html.join("");
        },
        columns: fieldArr,
        onLoadSuccess: function() { //加载成功时执行 

        },
        onLoadError: function() { //加载失败时执行 
            layer.open({
                title: "提示",
                content: "查询失败，请重试"
            });
        }
    });
}

function doQuery2G(value) {
    var params = {
        value: value
            // table: "siteGsm"
    };
    var fieldArr = [];
    var text = "id,CELL,CellNameChinese,CellIdentity,BAND,ARFCN,Longitude,Latitude,dir,cellType,height,plmnIdentity_mcc,plmnIdentity_mnc,LAC,BCCH,BCC,NCC,dtmSupport,city,importDate";
    var textArr = text.split(",");
    for (var k in textArr) {
        if (textArr[k] != 'id') {
            fieldArr[fieldArr.length] = {
                field: textArr[k],
                title: textArr[k],
                align: "left"
            };
        }
    }
    $("#siteTable").bootstrapTable("destroy");
    $("#siteTable").bootstrapTable({
        url: "siteManage/QuerySite2G",
        search: true,
        //searchText:'输入小区名',
        searchOnEnterKey: true,
        method: "get",
        striped: true,
        dataType: "json",
        pagination: true,
        pageList: [10, 20, 50, 100],
        pageSize: 10,
        pageNum: 1,
        idField: "id",
        sidePagination: "server", //设置为服务器端分页
        queryParams: function queryParams(p) { //设置查询参数 
            params.page = p.pageNumber;
            params.limit = p.pageSize;
            params.searchText = p.searchText;
            $("#searchText_site").val(p.searchText);
            return params;
        },
        queryParamsType: null,
        cache: false,
        showColumns: false,
        showToggle: false, //是否显示详细视图和列表视图的切换按钮
        cardView: false,
        detailView: true,
        detailFormatter: function detailFormatter(index, row) {
            var html = [];
            $.each(row, function(key, value) {
                html.push("<p><b>" + key + ":</b> " + value + "</p>");
            });
            return html.join("");
        },
        columns: fieldArr,
        onLoadSuccess: function() { //加载成功时执行 

        },
        onLoadError: function() { //加载失败时执行 
            layer.open({
                title: "提示",
                content: "查询失败，请重试"
            });
        }
    });
}

function doQuery3G(value) {
    var data = {
        value: value,
        table: "siteCdma"
    };
    var fieldArr = [];
    var text = "id,Cell,SiteNo,Name,Area,MSC,RNC,RNCID,cid,LAC,PSC,uarfcnDl,type,indoor/outdoor,Longitude,Latitude,Address,Antennaaddress,3Gantennaheight,3Gantennadirection,Federlength(),3Gantennatype,3GantennaH-beam,3GantennaV-beam,3GE-tiltrange,3GantennaE-tilt,3GantennaM-tilt,dualbandantennawithGSM,dualbandantennawithDCS,Co-siteGSMsite,Co-siteGSMsitename,GSMantennaheight,GSMantennadirection,GSMantennatilt,Co-siteDCSsite,Co-siteDCSsitename,DCSantennaheight,DCSantennadirection,DCSantennatilt,city,importDate";
    var textArr = text.split(",");
    for (var i in textArr) {
        if (fieldArr.length == 0) {
            fieldArr[fieldArr.length] = {
                field: textArr[fieldArr.length],
                title: textArr[fieldArr.length],
                hidden: true
            };
        } else if (textArr[fieldArr.length] == "Co-siteGSMsitename" | textArr[fieldArr.length] == "dualbandantennawithDCS" | textArr[fieldArr.length] == "dualbandantennawithGSM") {
            fieldArr[fieldArr.length] = { field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 200 };
        } else {
            fieldArr[fieldArr.length] = { field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 150 };
        }
    }

    $("#siteTable").grid("destroy", true, true);
    var grid = $("#siteTable").grid({
        columns: fieldArr,
        dataSource: {
            url: "siteManage/QuerySite3G",
            //data: {},
            success: function(data) {
                data = eval("(" + data + ")");
                grid.render(data);
            }
        },
        params: data,
        pager: { limit: 10, sizes: [10, 20, 50, 100] },
        autoScroll: true,
        uiLibrary: "bootstrap",
        primaryKey: "id",
        autoLoad: true
    });
}

function doQueryOther4G(value) {
    var params = {
        value: value,
        table: "otherSiteLte"
            //table : "siteLteBak0118"
    };
    var fieldArr = [];
    var text = "id,ecgi,cellName,cellNameChinese,siteName,siteNameChinese,tac,longitude,latitude,dir,pci,earfcn,siteType,cellType,tiltM,tiltE,antHeight,dualBandNetwork,band,channelBandWidth,noofTxAntennas(Site),cluster,覆盖属性,厂商,city,importDate";
    var textArr = text.split(",");
    for (var k in textArr) {
        if (textArr[k] != 'id') {
            fieldArr[fieldArr.length] = {
                field: textArr[k],
                title: textArr[k],
                align: "left"
            };
        }
    }
    $("#siteTable").bootstrapTable("destroy");
    $("#siteTable").bootstrapTable({
        url: "siteManage/QuerySiteOther4G",
        search: true,
        //searchText:'输入小区名',
        searchOnEnterKey: true,
        method: "get",
        striped: true,
        dataType: "json",
        pagination: true,
        pageList: [10, 20, 50, 100],
        pageSize: 10,
        pageNum: 1,
        idField: "id",
        sidePagination: "server", //设置为服务器端分页
        queryParams: function queryParams(p) { //设置查询参数 
            params.page = p.pageNumber;
            params.limit = p.pageSize;
            params.searchText = p.searchText;
            $("#searchText_site").val(p.searchText);
            return params;
        },
        queryParamsType: null,
        cache: false,
        showColumns: false,
        showToggle: false, //是否显示详细视图和列表视图的切换按钮
        cardView: false,
        detailView: true,
        detailFormatter: function detailFormatter(index, row) {
            var html = [];
            $.each(row, function(key, value) {
                html.push("<p><b>" + key + ":</b> " + value + "</p>");
            });
            return html.join("");
        },
        columns: fieldArr,
        onLoadSuccess: function() { //加载成功时执行 

        },
        onLoadError: function() { //加载失败时执行 
            layer.open({
                title: "提示",
                content: "查询失败，请重试"
            });
        }
    });
}

function import4G() {
    importSite("4G");
}

function import2G() {
    importSite("2G");
}

function import3G() {
    importSite("3G");
}

function importOther4G() {
    importSite("other4G");
}

function importSite(type) {
    var city = $("#cityValue").val();
    if (city == "") {
        //alert("请选择城市");
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return false;
    };
    $("#import_modal").modal();
    $("#siteSign").val(type);
    $("#fileImportName").val("");
    $("#fileImport").val("");
}

function majorActivities_import4G() {
    majorActivities_importSite("4G");
}

function majorActivities_import2G() {
    majorActivities_importSite("2G");
}

function majorActivities_importOther4G() {
    majorActivities_importSite("other4G");
}

function majorActivities_importSite(type) {
    var city = $("#cityValue").val();
    if (city == "") {
        //alert("请选择城市");
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return false;
    }
    $("#majorActivities_import_modal").modal();
    $("#siteSign").val(type);
    $("#majorActivities_fileImportName").val("");
    $("#majorActivities_fileImport").val("");
}

function majorActivities_toName(self) {
    $("#majorActivities_fileImportName").val(self.value);
}

function toName(self) {
    $("#fileImportName").val(self.value);
}

function importFile() {
    var siteSign = $("#siteSign").val();
    var table = "";
    var city = $("#cityValue").val();
    if (siteSign == "4G") {
        table = "siteManage";
    } else if (siteSign == "2G") {
        table = "2GSiteManage";
    } else if (siteSign == "other4G") {
        table = "otherSiteManage";
    } else if (siteSign == "3G") {
        table = "3GSiteManage";
    }

    var params = getParam(table);
    if (params == false) {
        return false;
    }
    var E = Ladda.create(document.getElementById("importBtn"));
    E.start();
    $.ajaxFileUpload({
        url: "siteManage/uploadFile",
        data: params,
        fileElementId: "fileImport",
        secureuri: true,
        dataType: "json",
        type: "post",
        success: function(data, status) {
            params.fileName = data;
            $.post("siteManage/getFileContent", params, function(data) {
                E.stop();
                $("#import_modal").modal("hide");
                if (data.hasOwnProperty('message')) {
                    layer.open({
                        title: "提示",
                        content: "上传失败,有重复ecgi：" + data['message'],
                        fixed: false
                    });
                    return;
                }
                if (data['sign'] == "4G") {
                    doQuery4G(city);
                } else if (data['sign'] == "2G") {
                    doQuery2G(city);
                } else if (data['sign'] == "other4G") {
                    doQueryOther4G(city);
                } else if (data['sign'] == "3G") {
                    doQuery3G(city);
                }
                //alert("上传成功");
                if (data.hasOwnProperty('params')) {
                    var params = "";
                    for (var key in data['params']) {
                        params = params + "<br/>" + data['params'][key];
                    }
                    layer.open({
                        title: "提示",
                        content: "上传失败,请重新导入,导入数据中存在双引号（\"）,有问题的参数如下:" + params,
                        fixed: false
                    });
                } else {
                    var cellNames = "";
                    if (data.hasOwnProperty('result') && data['result'].length > 0) {
                        cellNames = ",重复小区如下：";
                        for (var key in data['result']) {
                            cellNames = cellNames + "<br/>" + data['result'][key].cellName;
                        }
                    }
                    layer.open({
                        title: "提示",
                        content: "上传成功" + cellNames,
                        fixed: false
                    });
                }
            });

        },
        error: function(data, status, e) {
            //alert("上传失败");
            layer.open({
                title: "提示",
                content: "上传失败"
            });
        }
    });
}
//获取当前时间
function getNowFormatDate() {
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate + " " + date.getHours() + seperator2 + date.getMinutes() + seperator2 + date.getSeconds();
    return currentdate;
}

function getParam(table) {
    var importDate = getNowFormatDate();
    var city = $("#cityValue").val();

    if (city == "" || city == "city") {
        //alert("请选择城市！");
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return false;
    }
    var importType = $("input[type='radio'][name='importType']:checked")[0].id;
    var data = { table: table, city: city, importDate: importDate, importType: importType };

    if ($("#fileImport").val() == "") {
        //alert("请选择上传的文件！");
        layer.open({
            title: "提示",
            content: "请选择上传的文件"
        });
        return false;
    }
    return data;
}

function getMAParam(table) {
    var importDate = getNowFormatDate();
    var city;
    if (table == "siteManage") {
        city = $("#cityValue").val();

        if (city == "" || city == "city") {
            //alert("请选择城市！");
            layer.open({
                title: "提示",
                content: "请选择城市"
            });
            return false;
        }
        var data = { table: table, city: city, importDate: importDate };
    } else if (table == "2GSiteManage") {
        city = $("#cityValue").val();

        if (city == "" || city == "city") {
            //alert("请选择城市！");
            layer.open({
                title: "提示",
                content: "请选择城市"
            });
            return false;
        }
        var data = { table: table, city: city, importDate: importDate };
    }

    if ($("#majorActivities_fileImport").val() == "") {
        //alert("请选择上传的文件！");
        layer.open({
            title: "提示",
            content: "请选择上传的文件"
        });
        return false;
    }
    return data;
}

function export4G() {
    /*var E = Ladda.create(document.getElementById("export"));
    E.start();*/
    filetoExport("siteManage");
    /*E.stop();*/
}

function export2G() {
    /*var E = Ladda.create(document.getElementById("export"));
    E.start();*/
    filetoExport("2GSiteManage");
    /*E.stop();*/
}

function export3G() {
    var E = Ladda.create(document.getElementById("export"));
    E.start();
    filetoExport("3GSiteManage");
    E.stop();
}

function exportOther4G() {
    /*var E = Ladda.create(document.getElementById("export"));
    E.start();*/
    filetoExport("otherSiteManage");
    /*E.stop();*/
}

function filetoExport(table) {
    var city = $("#cityValue").val();
    if (city == "") {
        //alert("请选择城市！");
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return false;
    }
    var E = Ladda.create(document.getElementById("export"));
    E.start();
    var params = {
        city: city,
        table: table,
        searchText: $("#searchText_site").val()
    };
    $.get("siteManage/downloadFile", params, function(data) {
        if (data.result) {
            fileDownload(data.fileName);
            E.stop();
        } else {
            //alert("There is error occured!");
            layer.open({
                title: "提示",
                content: "下载失败"
            });
        }
    });
}

function exportTemplate() {
    var table = $("#siteType").val();
    var city = $("#cityValue").val();
    if (city == "") {
        //alert("请选择城市！");
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return;
    }
    params = {
        city: city,
        table: table
    };
    var E = Ladda.create(document.getElementById("exportTemplate"));
    E.start();
    $.get("siteManage/downloadTemplateFile", params, function(data) {
        E.stop();
        if (data.result) {
            fileDownload(data.fileName);
        } else {
            //alert("There is error occured!");
            layer.open({
                title: "提示",
                content: "下载失败"
            });
        }
    });
}

function switchTab(type) {
    // $("#siteTable").grid("destroy", true, true);
    $("#siteTable").bootstrapTable("destroy");
    $("#siteType").val(type);
    if (type == "siteManage") {
        $("#newSiteLte_box").show();
        $("#IP_box").show();
        $("#abnormal_station_box").show();
        //$("#majorActivities_box").show();
        //$("#majorActivities_2G_box").hide();
    } else if (type == "2GSiteManage") {
        $("#newSiteLte_box").hide();
        $("#IP_box").hide();
        $("#abnormal_station_box").hide();
        //$("#majorActivities_box").hide();
        //$("#majorActivities_2G_box").show();
    } else if (type == "otherSiteManage") {
        $("#newSiteLte_box").hide();
        $("#IP_box").hide();
        $("#abnormal_station_box").hide();
        //$("#majorActivities_box").hide();
        //$("#majorActivities_2G_box").hide();
    } else if (type == "3GSiteManage") {
        $("#newSiteLte_box").hide();
        $("#IP_box").hide();
        $("#abnormal_station_box").hide();
        //$("#majorActivities_box").hide();
        //$("#majorActivities_2G_box").hide();
    }
}

function importTemplate() {
    var type = $("#siteType").val();
    $("#importBtn").attr("onclick", "importFile()");
    $("#fileImport").attr("accept", ".csv");
    if (type == "siteManage") {
        import4G();
    } else if (type == "2GSiteManage") {
        import2G();
    } else if (type == "otherSiteManage") {
        importOther4G();
    } else if (type == "3GSiteManage") {
        import3G();
    }
}

function exportSite() {
    /*var E = Ladda.create(document.getElementById("export"));
    E.start();*/
    var type = $("#siteType").val();
    if (type == "siteManage") {
        export4G();
    } else if (type == "2GSiteManage") {
        export2G();
    } else if (type == "otherSiteManage") {
        exportOther4G();
    } else if (type == "3GSiteManage") {
        export3G();
    }
    /*E.stop();*/
}

function queryMajorActivities(city) {
    var data = {
        city: city
    };
    var fieldArr = [];
    var text = "city,templateName_title,templateName,templateName_checkRange,cell";
    var textArr = text.split(",");
    for (var i in textArr) {
        fieldArr[i] = { field: textArr[i], title: textArr[i], width: 150 };
    }
    $("#majorActivitiesTable").grid("destroy", true, true);
    var grid = $("#majorActivitiesTable").grid({
        columns: fieldArr,
        dataSource: {
            url: "siteManage/QueryMajorActivities",
            success: function(data) {
                data = JSON.parse(data);
                grid.render(data);
            }
        },
        params: data,
        pager: { limit: 10, sizes: [10, 20, 50, 100] },
        autoScroll: true,
        uiLibrary: "bootstrap",
        primaryKey: "id",
        autoLoad: true
    });
}

function queryMajorActivities_2G(city) {
    var data = {
        city: city
    };
    var fieldArr = [];
    var text = "city,templateName_title,templateName,templateName_checkRange,cell,bsc,configure";
    var textArr = text.split(",");
    for (var i in textArr) {
        fieldArr[i] = { field: textArr[i], title: textArr[i], width: 200 };
    }
    $("#majorActivities_2GTable").grid("destroy", true, true);
    var grid = $("#majorActivities_2GTable").grid({
        columns: fieldArr,
        dataSource: {
            url: "siteManage/QueryMajorActivities_2G",
            success: function(data) {
                data = JSON.parse(data);
                grid.render(data);
            }
        },
        params: data,
        pager: { limit: 10, sizes: [10, 20, 50, 100] },
        autoScroll: true,
        uiLibrary: "bootstrap",
        primaryKey: "id",
        autoLoad: true
    });
}

/*grid.on('rowSelect', function (e, $row, id, record) {
	$('#p_majorActivities_id').html("<input id='input1' class='col-sm-6' placeholder='公式 例如：RRC建立请求次数'/><input id='input2' class='col-sm-3' placeholder='只可填 >= = <= > <' /><input id='input3' class='col-sm-3' placeholder='只允许填写数字' />");
	window.ptemplateName = record.templateName;
	window.pcity = record.city;
	window.pid = record.id;
	window.ptemplateTitle = record.templateName_title;
	// console.log(record);
	$('#p_majorActivities_modal').modal('show');
	$('#p_majorActivities').html(ptemplateName);
});
// console.log(pid);
$('#next').click(function(){
	if($('#input1').val() == '' || $('#input2').val() == '' || $('#input3').val() == '') {
		layer.open({
			title: '提示',
			content: '请检查输入项是否有误！'
		});
		return;
	}
	var params = {
		city:pcity,
		templateTitle:ptemplateTitle,
		templateName:ptemplateName,
		id:pid,
		kpi:$('#input1').val(),
		symbol:$('#input2').val(),
		num:$('#input3').val(),
		andOr:$('#input4').val(),
	};
	$.ajax({
		type:'POST',
		url:'siteManage/insertdata',
		data:params,
		async: false,
		cache: false,
		success: function (data) {
			console.log(data);
		}
	});
	$("#input1").removeAttr("id");$("#input2").removeAttr("id");$("#input3").removeAttr("id");$("#input4").removeAttr("id");
	$('#p_majorActivities_id').append("<input id='input4' class='col-sm-12' placeholder='只可填 AND OR'/>");
	$('#p_majorActivities_id').append("<input id='input1' class='col-sm-6' placeholder='公式 例如：RRC建立请求次数'/><input id='input2' class='col-sm-3' placeholder='只可填 >= = <= > <' /><input id='input3' class='col-sm-3' placeholder='只允许填写数字' />");
});

$('#p_majorActivities_importBtn').click(function(){
	var params = {
		city:pcity,
		templateTitle:ptemplateTitle,
		templateName:ptemplateName,
		id:pid,
		kpi:$('#input1').val(),
		symbol:$('#input2').val(),
		num:$('#input3').val(),
		andOr:$('#input4').val(),
	};
	$.ajax({
		type:'POST',
		url:'siteManage/insertdata',
		data:params,
		async: false,
		cache: false,
		success: function (data) {
			alert(data)
			$('#p_majorActivities_modal').modal('hide');
		}
	});
});*/


function queryIP(city) {
    var data = {
        city: city
    };
    var fieldArr = [];
    var text = "serverName,city,type,externalAddress,internalAddress,subNetwork,fileDir";
    var textArr = text.split(",");
    for (var i in textArr) {
        fieldArr[i] = { field: textArr[i], title: textArr[i], width: 150 };
    }
    $("#IPTable").grid("destroy", true, true);
    var grid = $("#IPTable").grid({
        columns: fieldArr,
        dataSource: {
            url: "siteManage/QueryIP",
            success: function(data) {
                data = JSON.parse(data);
                grid.render(data);
                var dir = "szuser@" + data.records[0].externalAddress + ":" + data.records[0].fileDir.replace("logs", "script");
                $("#IP_Address").val(dir);
            }
        },
        params: data,
        pager: { limit: 10, sizes: [10, 20, 50, 100] },
        autoScroll: true,
        uiLibrary: "bootstrap",
        primaryKey: "id",
        autoLoad: true
    });
}

function majorActivities_export() {
    $.post("siteManage/majorActivities_export", "", function(data) {
        if (data.result) {
            fileDownload(data.fileName);
        } else {
            layer.open({
                title: "提示",
                content: "下载失败"
            });
        }
    });
}

function majorActivities_2G_export() {
    $.post("siteManage/majorActivities_2G_export", "", function(data) {
        if (data.result) {
            fileDownload(data.fileName);
        } else {
            layer.open({
                title: "提示",
                content: "下载失败"
            });
        }
    });
}

function majorActivities() {
    var type = $("#siteType").val();
    if (type == "siteManage" || type == "2GSiteManage") {
        var city = $("#cityValue").val();
        if (city == "" || city == "city") {
            layer.open({
                title: "提示",
                content: "请选择城市"
            });
            return;
        }
        $("#majorActivities_modal").modal();
        $("#majorActivities_fileImportName").val("");
        $("#majorActivities_fileImport").val("");
        $("#majorActivities_importBtn").attr("onclick", "majorActivities_importFile()");
        $("#majorActivities_fileImport").attr("accept", ".csv");
    }
    if (type == "siteManage") {
        majorActivities_import4G();
    } else if (type == "2GSiteManage") {
        majorActivities_import2G();
    } else {
        majorActivities_importOther4G();
    }
}

function importIP() {
    var city = $("#cityValue").val();
    if (city == "" || city == "city") {
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return;
    }
    $("#import_modal").modal();
    $("#fileImportName").val("");
    $("#fileImport").val("");
    $("#importBtn").attr("onclick", "importIPFile()");
    $("#fileImport").attr("accept", ".txt");
}

function majorActivities_importFile() {
    var siteSign = $("#siteSign").val();
    var table = "";
    var city = $("#cityValue").val();
    if (siteSign == "4G") {
        table = "siteManage";
    } else if (siteSign == "2G") {
        table = "2GSiteManage";
    } else {
        table = "otherSiteManage";
    }
    console.log(table);
    var params = getMAParam(table);
    /*console.log(params);*/
    if (params == false) {
        alert("false");
        return false;
    }
    var M = Ladda.create(document.getElementById("majorActivities_importBtn"));
    M.start();
    $.ajaxFileUpload({
        url: "siteManage/majorActivities_uploadFile",
        data: params,
        fileElementId: "majorActivities_fileImport",
        secureuri: true,
        dataType: "json",
        type: "post",
        success: function(data, status) {
            /*console.log(data);*/
            params.fileName = data;
            $.post("siteManage/getMAFileContent", params, function(data) {
                console.log(data);
                M.stop();
                $("#majorActivities_modal").modal("hide");
                if (data == "4G") {
                    queryMajorActivities(city);
                } else if (data == "2G") {
                    queryMajorActivities_2G(city);
                } else if (data == "other4G") {
                    return;
                }

                //alert("上传成功");
                layer.open({
                    title: "提示",
                    content: "上传成功"
                });
            });

        },
        error: function(data, status, e) {
            //alert("上传失败");
            layer.open({
                title: "提示",
                content: "上传失败"
            });
        }
    });
}

function importIPFile() {
    if ($("#fileImport").val() == "") {
        layer.open({
            title: "提示",
            content: "请选择上传的文件"
        });
        return;
    }
    var params = {
        city: $("#cityValue").val(),
        dir: $("#IP_Address").val()
    };
    console.log(params)
    var E = Ladda.create(document.getElementById("importBtn"));
    E.start();
    $.ajaxFileUpload({
        url: "siteManage/uploadIPFile",
        data: params,
        fileElementId: "fileImport",
        secureuri: true,
        dataType: "json",
        type: "post",
        success: function(data, status) {
            E.stop();
            if (data) {
                $("#import_modal").modal("hide");
                layer.open({
                    title: "提示",
                    content: "上传成功"
                });
            } else {
                layer.open({
                    title: "提示",
                    content: "上传失败"
                });
            }

        },
        error: function(data, status, e) {
            E.stop();
            layer.open({
                title: "提示",
                content: "上传失败"
            });
        }
    });
}
//----------------------------------------新站站点信息--------------
function doQueryNewSite(city) {
    var params = {
        city: city,
        //table: "newSiteLte"
    };
    var fieldArr = [];
    $.post("siteManage/getNewSiteLteField", params, function(data) {
        if (data.result == "error") {
            $("#newSiteLteTable").bootstrapTable("destroy");
            layer.open({
                title: "提示",
                content: "没有记录"
            });
            return;
        } else {
            fieldArr[fieldArr.length] = { checkbox: true };
            for (var k in data) {
                if (data[k] != 'id') {
                    fieldArr[fieldArr.length] = { field: data[k], title: data[k], align: "left" };
                }
            }
            $("#newSiteLteTable").bootstrapTable("destroy");
            $("#newSiteLteTable").bootstrapTable({
                url: "siteManage/getNewSiteLte",
                search: true,
                //searchText:'输入小区名',
                searchOnEnterKey: true,
                method: "post",
                striped: true,
                dataType: "json",
                pagination: true,
                pageList: [10, 20, 50, 100],
                pageSize: 10,
                pageNum: 1,
                idField: "id",
                sidePagination: "server", //设置为服务器端分页
                queryParams: function queryParams(p) { //设置查询参数 
                    params.page = p.pageNumber;
                    params.limit = p.pageSize;
                    params.city = city;
                    params.searchText = p.searchText;
                    $("#searchText").val(p.searchText);
                    return params;
                },
                queryParamsType: null,
                cache: false,
                showColumns: false,
                showToggle: false, //是否显示详细视图和列表视图的切换按钮
                cardView: false,
                detailView: true,
                detailFormatter: function detailFormatter(index, row) {
                    var html = [];
                    $.each(row, function(key, value) {
                        html.push("<p><b>" + key + ":</b> " + value + "</p>");
                    });
                    return html.join("");
                },
                columns: fieldArr,
                onLoadSuccess: function() { //加载成功时执行 

                },
                onLoadError: function() { //加载失败时执行 
                    layer.open({
                        title: "提示",
                        content: "查询失败，请重试"
                    });
                }
            });
        }
    });
}

function deleteNewSite() {

    layer.confirm("确认删除吗？", { title: "提示" }, function(index) {
        layer.close(index);
        var D = Ladda.create(document.getElementById("deleteNewSite"));
        D.start();
        var selections = $("#newSiteLteTable").bootstrapTable("getAllSelections");
        var ids = [];
        if (selections.length > 0) {
            for (var i = 0; i < selections.length; i++) {
                ids[i] = selections[i].id;
            };
            var params = {
                ids: ids
            }
            $.post('siteManage/deleteNewSiteLte', params, function(data) {
                D.stop();
                doQueryNewSite($("#cityValue").val());
                layer.open({
                    title: "提示",
                    content: "成功删除" + data + "条记录!"
                });
            });
        } else {
            D.stop();
            layer.open({
                title: "提示",
                content: "未选择删除项！"
            });
        }
    });
}

function exportNewSite() {
    var params = {
        city: $("#cityValue").val(),
        searchText: $("#searchText").val()
    }
    var E = Ladda.create(document.getElementById("exportNewSite"));
    E.start();
    $.post("siteManage/exportNewSiteLte", params, function(data) {
        E.stop();
        if (data.result) {
            fileDownload(data.fileName);
        } else {
            layer.open({
                title: "提示",
                content: "下载失败"
            });
        }
    });
}
//----------------------------------------新站站点信息--------------
//----------------------------------------ipListFile编辑--------------
function doQueryIpListFile(city) {
    $("#ipListContent").attr("disabled", true);
    $("#ipListContent").val("");
    var data = {
        city: city
    };
    $("#IPNum").html("");
    $("#ipListBox").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
    $.post("siteManage/openIpListFile", data, function(data) {
        $("#ipListBox").html('<textarea class="form-control" name="ipListContent" id="ipListContent" disabled="disabled" style="height:400px;resize:none;"></textarea>');
        var data = eval("(" + data + ")");
        var content = $("#ipListContent").val(data['content']);
        $("#IPNum").html(data['IPNum']);
    }, "html");
}
//编辑文件
function editIpListFile() {
    var city = $("#cityValue").val();
    if (city == "") {
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return false;
    }
    $("#ipListContent").attr("disabled", false);
}

function cancelEdit() {
    var c = Ladda.create(document.getElementById("cancelEdit"));
    c.start();
    doQueryIpListFile($("#cityValue").val());
    c.stop();
}

function saveIpListFile() {
    var city = $("#cityValue").val();
    if (city == "") {
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return false;
    }
    var s = Ladda.create(document.getElementById("saveIpListFile"));
    s.start();
    var content = $("#ipListContent").val();
    var params = {
        city: city,
        content: content
    };
    $.post("siteManage/saveIpListFile", params, function(data) {
        s.stop();
        doQueryIpListFile($("#cityValue").val());
        layer.open({
            title: "提示",
            content: "保存成功"
        });
    });

}


//----------------------------------------ipListFile编辑--------------

//----------------------------------------ipListFile导入导出--------------

function importIpList() {
    var type = $("#siteType").val();
    $("#ipListFile_importBtn").attr("onclick", "importIpListFile()");
    $("#ipListFile_fileImport").attr("accept", ".txt");
    var city = $("#cityValue").val();
    if (city == "") {
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return false;
    }
    $("#ipListFile_import_modal").modal();
    $("#ipListFile_fileImportName").val("");
    $("#ipListFile_fileImport").val("");

}

function ipListFile_toName(self) {
    $("#ipListFile_fileImportName").val(self.value);
}

function importIpListFile() {
    $("#ipListContent").val("");
    var city = $("#cityValue").val();
    if (city == "" || city == "city") {
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return false;
    }
    var params = {
        city: city,
        fileElementId: "ipListFile_fileImport"
    }
    var E = Ladda.create(document.getElementById("ipListFile_importBtn"));
    E.start();
    $.ajaxFileUpload({
        url: "siteManage/newUploadFile",
        data: params,
        fileElementId: "ipListFile_fileImport",
        secureuri: true,
        dataType: "json",
        type: "post",
        success: function(data, status) {
            params.fileName = data;
            $("#ipListContent").attr("disabled", true);
            $("#ipListContent").val("");
            $.post("siteManage/getIpListFileContent", params, function(data) {
                E.stop();
                $("#ipListFile_import_modal").modal("hide");
                var content = $("#ipListContent").val(data);
                layer.open({
                    title: "提示",
                    content: "上传成功"
                });
            }, "html");
        },
        error: function(data, status, e) {
            E.stop();
            layer.open({
                title: "提示",
                content: "上传失败"
            });
        }
    });
}

function exportIpListFile() {

    var city = $("#cityValue").val();
    if (city == "" || city == "city") {
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return false;
    }
    var E = Ladda.create(document.getElementById("exportIpListFile"));
    E.start();
    /*var url = "common/json/nodeList.json";
    $.get(url, null, function(data) {
        E.stop();
        var data = eval("(" + data + ")");
        var fileName = "common/txt/" + data[city].fileName;
        download(fileName);
    });*/
    $.ajax({
        type: 'POST',
        url: 'siteManage/exportIpListFile',
        data: {
            city: city
        },
        cache: true,
        success: function(data) {
            E.stop();
            var fileName = "common/txt/" + data;
            download(fileName);
        }
    });
}
//----------------------------------------ipListFile导入导出--------------

function getAbnormalStationCounts(city) {
    $(".abnormal_station_count").html("");
    $.ajax({
        type: 'POST',
        url: 'siteManage/getAbnormalStationCounts',
        data: {
            city: city
        },
        cache: true,
        success: function(data) {
            data = JSON.parse(data);
            for (var i in data) {
                $("#" + i).html(data[i]);
            }
        }
    });
}

function exportAbnormalStation(action) {
    var city = $("#cityValue").val();
    if (!city) {
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return false;
    }
    var count = $("#" + action + "_count").html();
    if (count == 0 || count == "" || count == 'no data') {
        layer.open({
            title: "提示",
            content: "该指标没有异常"
        });
        return false;
    }
    var params = {
        action: action,
        city: city,
        field: getFieldArr(action)
    };
    layer.msg('开始导出');
    $.post("siteManage/exportAbnormalStation", params, function(data) {
        if (data.result) {
            fileDownload(data.fileName);
        } else {
            layer.open({
                title: "提示",
                content: "下载失败"
            });
        }
    });
}

function getFieldArr(action) {
    var fieldArr = [];
    switch (action) {
        case 'cross_station':
            fieldArr = [{
                field: 'ECGI',
                title: 'ECGI'
            }, {
                field: 'ECELL',
                title: 'ECELL'
            }, {
                field: 'SITE',
                title: 'SITE'
            }, {
                field: 'ECI',
                title: 'ECI'
            }, {
                field: 'cellNameChinese',
                title: 'cellNameChinese'
            }, {
                field: 'siteNameChinese',
                title: 'siteNameChinese'
            }, {
                field: 'duplexMode',
                title: 'duplexMode'
            }, {
                field: 'cluster',
                title: 'cluster'
            }, {
                field: 'x1',
                title: 'Lon'
            }, {
                field: 'y1',
                title: 'Lat'
            }, {
                field: 'dir1',
                title: 'DIR'
            }, {
                field: 'n_ECGI',
                title: '邻小区ECGI'
            }, {
                field: 'n_ECELL',
                title: '邻小区ECELL'
            }, {
                field: 'n_SITE',
                title: '邻小区SITE'
            }, {
                field: 'n_ECI',
                title: '邻小区ECI'
            }, {
                field: 'n_cellNameChinese',
                title: '邻小区cellNameChinese'
            }, {
                field: 'n_siteNameChinese',
                title: '邻小区siteNameChinese'
            }, {
                field: 'n_cluster',
                title: '邻小区cluster'
            }, {
                field: 'x2',
                title: '邻小区Lon'
            }, {
                field: 'y2',
                title: '邻小区Lat'
            }, {
                field: 'dir2',
                title: '邻小区DIR'
            }, {
                field: 'earfcn',
                title: 'EARFCN	'
            }, {
                field: 'dist_km',
                title: '距离[千米]'
            }, {
                field: 'city',
                title: 'city'
            }];
            break;
        case 'same_station':
            fieldArr = [{
                field: 'ECGI',
                title: 'ECGI'
            }, {
                field: 'ECELL',
                title: 'ECELL'
            }, {
                field: 'SITE',
                title: 'SITE'
            }, {
                field: 'ECI',
                title: 'ECI'
            }, {
                field: 'cellNameChinese',
                title: 'cellNameChinese'
            }, {
                field: 'siteNameChinese',
                title: 'siteNameChinese'
            }, {
                field: 'duplexMode',
                title: 'duplexMode'
            }, {
                field: 'cluster',
                title: 'cluster'
            }, {
                field: 'x1',
                title: 'Lon'
            }, {
                field: 'y1',
                title: 'Lat'
            }, {
                field: 'dir1',
                title: 'DIR'
            }, {
                field: 'n_ECGI',
                title: '邻小区ECGI'
            }, {
                field: 'n_ECELL',
                title: '邻小区ECELL'
            }, {
                field: 'n_SITE',
                title: '邻小区SITE'
            }, {
                field: 'x2',
                title: '邻小区Lon'
            }, {
                field: 'y2',
                title: '邻小区Lat'
            }, {
                field: 'dir2',
                title: '邻小区DIR'
            }, {
                field: 'earfcn',
                title: 'EARFCN	'
            }, {
                field: 'dist_km',
                title: '距离[千米]'
            }, {
                field: 'dir_diff',
                title: 'DIR方向差[度]'
            }, {
                field: 'city',
                title: 'city'
            }];
            break;
        case 'azimuth_check':
            fieldArr = [{
                field: 'ECGI',
                title: 'ECGI'
            }, {
                field: 'ECELL',
                title: 'ECELL'
            }, {
                field: 'ECI',
                title: 'ECI'
            }, {
                field: 'cellNameChinese',
                title: 'cellNameChinese'
            }, {
                field: 'siteName',
                title: 'siteName'
            }, {
                field: 'siteNameChinese',
                title: 'siteNameChinese'
            }, {
                field: 'duplexMode',
                title: 'duplexMode'
            }, {
                field: 'cluster',
                title: 'cluster'
            }, {
                field: 'subNetwork',
                title: 'subNetwork'
            }, {
                field: 'currentOSS',
                title: 'currentOSS'
            }, {
                field: 'cellType',
                title: 'cellType'
            }, {
                field: 'dir0',
                title: 'DIR'
            }, {
                field: 'Result_Type',
                title: '结果[MRO / HO]'
            }, {
                field: 'Result_cnt_Sample',
                title: '所选邻宏小区个数[MRO / HO]'
            }, {
                field: 'Result_sum_Sample',
                title: '所选邻宏小区之总次数[MRO / HO]'
            }, {
                field: 'Result_dir_deg',
                title: '加权平均DIR[度][MRO / HO]'
            }, {
                field: 'Result_dir_diff',
                title: 'DIR方向差[度][MRO / HO]'
            }, {
                field: 'Result_chk_dir',
                title: 'DIR方向差检查[MRO / HO]'
            }, {
                field: 'HO_cnt_Sample',
                title: '所选邻宏小区个数[HO]'
            }, {
                field: 'HO_sum_Sample',
                title: '所选邻宏小区之总次数[HO]'
            }, {
                field: 'HO_N_ECGI_List',
                title: '所选邻宏小区（ 次数）[HO]'
            }, {
                field: 'HO_dir_deg',
                title: '加权平均DIR[度][HO]'
            }, {
                field: 'HO_dir_diff',
                title: 'DIR方向差[度][HO]'
            }, {
                field: 'HO_Invalid_ECGI',
                title: 'ECGI是否失效[0 为有效][HO]'
            }, {
                field: 'HO_chk_dir',
                title: 'DIR方向差检查[HO]'
            }, {
                field: 'MRO_cnt_Sample',
                title: '所选邻宏小区个数[MRO]'
            }, {
                field: 'MRO_sum_Sample',
                title: '所选邻宏小区之总次数[MRO]'
            }, {
                field: 'MRO_N_ECGI_List',
                title: '所选邻宏小区（ 次数）[MRO]'
            }, {
                field: 'MRO_dir_deg',
                title: '加权平均DIR[度][MRO]'
            }, {
                field: 'MRO_dir_diff',
                title: 'DIR方向差[度][MRO]'
            }, {
                field: 'MRO_Invalid_ECGI',
                title: 'ECGI是否失效[0 为有效][MRO]'
            }, {
                field: 'MRO_chk_dir',
                title: 'DIR方向差检查[MRO]'
            }, {
                field: 'city',
                title: 'city'
            }];
            break;
        case 'lon_lat_check':
            fieldArr = [{
                field: 'ECGI',
                title: 'ECGI'
            }, {
                field: 'ECELL',
                title: 'ECELL'
            }, {
                field: 'ECI',
                title: 'ECI'
            }, {
                field: 'cellNameChinese',
                title: 'cellNameChinese'
            }, {
                field: 'siteName',
                title: 'siteName'
            }, {
                field: 'siteNameChinese',
                title: 'siteNameChinese'
            }, {
                field: 'duplexMode',
                title: 'duplexMode'
            }, {
                field: 'cluster',
                title: 'cluster'
            }, {
                field: 'subNetwork',
                title: 'subNetwork'
            }, {
                field: 'currentOSS',
                title: 'currentOSS'
            }, {
                field: 'cellType',
                title: 'cellType'
            }, {
                field: 'x0',
                title: 'Lon'
            }, {
                field: 'y0',
                title: 'Lat'
            }, {
                field: 'Result_Type',
                title: '结果[MRO / HO]'
            }, {
                field: 'Result_cnt_Sample',
                title: '所选邻宏小区个数[MRO / HO]'
            }, {
                field: 'Result_sum_Sample',
                title: '所选邻宏小区之总次数[MRO / HO]'
            }, {
                field: 'Result_x_avg',
                title: '加权平均Lon[MRO / HO]'
            }, {
                field: 'Result_y_avg',
                title: '加权平均Lat[MRO / HO]'
            }, {
                field: 'Result_dist_km',
                title: '距离[千米][MRO / HO]'
            }, {
                field: 'HO_cnt_Sample',
                title: '所选邻宏小区个数[HO]'
            }, {
                field: 'HO_sum_Sample',
                title: '所选邻宏小区之总次数[HO]'
            }, {
                field: 'HO_N_ECGI_List',
                title: '所选邻宏小区（ 次数）[HO]'
            }, {
                field: 'HO_x_avg',
                title: '加权平均Lon[HO]'
            }, {
                field: 'HO_y_avg',
                title: '加权平均Lat[HO]'
            }, {
                field: 'HO_dist_km',
                title: '距离[千米][HO]'
            }, {
                field: 'HO_Invalid_ECGI',
                title: 'ECGI是否失效[0 为有效][HO]'
            }, {
                field: 'HO_Invalid_N_ECGI',
                title: '加权平均Lon/Lat是否失效[0为有效][HO]'
            }, {
                field: 'HO_noxy_N_ECGI',
                title: '有否所选邻宏小区缺失Lon/Lat值[空为有效][HO]'
            }, {
                field: 'HO_chk_dist',
                title: '距离检查[HO]'
            }, {
                field: 'MRO_cnt_Sample',
                title: '所选邻宏小区个数[MRO]'
            }, {
                field: 'MRO_sum_Sample',
                title: '所选邻宏小区之总次数[MRO]'
            }, {
                field: 'MRO_N_ECGI_List',
                title: '所选邻宏小区（ 次数）[MRO]'
            }, {
                field: 'MRO_x_avg',
                title: '加权平均Lon[MRO]'
            }, {
                field: 'MRO_y_avg',
                title: '加权平均Lat[MRO]'
            }, {
                field: 'MRO_dist_km',
                title: '距离[千米][MRO]'
            }, {
                field: 'MRO_Invalid_ECGI',
                title: 'ECGI是否失效[0 为有效][MRO]'
            }, {
                field: 'MRO_Invalid_N_ECGI',
                title: '加权平均Lon/Lat是否失效[0为有效][MRO]'
            }, {
                field: 'MRO_noxy_N_ECGI',
                title: '有否所选邻宏小区缺失Lon/Lat值[空为有效][MRO]'
            }, {
                field: 'MRO_chk_dist',
                title: '距离检查[MRO]'
            }, {
                field: 'city',
                title: 'city'
            }];
            break;
    }
    return fieldArr;
}