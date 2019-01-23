$(function() {
    toogle("workingParameterDataAnalysis");
    initOptions();
});

function initOptions() {
    initActions();
    initDimensions();
    initDate();
    initCity();
    // 先全部只读
    $("#allCity").multiselect('disable');
    $("#erbsInput").attr("readonly", "readonly");
    $("#cellInput").attr("readonly", "readonly");
    $('#fileImport').next().children().attr("disabled", "disabled");
    $("#keyWord").attr("readonly", "readonly");

    //再根据选择的维度控制是否可以输入
    $("#dimensions").change(function() {
        var value = $("#dimensions").val();
        switch (value) {
            case 'all':
                $("#allCity").multiselect('disable');
                $("#erbsInput").attr("readonly", "readonly");
                $("#cellInput").attr("readonly", "readonly");
                $('#fileImport').next().children().attr("disabled", "disabled");
                $("#keyWord").attr("readonly", "readonly");
                break;
            case 'city':
                $("#allCity").multiselect('enable');
                $("#erbsInput").attr("readonly", "readonly");
                $("#cellInput").attr("readonly", "readonly");
                $('#fileImport').next().children().attr("disabled", "disabled");
                $("#keyWord").attr("readonly", "readonly");
                break;
            case 'station':
                $("#allCity").multiselect('disable');
                $("#erbsInput").removeAttr("readonly");
                $("#cellInput").attr("readonly", "readonly");
                $('#fileImport').next().children().attr("disabled", "disabled");
                $("#keyWord").attr("readonly", "readonly");
                break;
            case 'cell':
                $("#allCity").multiselect('disable');
                $("#cellInput").removeAttr("readonly");
                $('#fileImport').next().children().removeAttr("disabled");
                $("#erbsInput").attr("readonly", "readonly");
                $("#keyWord").attr("readonly", "readonly");
                break;
            case 'keyWord':
                $("#allCity").multiselect('disable');
                $("#erbsInput").attr("readonly", "readonly");
                $("#cellInput").attr("readonly", "readonly");
                $('#fileImport').next().children().attr("disabled", "disabled");
                $("#keyWord").removeAttr("readonly");
                break;
            default:
                $("#allCity").multiselect('disable');
                $("#erbsInput").attr("readonly", "readonly");
                $("#cellInput").attr("readonly", "readonly");
                $('#fileImport').next().children().attr("disabled", "disabled");
                $("#keyWord").attr("readonly", "readonly");
                break;
        }
    });

}

function initActions() {
    $("#actions").multiselect({
        dropRight: true,
        buttonWidth: "100%",
        //enableFiltering: true,
        // nonSelectedText: "请选择功能",
        //filterPlaceholder:'搜索',
        // nSelectedText: "项被选中",
        // includeSelectAllOption: true,
        // selectAllText: "全选/取消全选",
        // allSelectedText: "已选中所有功能",
        maxHeight: 200,
        width: 220
    });
}

function initDimensions() {
    $("#dimensions").multiselect({
        dropRight: true,
        buttonWidth: "100%",
        //enableFiltering: true,
        // nonSelectedText: "请选择维度",
        //filterPlaceholder:'搜索',
        // nSelectedText: "项被选中",
        // includeSelectAllOption: true,
        // selectAllText: "全选/取消全选",
        // allSelectedText: "已选中所有功能",
        maxHeight: 200,
        width: 220
    });
}

