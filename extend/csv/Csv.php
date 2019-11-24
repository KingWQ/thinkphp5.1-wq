<?php
/**
 * @Author Quincy  2019/11/24 下午10:38
 * @Note
 */
namespace csv;

class Csv
{

    /**
     * @Author Quincy  2019/1/5 下午6:22
     * @Note 导出并下载csv文件  先设置csv相关的Header头, 然后打开 PHP output流, 渐进式的往output流中写入数据, 写到一定量后将系统缓冲冲刷到响应中
     * @param $query object 模型构造对象
     * @param $coulumnName  array  列名
     * @param $fieldName  array 字段名
     * @param $csvName string 文件名
     */
    public static function downCsv($query, $coulumnName, $fieldName,$csvName,$extraData=array())
    {
        set_time_limit(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$csvName);
        header('Cache-Control: max-age=0');

        $fp = fopen('php://output', 'a');       //打开output流
        mb_convert_variables('GBK', 'UTF-8', $coulumnName);
        fputcsv($fp, $coulumnName);                            //将数据格式化为CSV格式并写入到output流中

        $query->chunk(1000,function($data) use($fp,$fieldName,$extraData){
            foreach($data as $row){
                $rowData = [];
                for($i=0; $i<count($fieldName); $i++){
                    if(substr_count($fieldName[$i],'@') >=1){
                        $arr = explode('@',$fieldName[$i]);
                        $name = $arr[0];
                        $relation = $arr[1];
                        $str = $row[$relation][$name];
                    }else if(substr_count($fieldName[$i],'extra_') >=1){
                        $str = $extraData[$fieldName[$i]];
                    }else{
                        $str = $row[$fieldName[$i]]?$row[$fieldName[$i]]: '';
                    }

                    $rowData[] =  $str;
                }
                mb_convert_variables('GBK', 'UTF-8', $rowData);

                fputcsv($fp, $rowData);
            }

            //释放变量的内存
            unset($data);

            //刷新输出缓冲到浏览器  必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
            ob_flush();
            flush();
        });

        fclose($fp);exit();
    }

    public static function simpleCsv( $coulumnName, $fieldName,$csvName,$data)
    {
        set_time_limit(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$csvName);
        header('Cache-Control: max-age=0');

        $fp = fopen('php://output', 'a');       //打开output流
//        mb_convert_variables('GBK', 'UTF-8', $coulumnName);
        fputcsv($fp, $coulumnName);                            //将数据格式化为CSV格式并写入到output流中

        foreach($data as $row){
            $rowData = [];
            for($i=0; $i<count($fieldName); $i++){
                $rowData[] =  $row[$fieldName[$i]];
            }
//            mb_convert_variables('GBK', 'UTF-8', $rowData);
            fputcsv($fp, $rowData);
        }

        //释放变量的内存
        unset($data);

        //刷新输出缓冲到浏览器  必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
        ob_flush();
        flush();

        fclose($fp);exit();
    }
}
