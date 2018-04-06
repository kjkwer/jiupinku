<?php
include "F:\Program\pk10\public\phpQuery\phpQuery\phpQuery.php";
ignore_user_abort();//关掉浏览器，PHP脚本也可以继续执行.
set_time_limit(0);// 通过set_time_limit(0)可以让程序无限制的执行下去
ini_set('memory_limit','512M'); // 设置内存限制
$interval=30;// 每隔半分钟运行
do{
    //ToDo
    for ($i=1;$i<=10;$i++){
//    echo $i;
        phpQuery::newDocumentFile("http://www.cjcjihua.com/wei/".$i);
        $datas = array();
        //>>期数
        $qiShu = pq(".textInfo div .t1:eq(0)")->text();
        $datas["qiShu"] = substr($qiShu,0,6);
        //>>名次
        $mingCi = pq(".textInfo div .t1:eq(1)")->text();
        $mingCi = str_replace("【","",$mingCi);
        $mingCi = str_replace("】","",$mingCi);
        $datas["mingCi"] = $mingCi;
        //>>正确率
        $trueRate = pq(".textInfo div .t3")->text();
        $datas["trueRate"] = $trueRate;
        //>>开奖号码
        $numbers = pq(".bgList span")->text();
        $numbers = explode("\n",$numbers);
        unset($numbers[10]);
        $datas["numbers"] = $numbers;
        //>>计划表数据
        $n=0;
        while ($str = pq("tbody tr:eq($n)")->text()){
            $str = preg_replace("/\t+/","@",$str);
            $str = preg_replace("/\n/","",$str);
            $str = trim($str,"@");
            $datas["data"][] = explode("@",$str);
            $n++;
        }
        //>>爬取时间
        $datas["time"] = time();
        //>>抓取倒计时
        $shijian = pq("script")->html();
        $datas["daojishi"] = str_replace("index.php","index.php?index.php?p=show&c=index&a=getQishu",$shijian);
        //>>写入文件
        file_put_contents("F:\Program\pk10\public\phpJqueryFile\cjcjihua".$i.".txt",serialize($datas));
    }
    sleep($interval);// 等待5分钟
}
while(true);
