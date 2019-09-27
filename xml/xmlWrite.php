<?php
class xmlWrite {
    /**
     * 第一种方法：拼接
     * @param array $data
     * @return string
     */
    function writer_1(array $data){
        $html = '<?xml version="1.0"?>';
        $html .='<root>';
        foreach ($data as $key =>$val){
            $html .='<'.$key.'>'.$val.'</'.$key.'>';
        }
        $html .='</root>';

        return $html;
    }


    /**
     * 第二种使用：DOMDocument对象
     * @param $arr
     * @param int $dom
     * @param int $item
     * @return string
     */
    function writer_2($data){
        $dom = new DOMDocument("1.0");
        $dom->encoding = 'UTF-8';
        $root = $dom->createElement("root");//创建root标签元素对象
        $root->setAttribute("xmlns","http://www.cdiscount.com"); // 设置root标签元素的标签属性值
        $this->test($data,$root,$dom);
        $dom->appendChild($root);
        return $dom->saveXML();
    }

    public function test($data,$root,$dom){
        foreach ($data as $key=>$val){
            if (!is_array($val['value'])){
                $item = $dom->createElement($key,$val['value']);
                if(isset($val['attr'])){
                    foreach ($val['attr']as $k => $v){
                        $item->setAttribute($k,$v);
                    }
                }
                $root->appendChild($item);
            }else {
                $item = $dom->createElement($key);
                if(isset($val['attr'])){
                    foreach ($val['attr']as $k => $v){
                        $item->setAttribute($k,$v);
                    }
                }
                $root->appendChild($item);
                $this->test($val['value'],$item,$dom);
            }
        }
    }
    /**
     * 第三种使方法
     */
    function writer_3($data){
        header("Content-type: text/html; charset=utf-8");
        $xml = new XMLWriter();
        $xml->openUri("php://output");//输出到浏览器
        $xml->openUri("./mimvp.xml");//输入到该文件
        //设置缩进字符串
        $xml->setIndentString("\t");
        $xml->setIndent(true);
        //xml文档开始
        $xml->startDocument('1.0', 'utf-8');
        //创建根节点
        $xml->startElement("MimvpInfo");
        $this->createSub($xml,$data);
        $xml->endElement();
        $xml->endDocument();
    }

    function createSub($xml,$data){
        foreach ($data as $key =>$val){
            if (!is_array($val['value'])){
                $xml->startElement($key);
                if(isset($val['attr'])){
                    foreach ($val['attr']as $k => $v){
                        $xml->writeAttribute($k,$v);
                    }
                }
                $xml->text($val['value']);
                $xml->endElement();
            }else{
                $xml->startElement($key);
                if(isset($val['attr'])){
                    foreach ($val['attr']as $k => $v){
                        $xml->writeAttribute($k,$v);
                    }
                }
                $this->createSub($xml,$val['value']);
                $xml->endElement();
            }
        }

    }

}




