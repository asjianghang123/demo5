<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Genius</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="plugins/font-awesome/css/font-awesome.min.css">
    <!-- Styles -->
    <link rel="stylesheet" href="plugins/layui/css/layui.css">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/nav.css">
    <style>
        body {
            font-family: Arial,Microsoft YaHei,微软雅黑,sans-serif;
        }
        .fa-btn {
            margin-right: 6px;
        }
        .content{
            margin-top: 100px;
        }
    </style>
</head>
<body id="app-layout">

    @yield('content')
    <!-- JavaScripts -->
    <script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <!--layui -->
    <script src="plugins/layui/layui.js"></script>
    @yield('scripts')
</body>
</html>
