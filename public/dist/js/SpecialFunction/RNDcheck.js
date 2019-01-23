$(document).ready(function () {
	// toogle("operationQuery");
    setTree();
	initCitys();

    initValidata_mode();

});
var records;
function setTree() {
    $.get("RNDcheck/getCitiesTree", null, function (data) {
        // storageQueryTreeData = eval("(" + data + ")");
        console.log(data);
        var options = {
            bootstrap2: false,
            showTags: true,
            levels: 2,
            data: data,
            onNodeSelected: function (event, data) {
                $("#storageFlag").val(data.text);
                doQueryTask(data.text);
            }
        };
        $("#storageQueryTree").treeview(options);
        
    });
}

function doQueryTask(city){



            $columns ="taskName,status,startTime,endTime,owner,createTime";
    console.log(city);
    console.log($("#storageQueryTree").treeview("getSelected"))
    $.post("RNDcheck/getTaskLog",{data:city},function(data){
        var data = JSON.parse(data);
        var fieldArr = [];
        var fieldStr = "taskName,status,startTime,endTime,owner,createTime";
        var text = fieldStr.split(",");
        for (var i in text) {
            fieldArr[fieldArr.length] = {field: text[fieldArr.length], title: text[fieldArr.length], width: 200};
        }
        var newData = data.rows;
        $("#RndCheckTaskTable").grid("destroy", true, true);
      var grid=$("#RndCheckTaskTable").grid({
            columns: fieldArr,
            dataSource: newData,
            pager: {limit: 10, sizes: [10, 20, 50, 100]},
            autoScroll: true,
            uiLibrary: "bootstrap",
            primaryKey: "id"
        });
        grid.on("rowSelect", function (e, $row, id, record) {
			records=record;
		});
    })
}
function initCitys() {
    $('#city').multiselect({
        dropRight: true,
        buttonWidth: '100%',
        //enableFiltering: true,
        nonSelectedText: '请选择城市',
        //filterPlaceholder:'搜索',
        nSelectedText: '项被选中',
        includeSelectAllOption: true,
        selectAllText: '全选/取消全选',
        allSelectedText: '已选中所有平台类型',
        maxHeight: 200,
        width: 220
    });
    var url = "RNDcheck/getCitys";
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (data) {
            var newOptions = [];
            var obj = {};
            $(data).each(function (k, v) {
                v = eval("(" + v + ")");
                obj = {
                    label: v.text,
                    value: v.value
                };
                newOptions.push(obj);
            });
            $('#city').multiselect('dataprovider', newOptions);
        }
    });
}

function addRndCheckTask(){
    $("#addRndCheckTask").modal();
    $("#add_mode input").val("");
    $("#taskForm").data("bootstrapValidator").destroy();
    initValidata_mode();
    
}
function addTask(){
    console.log($("#cellsType").prop("checked"));
    $("#taskForm").data("bootstrapValidator").validate();
    var flag = $("#taskForm").data("bootstrapValidator").isValid();
    console.log(flag)
    if (!flag) {
        return;
    }
    if($("#fileImportName").val()==''){
        layer.open({
                title: "提示",
                content: "请选择要上传的文件"
                });
        return;
    }
    var params = {
        "city":$("#city").val(),
        "taskName":$("#taskName").val(),
        "taskType":$("#cellsType").prop("checked")?"FDD":"TDD",
        "filename": $("#fileImportName")
    }

    $.ajaxFileUpload({
        url :"RNDcheck/addTask",
        type : "POST",
        dataType: "json",
        data:params,
        fileElementId :"fileImport",
        success:function(data){
            data = JSON.parse(data);
            if(data.error){
               layer.open({
                    title:"提示",
                    content:data.error+",请重新插入"
                });
            }else{
                layer.open({
                    title:"提示",
                    content:"上传成功"
                });
                doQueryTask($("#storageQueryTree").treeview("getSelected")[0].text);
                $("#addRndCheckTask").modal('hide');
                console.log($("#storageQueryTree").treeview("getSelected")[0].text)
            }
         
        },
        error: function (data, status, e) {
            //alert("上传失败");
            // E.stop();
            
            // layer.open({
            //     title:"提示",
            //     content:"插入成功"
            // });
        }

    })





    // $.post("RNDcheck/addTask",params,function(data){
    //     console.log(JSON.parse(data))
    //     var data = JSON.parse(data);
    //     if(data.error){
    //         layer.open({
    //             title:"提示",
    //             content:data.error
    //         })
    //         return;
    //     }else{
    //     $("#addRndCheckTask").modal("hide");
    //         layer.open({
    //             title:"提示",
    //             content:"新建任务成功"
    //         })
    //         doQueryTask($("#storageFlag").val());

    //     }
    // })

    console.log(params);
}
function initValidata_mode() {
    $("#taskForm").bootstrapValidator({
        message: "This value is not valid",
        feedbackIcons: {
            valid: "glyphicon glyphicon-ok",
            invalid: "glyphicon glyphicon-remove",
            validating: "glyphicon glyphicon-refresh"
        },
        fields: {
            taskName: {
                //message: "用户名验证失败",
                validators: {
                    notEmpty: {
                        message: "任务名称不能为空"
                    }
                }
            },
            city:{
                 validators: {
                    notEmpty: {
                        message: "城市名称不能为空"
                    }
                }
            }
        }
    });
}

function importTemplate(){
    $("#import_modal").modal();
    $("#fileImportName").val("");
    $("#fileImport").val("");
}
function toName(self) {
    $("#fileImportName").val(self.value);
}