function initDate() {
    $("#date").datepicker({
        format: 'yyyy-mm-dd'
    }); //返回日期
    var nowTemp = new Date();
    var year = nowTemp.getFullYear();
    var month = nowTemp.getMonth() + 1;
    var day = nowTemp.getDate();
    var today = year + '-' + month + '-' + day;

    var params = {
        action: $("#actions").val()
    };
    $.get('workingParameter/getDate', params, function(data) {
        var sdata = [];
        for (var i = 0; i < data.length; i++) {
            if (data[i] === today) {
                continue;
            }
            sdata.push(data[i]);
        }
        sdata.push(today);
        $("#date").datepicker('setValues', sdata);
    });
    // $('#actions').change(function() {
    //     var params = {
    //         action: $(this).val()
    //     };
    //     $.get('workingParameter/getDate', params, function(data) {
    //         var sdata = [];
    //         for (var i = 0; i < data.length; i++) {
    //             if (data[i] === today) {
    //                 continue;
    //             }
    //             sdata.push(data[i]);
    //         }
    //         sdata.push(today);
    //         $("#date").datepicker('setValues', sdata);
    //     });
    // });

    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var checkin = $('#date').datepicker({
        onRender: function(date) {
            return date.valueOf() < now.valueOf() ? '' : '';
        }
    }).on('changeDate', function(ev) {
        checkin.hide();
    }).data('datepicker');
}

function initCity() {
    $("#allCity").multiselect({
        dropRight: true,
        buttonWidth: "100%",
        //enableFiltering: true,
        nonSelectedText: "请选择城市",
        //filterPlaceholder:"搜索",
        nSelectedText: "个城市被选中",
        includeSelectAllOption: true,
        selectAllText: "全选/取消全选",
        allSelectedText: "已选中所有城市",
        maxHeight: 200,
        maxWidth: "100%"
    });
    var url = "workingParameter/getAllCity";
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function(data) {
            var newOptions = [];
            var obj = {};
            $(data).each(function(k, v) {
                v = eval("(" + v + ")");
                obj = {
                    label: v.text,
                    value: v.value
                };
                newOptions.push(obj);
            });
            $("#allCity").multiselect("dataprovider", newOptions);
            $("#allCity").multiselect('disable');
        }
    });
}

