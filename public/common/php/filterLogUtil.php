<?php

class filterLogUtil
{
    public function helloWorld()
    {
        echo 'helloWorld';
    }
    
    // 读取log
    public function filterLogFile($filePath, $arrIplist, &$errorCode, &$errorMessage)
    {
        
        try{
            $file = fopen($filePath, "r"); // 以只读的方式打开文件
            if(empty($file)){
                $errorCode = 201;
                $errorMessage = "file not found";
                return;
            }
            $pattern = '/\s{2,}/';
            $arrIpReturn = array();
            //输出文本中所有的行，直到文件结束为止。
            while(!feof($file)) {
                
                $itemStr = fgets($file); //fgets()函数从文件指针中读取一行
                $itemArray = preg_split($pattern,$itemStr); // 按大于2个以上空格分割的每行内容提取出来
                $itemArray = array_filter($itemArray); // 对itemArray进行校验
                
                if(sizeof($itemArray)==3 && in_array($itemArray[2],$arrIplist)){ //
//                     print_r($itemArray);
                   $arrIpReturn[$itemArray[2]] = $itemArray;
                }
                if(sizeof($arrIpReturn) >0 && sizeof($arrIpReturn)  == sizeof($arrIplist)){
                    break;
                }
            }
            fclose($file);
            #print_r($arrIpReturn);
        }catch (Exception $exception){
            $errorCode = $exception->getCode();
            $errorMessage = $exception->getMessage();
        }
        return $arrIpReturn;
    }
}

#C:\local_data\genius\eNodeB
/*$filePath = 'C:\local_data\genius\eNodeB\201806221107.log';

$errorCode='';
$errorMessage='';
$iplist = array('100.93.170.193','100.93.171.143','100.93.170.221','100.71.250.82');
$obj = new filterLogUtil();
$obj->filterLogFile($filePath,$iplist,$errorCode,$errorMessage);
echo $errorMessage;*/

?>