<?php
 require './xmlReader.php';
 require './xmlWrite.php';


 function read(){
     $xml = new cdXmlReader();
     $content = file_get_contents('./1.xml');
     $result = $xml->fromString($content);
     file_put_contents('./1.php',"<?php\n".'return '.var_export($result, true).";\n\n?>");
     print_r($result);die;
 }

 function write(){
     $data =  array(
         'user' => array(
             'attr'=>array('xing' =>'chen','ming' =>'lei'),
             'value'=>'月光光abcd'
         ),
         'pvs' => array('value'=>'888888'),
         'date' => array('value'=>'2016-08-29'),
         'info'=>array(
             'attr'=>array('xing' =>'chen','ming' =>'lei'),
             'value'=>array(
                 'age'=>array('value'=>'10'),
                 'sex'=>array('value'=>'man')
             )
         )
     );
     $xml = new xmlWrite();
     $result = $xml->writer_2($data);
     file_put_contents('./3.xml',$result.PHP_EOL,FILE_APPEND);
 }

 function writer(){
     $data =  array(
         'user' => array(
             'attr'=>array('xing' =>'chen','ming' =>'lei'),
             'value'=>'月光光abcd'
         ),
         'pvs' => array('value'=>'888888'),
         'date' => array('value'=>'2016-08-29'),
         'info'=>array(
             'attr'=>array('xing' =>'chen','ming' =>'lei'),
             'value'=>array(
                 'age'=>array('value'=>'10'),
                 'sex'=>array('value'=>'man'),
                 'hobby'=>array(
                     'attr'=>array('define'=>'自定义'),
                     'value'=>array(
                         'read'=>array('value'=>'xiyouji'),
                         'music'=>array('value'=>'tiantang'),
                     )
                 )
             )
         )
     );
    $xml = new xmlWrite();
    $xml->writer_3($data);
 }

 //read();

writer();