//导入小区
function toName(self) {
    $.ajaxFileUpload({
        url: "workingParameter/uploadFile",
        //data : data,
        fileElementId: "fileImport",
        secureuri: false,
        dataType: "json",
        type: "post",
        success: function(data, status) {
            $("#cellInput").val(data);
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

function getParams() {
    var action = $("#actions").val();
    var dimension = $("#dimensions").val();
    var date = $("#date").val();
    var city = $("#allCity").val();
    var station = $("#erbsInput").val();
    var cells = $("#cellInput").val();
    var keyWord = $("#keyWord").val();

    if (!date) {
        layer.open({
            title: "提示",
            content: "请选择日期"
        });
        return false;
    }
    if (dimension == 'city' && !city) {
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return false;
    }
    if (dimension == 'station' && station == '') {
        layer.open({
            title: "提示",
            content: "请输入基站"
        });
        return false;
    }
    if (dimension == 'cell' && cells == '') {
        layer.open({
            title: "提示",
            content: "请输入小区"
        });
        return false;
    }
    if (dimension == 'keyWord' && keyWord == '') {
        layer.open({
            title: "提示",
            content: "请输入关键词"
        });
        return false;
    }
    return {
        action: action,
        dimension: dimension,
        date: date,
        city: city,
        station: station,
        cells: cells,
        keyWord: keyWord
    };
}

function getFieldArr(action) {
    var fieldArr = [];
    switch (action) {
        case '1':
            fieldArr = [{
                field: 'ECGI',
                title: 'ECGI',
                width: 200
            }, {
                field: 'ECELL',
                title: 'ECELL',
                width: 200
            }, {
                field: 'SITE',
                title: 'SITE',
                width: 200
            }, {
                field: 'ECI',
                title: 'ECI',
                width: 200
            }, {
                field: 'cellNameChinese',
                title: 'cellNameChinese',
                width: 200
            }, {
                field: 'siteNameChinese',
                title: 'siteNameChinese',
                width: 200
            }, {
                field: 'duplexMode',
                title: 'duplexMode',
                width: 200
            }, {
                field: 'cluster',
                title: 'cluster',
                width: 200
            }, {
                field: 'x1',
                title: 'Lon',
                width: 200
            }, {
                field: 'y1',
                title: 'Lat',
                width: 200
            }, {
                field: 'dir1',
                title: 'DIR',
                width: 200
            }, {
                field: 'n_ECGI',
                title: '邻小区ECGI',
                width: 200
            }, {
                field: 'n_ECELL',
                title: '邻小区ECELL',
                width: 200
            }, {
                field: 'n_SITE',
                title: '邻小区SITE',
                width: 200
            }, {
                field: 'n_ECI',
                title: '邻小区ECI',
                width: 200
            }, {
                field: 'n_cellNameChinese',
                title: '邻小区cellNameChinese',
                width: 200
            }, {
                field: 'n_siteNameChinese',
                title: '邻小区siteNameChinese',
                width: 200
            }, {
                field: 'n_cluster',
                title: '邻小区cluster',
                width: 200
            }, {
                field: 'x2',
                title: '邻小区Lon',
                width: 200
            }, {
                field: 'y2',
                title: '邻小区Lat',
                width: 200
            }, {
                field: 'dir2',
                title: '邻小区DIR',
                width: 200
            }, {
                field: 'earfcn',
                title: 'EARFCN	',
                width: 200
            }, {
                field: 'dist_km',
                title: '距离[千米]',
                width: 200
            }];
            break;
        case '2':
            fieldArr = [{
                field: 'ECGI',
                title: 'ECGI',
                width: 200
            }, {
                field: 'ECELL',
                title: 'ECELL',
                width: 200
            }, {
                field: 'SITE',
                title: 'SITE',
                width: 200
            }, {
                field: 'ECI',
                title: 'ECI',
                width: 200
            }, {
                field: 'cellNameChinese',
                title: 'cellNameChinese',
                width: 200
            }, {
                field: 'siteNameChinese',
                title: 'siteNameChinese',
                width: 200
            }, {
                field: 'duplexMode',
                title: 'duplexMode',
                width: 200
            }, {
                field: 'cluster',
                title: 'cluster',
                width: 200
            }, {
                field: 'x1',
                title: 'Lon',
                width: 200
            }, {
                field: 'y1',
                title: 'Lat',
                width: 200
            }, {
                field: 'dir1',
                title: 'DIR',
                width: 200
            }, {
                field: 'n_ECGI',
                title: '邻小区ECGI',
                width: 200
            }, {
                field: 'n_ECELL',
                title: '邻小区ECELL',
                width: 200
            }, {
                field: 'n_SITE',
                title: '邻小区SITE',
                width: 200
            }, {
                field: 'x2',
                title: '邻小区Lon',
                width: 200
            }, {
                field: 'y2',
                title: '邻小区Lat',
                width: 200
            }, {
                field: 'dir2',
                title: '邻小区DIR',
                width: 200
            }, {
                field: 'earfcn',
                title: 'EARFCN	',
                width: 200
            }, {
                field: 'dist_km',
                title: '距离[千米]',
                width: 200
            }, {
                field: 'dir_diff',
                title: 'DIR方向差[度]',
                width: 200
            }];
            break;
        case '3':
            fieldArr = [{
                field: 'ECGI',
                title: 'ECGI',
                width: 200
            }, {
                field: 'ECELL',
                title: 'ECELL',
                width: 200
            }, {
                field: 'ECI',
                title: 'ECI',
                width: 200
            }, {
                field: 'cellNameChinese',
                title: 'cellNameChinese',
                width: 200
            }, {
                field: 'siteName',
                title: 'siteName',
                width: 200
            }, {
                field: 'siteNameChinese',
                title: 'siteNameChinese',
                width: 200
            }, {
                field: 'duplexMode',
                title: 'duplexMode',
                width: 200
            }, {
                field: 'cluster',
                title: 'cluster',
                width: 200
            }, {
                field: 'subNetwork',
                title: 'subNetwork',
                width: 200
            }, {
                field: 'currentOSS',
                title: 'currentOSS',
                width: 200
            }, {
                field: 'cellType',
                title: 'cellType',
                width: 200
            }, {
                field: 'dir0',
                title: 'DIR',
                width: 200
            }, {
                field: 'Result_Type',
                title: '结果[MRO / HO]',
                width: 200
            }, {
                field: 'Result_cnt_Sample',
                title: '所选邻宏小区个数[MRO / HO]',
                width: 200
            }, {
                field: 'Result_sum_Sample',
                title: '所选邻宏小区之总次数[MRO / HO]',
                width: 200
            }, {
                field: 'Result_dir_deg',
                title: '加权平均DIR[度][MRO / HO]',
                width: 200
            }, {
                field: 'Result_dir_diff',
                title: 'DIR方向差[度][MRO / HO]',
                width: 200
            }, {
                field: 'Result_chk_dir',
                title: 'DIR方向差检查[MRO / HO]',
                width: 200
            }, {
                field: 'HO_cnt_Sample',
                title: '所选邻宏小区个数[HO]',
                width: 200
            }, {
                field: 'HO_sum_Sample',
                title: '所选邻宏小区之总次数[HO]',
                width: 200
            }, {
                field: 'HO_N_ECGI_List',
                title: '所选邻宏小区（ 次数）[HO]',
                width: 400
            }, {
                field: 'HO_dir_deg',
                title: '加权平均DIR[度][HO]',
                width: 200
            }, {
                field: 'HO_dir_diff',
                title: 'DIR方向差[度][HO]',
                width: 200
            }, {
                field: 'HO_Invalid_ECGI',
                title: 'ECGI是否失效[0 为有效][HO]',
                width: 200
            }, {
                field: 'HO_chk_dir',
                title: 'DIR方向差检查[HO]',
                width: 200
            }, {
                field: 'MRO_cnt_Sample',
                title: '所选邻宏小区个数[MRO]',
                width: 200
            }, {
                field: 'MRO_sum_Sample',
                title: '所选邻宏小区之总次数[MRO]',
                width: 200
            }, {
                field: 'MRO_N_ECGI_List',
                title: '所选邻宏小区（ 次数）[MRO]',
                width: 400
            }, {
                field: 'MRO_dir_deg',
                title: '加权平均DIR[度][MRO]',
                width: 200
            }, {
                field: 'MRO_dir_diff',
                title: 'DIR方向差[度][MRO]',
                width: 200
            }, {
                field: 'MRO_Invalid_ECGI',
                title: 'ECGI是否失效[0 为有效][MRO]',
                width: 200
            }, {
                field: 'MRO_chk_dir',
                title: 'DIR方向差检查[MRO]',
                width: 200
            }];
            break;
        case '4':
            fieldArr = [{
                field: 'ECGI',
                title: 'ECGI',
                width: 200
            }, {
                field: 'ECELL',
                title: 'ECELL',
                width: 200
            }, {
                field: 'ECI',
                title: 'ECI',
                width: 200
            }, {
                field: 'cellNameChinese',
                title: 'cellNameChinese',
                width: 200
            }, {
                field: 'siteName',
                title: 'siteName',
                width: 200
            }, {
                field: 'siteNameChinese',
                title: 'siteNameChinese',
                width: 200
            }, {
                field: 'duplexMode',
                title: 'duplexMode',
                width: 200
            }, {
                field: 'cluster',
                title: 'cluster',
                width: 200
            }, {
                field: 'subNetwork',
                title: 'subNetwork',
                width: 200
            }, {
                field: 'currentOSS',
                title: 'currentOSS',
                width: 200
            }, {
                field: 'cellType',
                title: 'cellType',
                width: 200
            }, {
                field: 'x0',
                title: 'Lon',
                width: 200
            }, {
                field: 'y0',
                title: 'Lat',
                width: 200
            }, {
                field: 'Result_Type',
                title: '结果[MRO / HO]',
                width: 200
            }, {
                field: 'Result_cnt_Sample',
                title: '所选邻宏小区个数[MRO / HO]',
                width: 200
            }, {
                field: 'Result_sum_Sample',
                title: '所选邻宏小区之总次数[MRO / HO]',
                width: 200
            }, {
                field: 'Result_x_avg',
                title: '加权平均Lon[MRO / HO]',
                width: 200
            }, {
                field: 'Result_y_avg',
                title: '加权平均Lat[MRO / HO]',
                width: 200
            }, {
                field: 'Result_dist_km',
                title: '距离[千米][MRO / HO]',
                width: 200
            }, {
                field: 'HO_cnt_Sample',
                title: '所选邻宏小区个数[HO]',
                width: 200
            }, {
                field: 'HO_sum_Sample',
                title: '所选邻宏小区之总次数[HO]',
                width: 200
            }, {
                field: 'HO_N_ECGI_List',
                title: '所选邻宏小区（ 次数）[HO]',
                width: 400
            }, {
                field: 'HO_x_avg',
                title: '加权平均Lon[HO]',
                width: 200
            }, {
                field: 'HO_y_avg',
                title: '加权平均Lat[HO]',
                width: 200
            }, {
                field: 'HO_dist_km',
                title: '距离[千米][HO]',
                width: 200
            }, {
                field: 'HO_Invalid_ECGI',
                title: 'ECGI是否失效[0 为有效][HO]',
                width: 200
            }, {
                field: 'HO_Invalid_N_ECGI',
                title: '加权平均Lon/Lat是否失效[0为有效][HO]',
                width: 200
            }, {
                field: 'HO_noxy_N_ECGI',
                title: '有否所选邻宏小区缺失Lon/Lat值[空为有效][HO]',
                width: 200
            }, {
                field: 'HO_chk_dist',
                title: '距离检查[HO]',
                width: 200
            }, {
                field: 'MRO_cnt_Sample',
                title: '所选邻宏小区个数[MRO]',
                width: 200
            }, {
                field: 'MRO_sum_Sample',
                title: '所选邻宏小区之总次数[MRO]',
                width: 200
            }, {
                field: 'MRO_N_ECGI_List',
                title: '所选邻宏小区（ 次数）[MRO]',
                width: 400
            }, {
                field: 'MRO_x_avg',
                title: '加权平均Lon[MRO]',
                width: 200
            }, {
                field: 'MRO_y_avg',
                title: '加权平均Lat[MRO]',
                width: 200
            }, {
                field: 'MRO_dist_km',
                title: '距离[千米][MRO]',
                width: 200
            }, {
                field: 'MRO_Invalid_ECGI',
                title: 'ECGI是否失效[0 为有效][MRO]',
                width: 200
            }, {
                field: 'MRO_Invalid_N_ECGI',
                title: '加权平均Lon/Lat是否失效[0为有效][MRO]',
                width: 200
            }, {
                field: 'MRO_noxy_N_ECGI',
                title: '有否所选邻宏小区缺失Lon/Lat值[空为有效][MRO]',
                width: 200
            }, {
                field: 'MRO_chk_dist',
                title: '距离检查[MRO]',
                width: 200
            }];
            break;

    }
    return fieldArr;
}

function query() {
    var params = getParams();
    if (!params) {
        return;
    }
    var fieldArr = getFieldArr(params.action);
    $("#queryDataTable").grid("destroy", true, true);
    var grid = $("#queryDataTable").grid({
        columns: fieldArr,
        dataSource: {
            url: "workingParameter/getTableData",
            success: function(data) {
                data = JSON.parse(data);
                grid.render(data);
            }
        },
        params: params,
        pager: {
            limit: 10,
            sizes: [10, 20, 50, 100]
        },
        autoScroll: true,
        uiLibrary: "bootstrap",
        // primaryKey: "id",
        autoLoad: true
    });
}

function exportFile() {
    var params = getParams();
    if (!params) {
        return;
    }
    var fieldArr = getFieldArr(params.action);
    params.field = fieldArr;
    var url = "workingParameter/exportFile";
    $.post(url, params, function(data) {
        data = JSON.parse(data);
        download(data.filename);
    });
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