<?php
/*

//例子1
//以下父进程运行的逻辑代码环境
$a = 'hello';
//说明 $parent_process与$son_process其实是同一个对象  只是所在的环境不同  $parent_process在父进程环境中运行，$son_process在子进程环境中运行
$parent_process = new Swoole\Process(function($son_process)use($a){
    //以下为子进程运行的逻辑代码环境
    sleep(30);
    echo 88888;
});
//启动进程
$parent_process->start();

echo 7777;
swoole_process::wait();

 */


/*

//例子2 父子进程通过管道通信
$a = 'hello';
$parent_process = new Swoole\Process(function($son_process)use($a){
    $son_process->write($a);//子进程向管道内写入数据
});
$parent_process->start();
echo $parent_process->read();//父进程从管道内读取数据
swoole_process::wait();

 */

/*

//例子3 设置管道读写操作的超时时间超时
$a = 'hello';
$parent_process = new Swoole\Process(function($son_process)use($a){
    //sleep(1);
    sleep(3);
    $son_process->write($a);
});
//设置管道读写操作的超时时间
$parent_process->setTimeout(2);
$parent_process->start();

echo $parent_process->read();

swoole_process::wait();

 */


/*
    例子4 父子进程通过协程进行通信
    $process = new Swoole\Process(function ($proc) {
        $socket = $proc->exportSocket();//子进程中创建一个协程对象
        var_dump('son start');
        //var_dump($socket->recv());//接受父进程向子进程发送的消息

        $socket->send("hello parent");//子进程向父进程发送消息
        var_dump("son stop");

    }, false, 1, true);
    $process->start();

    //父进程创建一个协程容器
    Co\run(function() use ($process) {
        $socket = $process->exportSocket(); // 父进程中创建一个协程对象

        var_dump('parent start');
        //$socket->send("hello son"); //父进程向子进程发送消息

        var_dump($socket->recv());  //接受子进程向父进程发送的消息
        var_dump('parent stop');
    });
    //在父进程中回收结束运行的子进程
    Swoole\Process::wait(true);

 */


/*

//例子5 父子进程通过中黏包问题的测试
$process = new Swoole\Process(function ($proc) {
    $socket = $proc->exportSocket();
    //子进程当中 间隔1000毫秒向 父进程发送消息
    Swoole\Timer::tick(1000, function () use ($socket) {
        $socket->send("hello parent".time());
    });
}, false, 2, true); //参数create_pipe为2不会产生黏包问题; 当为1时可能产生黏包问题
$process->start();


//父进程创建一个协程容器
Co\run(function() use ($process) {
    while (1){
        Co::sleep(5);                        // 间隔五秒
        $socket = $process->exportSocket();  // 创建一个协程对象
        var_dump($socket->recv());           // 接受子进程向父进程发送的消息
    }
});
//在父进程中回收结束运行的子进程
Swoole\Process::wait(true);

*/

//例子6 父进程与新进程之间可以通过标准输入输出进行通信，必须启用标准输入输出重定向。设置参数redirect_stdin_stdout为true
$process = new Swoole\Process("callback_function", true, 1, true);
$process->start();
function callback_function(Swoole\Process $worker)
{
    //必须为绝对路径
    $worker->exec("D:\code\swoole\php-cli\bin\swoole.exe",['D:\code\phpstudy\PHPTutorial\WWW\try\php\swoole\process\echo.php']);
    //swoole_set_process_name("swoole_test");//修改进程名称 没起作用 不知道为何
    //$worker->name("andex_process");//修改进程名称 没起作用 不知道为何
    //$worker->close();  // 关闭子进程 有一些特殊的情况 子进程对象无法释放，如果持续创建进程会导致连接泄漏。调用此函数就可以直接关闭 unixSocket，释放资源。
    //$worker->exit();   // 退出子进程
}

//父进程创建一个协程容器
Co\run(function() use ($process) {
    Co:sleep(15);
    $socket = $process->exportSocket();  // 创建一个协程对象
    var_dump($socket->recv());           // 接受子进程向父进程发送的消息
});
//在父进程中回收结束运行的子进程
$result = Swoole\Process::wait(true);
var_dump($result);die;
















