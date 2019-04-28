<?php
$dbhost = 'localhost:3306';  // mysql服务器主机地址
$dbuser = 'root';            // mysql用户名
$dbpass = 'yc_dfs168';          // mysql用户名密码
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
    die('连接失败: ' . mysqli_error($conn));
}
// 设置编码，防止中文乱码
mysqli_query($conn , "set names utf8");

mysqli_select_db( $conn, 'test' );

$sql = 'SELECT * FROM test_topic';

$retval = mysqli_query( $conn, $sql );
if(! $retval )
{
    die('无法读取数据: ' . mysqli_error($conn));
}
echo '<h2>mysql测试<h2>';
echo '<table border="1"><tr><td>ID</td><td>标题</td></tr>';
while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC))
{
    echo "<tr><td> {$row['id']}</td> ".
        "<td>{$row['title']} </td> ".
        "</tr>";
}
echo '</table>';
mysqli_close($conn);
?>


alter table runoob_tbl partition by range columns (runoob_id) (
partition p0 values less than (5),
partition p1 values less than (10),
partition p2 values less than (maxvalue)
);

