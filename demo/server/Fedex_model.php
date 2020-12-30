<?php
class Fedex_model
{
    public  $code             = 'Fedex';

//    旧
//    private $key              = "GuS2PCYHaiXzlH34";
//    private $password         = "BwTl5wu2sT0h0ZNr4D2StjAx2";
//    private $account_number   = "510088000";
//    private $meter_number     = "114090196";

    //新
    private $key              = "WPXoLxuRN8MOAiP5";
    private $password         = "fJOWbPluIfvvOSb7tHL6Cws3w";
    private $account_number   = "510087860";
    private $meter_number     = "119166370";

    private $UploadDocumentServiceWsdl;
    private $logo;
    private $signature;

    /**
     * 实例化
     * OST_model constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->UploadDocumentServiceWsdl = PATH_WSDL.'/UploadDocumentService_v17.wsdl';
        $this->logo = PATH_WSDL.'/logo.png';
        $this->signature = PATH_WSDL.'/signature.png';
    }

    public function uploadEtdImage(){
        //请求数据
        $params = [
            'WebAuthenticationDetail'    => [
                'UserCredential'     => [
                    'Key' =>  $this->key,
                    'Password' => $this->password
                ]
            ],
            'ClientDetail'               => [
                'AccountNumber' => $this->account_number,
                'MeterNumber' => $this->meter_number
            ],
            'TransactionDetail'          => [
                'CustomerTransactionId' => time().mt_rand(100000000,999999999)
            ],
            'Version'                    => [
                'ServiceId' => 'cdus',
                'Major' => '17',
                'Intermediate' => '0',
                'Minor' => '0'
            ],
            'Images'     => [
                '0' => [
                    'Id' => 'IMAGE_1',
                    'Image' => $this->fileToBase64($this->logo)
                ],
                '1' => [
                    'Id' => 'IMAGE_2',
                    'Image' => $this->fileToBase64($this->signature)
                ]
            ],
        ];
        $result = $this->httpRequest($params,$this->UploadDocumentServiceWsdl,'uploadImages');
        $this->pr($result);die;
    }

    /**
     * 请求
     * @param $data
     * @param $wsdl
     * @param $action
     * @return array|bool
     */
    private function httpRequest($data,$wsdl,$action)
    {
        try{
            ini_set( 'soap.wsdl_cache_enabled', 0 );
            $client      = new SoapClient($wsdl, array('trace' => 1,'encoding'=>'ISO-8859-1'));
            $client->__setLocation("https://wsbeta.fedex.com/web-services/uploaddocument");
            return $result      = $client->$action($data);
        }
        catch (SoapFault $exception)
        {
            var_dump($exception->getMessage());
            return false;
        }
    }

    /**
     * 对象转数组
     * @param $data
     * @return array
     */
    public function objectToArray($data)
    {
        if(is_array($data)){
            foreach ($data as $key => $value){
                $data[$key] = $this->objectToArray($value);
            }
        }elseif (is_object($data)){
            $data = (array) $data;
            foreach ($data as $key => $value){
                $data[$key] = $this->objectToArray($value);
            }
        }
        return $data;
    }

    /**
     * 文件转base64输出
     * @param String $file 文件路径
     * @return String base64 string
     */

    private function fileToBase64($file){
        $base64_file = '';
        if(file_exists($file)){
            $mime_type = (new finfo(FILEINFO_MIME_TYPE))->file($file);
            $base64_data = base64_encode(file_get_contents($file));
            //$base64_file = 'data:'.$mime_type.';base64,'.$base64_data;
            $base64_file = $base64_data;
        }
        return $base64_file;
    }


    private function pr($arr, $escape_html = true, $bg_color = '#EEEEE0', $txt_color = '#000000')
    {
        echo sprintf('<pre style="background-color: %s; color: %s;">', $bg_color, $txt_color);
        if ($arr) {
            if ($escape_html) {
                echo htmlspecialchars(print_r($arr, true));
            } else {
                print_r($arr);
            }

        } else {
            var_dump($arr);
        }
        echo '</pre>';
    }


}