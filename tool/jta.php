<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <script src="../js/jquery-1.9.0.min.js"></script>
</head>
<body>
<div id="container" style="width: 100%">
    <form action="http://www.try.com/tool/show.php" method="post" target="_blank">
        <textarea id = "json_str" style="height: 200px;width:100%" name="str"></textarea>
        <div>
            <input id = "test" style="display: inline-block;width: 49.5%"  type="submit" value ="提交">
            <input style="display: inline-block;width: 49.5%"  type="reset"  value ="重置">
        </div>
    </form>
</div>
<!--<script>
    $("#test").click(function () {
        var str = $("#json_str").val();
        $.ajax({
            type: "POST",
            url:'http://www.try.com/tool/jta.php',
            dataType:'json',
            data:{'str':str},
            success:function (data) {
                console.log(data);
            }
        })
    })
</script>-->
</body>
</html>


