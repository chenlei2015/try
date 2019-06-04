<?php
var_dump(json_decode(false,true));die;
phpinfo();die;
ini_set('display_errors','on');
error_reporting(-1);
//print_r(range(a,z));

//$lamp1 = ['a'=>'php','b'=>'mysql','c'=>'linux','d'=>'nginx','java_web'=>['l'=>'java_1','s'=>'tomcat'],'t'=>'c++'];

//$lamp2 = ['a'=>'phper','b'=>'mysqld','e'=>'java','f'=>'jsp','java_web'=>['l'=>'java_2','s'=>'nginx','k'=>'hebernet']];

//print_r(array_merge($lamp1,$lamp2));



//end($lamp1);

//echo current($lamp1);

//echo key($lamp1);


//$format="my name is %2\$s,my age is %1\$u,selary is %3\$0.4f,template is %1\$0.2f";

//$name="chenlei";

//$age = 15;

//$selary=20000;

//echo sprintf($format,$age,$name,$selary);

//echo nl2br("kjfjldsjfldsfjl");

//echo "999999999999";

//$str = "看<span>你好呀</span>";

//echo $str;
//echo "<br>";
//echo htmlentities($str,ENT_NOQUOTES,'UTF-8');

//$lamp3 = ['a'=>'php','b'=>'mysql','c'=>'linux','d'=>'nginx'];
//
//print_r(preg_grep("/php|mysql/",$lamp3));
//
//
//

//$pattern = "/<[\/\!]*?[^<>]*/is";

// $text="这个文本中有<b>粗体</b>和<u>下划线</u>以及<i>斜体</i>还有<font color='red'>带有颜色的字体</font>";

//ECHO preg_replace($pattern,'',$text,4);



//$pattern2="/^\d{4}(\W)(\d{2})(\W)\d{2}$/";

//print_r(preg_grep($pattern2,['a'=>'2018-08/15','b'=>'2019-08/15']));


//$image = file_get_contents('http://image.dfs168.com/market/105/PC_01.jpg');
//file_put_contents('./image/2.jpg',$image);

//$handler = fopen('./1.jpg','w');
//
//fwrite($handler,$image);
//
//fclose($handler);

//readfile('http://image.dfs168.com/market/105/PC_01.jpg');

//mkdir('./image/',0777);

//try{
//    if(1===1) throw new \Exception('异常错误');
//}catch (Exception $e){
//    echo $e->getMessage();
//}

//trigger_error('注意',E_USER_NOTICE);

//trigger_error('警告',E_USER_WARNING);

//trigger_error('错误',E_USER_ERROR);


//print_r(getdate());

//print_r(scandir('../try'));

//mkdir('./css/crm/',0775,true);//递归的创建目录

//$image='kkkkkkkkkkkkk';
//
//header('Content-Type:text/plain');
//
//header('Content-Disposition:attachment;filename="'.time().'.txt"');
//
////header('Content-Length:'.strlen($image));
//
//echo $image;

//readfile($image);

//echo 7777777778889999


//function test(int $a,string $b,array $c) : array {
//       return [
//          'a' => $a,
//          'b' => $b,
//           'c' => $c
//       ];
//}
//
//$result=test(3,"kkk",['p','f']);
//
//var_dump($result);

//declare(strict_types=1);


//function get(){
//
//    try {
//
//      niu();
//
//    }catch (Throwable $e){
//
//        echo "sss: {$e->getMessage()}";
//
//    }
//}
//
//
//
//get();

//$m=888;
//$username = isset($_GET['user]) ? $_GET['user] : 'nobody';

////现在
//$r=7;
//$f=7;
//echo $c = $r <=> $f;

//define('ARR',['a'=>'555','b']);
//echo ARR['a'];

//echo intdiv(5,3);






