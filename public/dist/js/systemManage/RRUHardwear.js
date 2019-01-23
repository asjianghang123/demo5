$(document).ready(function() {
    toogle("RRUHardwear");
    setTime();
    initSubnetwork();
});

function setTime() {
    $("#date").datepicker({
        format: "yyyy-mm-dd"
    }); //返回日期
    var nowTemp = new Date();
    $.post("RRUHardwear/getDate", null, function(data) {
        data = JSON.parse(data);
        $("#date").datepicker("setValues", data);
    });
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var checkin = $("#date").datepicker({
        onRender: function(date) {
            return date.valueOf() < now.valueOf() ? "" : "";
        }
    }).on("changeDate", function(ev) {
        checkin.hide();
    }).data("datepicker");
}

function initSubnetwork() {
    $('#subNetwork').multiselect('destroy');
    $('#subNetwork').multiselect({
        dropRight: true,
        buttonWidth: "100%",
        enableFiltering: true,
        nonSelectedText: "请选择子网",
        filterPlaceholder: "搜索",
        nSelectedText: "个子网",
        includeSelectAllOption: true,
        selectAllText: "全选/取消全选",
        allSelectedText: "全部子网",
        maxHeight: 200,
        maxWidth: "100%"
    });
    $.post("RRUHardwear/getSubnetwork", null, function(data) {
        var options = [];
        for (var i in data) {
            options.push({
                label: data[i].subNetwork,
                value: data[i].subNetwork,
            });
        }
        $('#subNetwork').multiselect('dataprovider', options);
    });
}

function query() {
    var date = $("#date").val();
    var rulogicalid = $("#rulogicalid").val();
    var ruserialno = $("#ruserialno").val();
    var productName = $("#productName").val();
    var serialNumber = $("#serialNumber").val();
    var ecgi = $("#ecgi").val();
    var cell_2g = $("#cell_2g").val();
    var bsc = $("#bsc").val();
    var subNetwork = $("#subNetwork").val();
    if (!date && !rulogicalid && !ruserialno && !productName && !serialNumber && !ecgi && !cell_2g && !bsc && !subNetwork) {
        layer.open({
            title: "提示",
            content: "请至少给出一个搜索条件",
            fixed: false
        });
        return;
    }
    var params = {
        date: date,
        rulogicalid: rulogicalid,
        ruserialno: ruserialno,
        productName: productName,
        serialNumber: serialNumber,
        ecgi: ecgi,
        cell_2g: cell_2g,
        bsc: bsc,
        subNetwork: subNetwork
    };
    var columns = [{
        field: 'date_id',
        title: '日期'
    }, {
        field: 'rulogicalid',
        title: '2G RU型号'
    }, {
        field: 'ruserialno',
        title: '2G串号'
    }, {
        field: 'productName',
        title: '4G RU型号'
    }, {
        field: 'serialNumber',
        title: '4G串号'
    }, {
        field: 'ecgi',
        title: '4G ECGI'
    }, {
        field: 'cell_2g',
        title: '2G cell ID'
    }, {
        field: 'bsc',
        title: 'bsc'
    }, {
        field: 'subNetwork',
        title: 'subNetwork'
    }];
    var Q = Ladda.create(document.getElementById("queryBtn"));
    Q.start();
    $("#RRUHardwearTable").bootstrapTable("destroy");
    $("#RRUHardwearTable").bootstrapTable({
        url: "RRUHardwear/searchData",
        search: false,
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
        columns: columns,
        onLoadSuccess: function() { //加载成功时执行 
            Q.stop();
        },
        onLoadError: function() { //加载失败时执行 
            layer.open({
                title: "提示",
                content: "查询失败，请重试"
            });
            Q.stop();
        }
    });
}

function exportData() {
    var date = $("#date").val();
    var rulogicalid = $("#rulogicalid").val();
    var ruserialno = $("#ruserialno").val();
    var productName = $("#productName").val();
    var serialNumber = $("#serialNumber").val();
    var ecgi = $("#ecgi").val();
    var cell_2g = $("#cell_2g").val();
    var bsc = $("#bsc").val();
    var subNetwork = $("#subNetwork").val();
    if (!date && !rulogicalid && !ruserialno && !productName && !serialNumber && !ecgi && !cell_2g && !bsc && !subNetwork) {
        layer.open({
            title: "提示",
            content: "请至少给出一个搜索条件",
            fixed: false
        });
        return;
    }
    var params = {
        date: date,
        rulogicalid: rulogicalid,
        ruserialno: ruserialno,
        productName: productName,
        serialNumber: serialNumber,
        ecgi: ecgi,
        cell_2g: cell_2g,
        bsc: bsc,
        subNetwork: subNetwork
    };
    var E = Ladda.create(document.getElementById("exportBtn"));
    E.start();

    $.post("RRUHardwear/downloadFile", params, function(data) {
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