function importFile(){
    if($("#fileImportName").val()==''){
        layer.open({
                        title: "提示",
                        content: "请选择要上传的文件"
                });
        return;
    }
    console.log($("#city").val());
    var data = {
        "filename": $("#fileImportName"),
        "city":$("#city").val()
    };
    $.ajaxFileUpload({
        url :"RNDcheck/uploadFile",
        type : "POST",
        dataType: "json",
        data:data,
        fileElementId :"fileImport",
        success:function(data){
            data = JSON.parse(data);
            if(data.error){
               layer.open({
                    title:"提示",
                    content:data.error+",请重新插入"
                });
            }else{
                layer.open({
                    title:"提示",
                    content:"插入成功"
                });
            }
         
        },
        error: function (data, status, e) {
            //alert("上传失败");
            // E.stop();
            
            // layer.open({
            //     title:"提示",
            //     content:"插入成功"
            // });
        }

    })

}

function runRndCheckTask(){

    // var selectedData = $("#RndCheckTaskTable").grid("getById", $("#RndCheckTaskTable").grid("getSelected"));
    // console.log($("#RndCheckTaskTable").grid("getSelected"));
    var selectedData = $("#RndCheckTaskTable").grid("getSelected");
    var user = $("#user_user").html();
    console.log(user);
    if(!selectedData||!records){
        layer.open({
            title:"提示",
            content:"请选择要执行的任务"
        });
        return;
    }
    if (user == "admin" || user == records.owner) {
        
        if(records.status!="prepare"){
             layer.open({
                title:"提示",
                content:"任务正在执行或已经执行完成"
            });
            return;
        }

            var taskName = decodeURIComponent(records.taskName);
            var owner    = decodeURIComponent(records.owner);
            var taskId   = decodeURIComponent(records.id);
            var type   = decodeURIComponent(records.type);
            var myDate = getNewDate();
            var params = {
                "taskName": taskName,
                "owner":owner,
                "taskId":taskId,
                "type":type,
                "startTime":myDate
            };
            console.log(myDate);
            var l = Ladda.create(document.getElementById('runRndCheckTask'));
            l.start();
            $("#RndCheckTaskTable tr.active td").eq(1).children("div").html("ongoing");
            $("#RndCheckTaskTable tr.active td").eq(2).children("div").html(myDate);
            $.post("RNDcheck/runRndCheckTask",params,function(data){
                l.stop();
                layer.open({
                    title:"提示",
                    content:"执行成功"
                });
                doQueryTask($("#storageQueryTree").treeview("getSelected")[0].id);

            })
                console.log($("#storageQueryTree").treeview("getSelected")[0].id)
        }else{
                layer.open({
                title: "提示",
                content: "没有权限启动该任务"
            });
                l.stop();
            return;
        }





}


function runTask()
{       
    var data = {
        "city":$("#city").val()
    };
    var E = Ladda.create(document.getElementById("runTask"));
    E.start();
    $.post("RNDcheck/runProcedure",data,function(data){
        E.stop();
          layer.open({
            title :"提示",
            content:data
          })
    })
}
function exportRndCheckTask(){

    var selectedData =  $("#RndCheckTaskTable").grid("getSelected");
    var user = $("#user_user").html();
    console.log(user);
    if(!selectedData||!records){
        layer.open({
            title:"提示",
            content:"请选择要导出的任务"
        });
        return;
    }

    if(records.status=="complete"){
             var taskName = decodeURIComponent(records.taskName);
            var taskId   = decodeURIComponent(records.id);
            var type   = decodeURIComponent(records.type);
            var params = {
                "taskName": taskName,
                "taskId":taskId,
                "type":type
            };
            var E = Ladda.create(document.getElementById("exportBtn"));
            E.start();
            $.post("RNDcheck/export",params,function(data){
                console.log(data);
                if(data){
                      // var name=JSON.parse(data);
               fileDownload(JSON.parse(data));
               E.stop();
                    // var filepath = name.filename.replace("\\", "");
                    // download(filepath, "", "data:text/csv;charset=utf-8");
                }else{
                     layer.open({
                        title :"提示",
                        content:"数据库不存在"
                      })
                }
              
            })
    }else{
        layer.open({
            title:"提示",
            content:"任务没有执行完成"
        })
    }
   

}

// 


//下载模板
function downTemplate()
{
    $("#template").modal();
}

function exportTemplate(type){
 var data = {
     "type":type
 };

 $.post("RNDcheck/exportTemplate",data,function(data){
     if(data){
              var name=JSON.parse(data);
        // fileDownload(JSON.parse(data).filename);
            var filepath = name.filename.replace("\\", "");
            download(filepath, "", "data:text/csv;charset=utf-8");
        }else{
             layer.open({
                title :"提示",
                content:"下载模版失败"
              })
        }


 })


}


function getNewDate() {
    var date = new Date();
    var year = date.getFullYear();
    var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
    var day = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
    var hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
    var minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
    var second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
    var mydate = year + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + second;
    return mydate;
}

function deleteTask()
{
    var selectedData = $("#RndCheckTaskTable").grid("getSelected");

    if(!selectedData||!records){
        layer.open({
            title:"提示",
            content:"请选择要删除的任务"
        });
        return;
    }
    console.log(records);

    if(records.status=="ongoing"){
         layer.open({
            title:"提示",
            content:"不能删除正在运行的任务"
        });
        return;
    }
    $.ajax({ url:"RNDcheck/deleteTask",type:"POST",data:records,
       success:function(data){
                layer.open({
                title:"提示",
                content:"删除成功"
            });
            doQueryTask(records.city);
            return;

        }
    })

}