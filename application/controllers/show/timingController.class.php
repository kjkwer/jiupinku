<?php
/**
 * Created by PhpStorm.
 * User: ZYQ
 * Date: 2018/3/23
 * Time: 16:31
 */

class timingController
{

    public function  __construct()
    {
        ob_end_clean();
    }
    public function timingAction(){
        //>>路径设置
        $rootPath = $_SERVER['DOCUMENT_ROOT']; //网站根目录
        $phpQueryPath = $rootPath.'/public/phpQuery/phpQuery/phpQuery.php'; //phpQuery文件路径
        $dataPath = $rootPath."/public/phpJqueryFile/";
        //>>加载文件
        include $phpQueryPath;//加载phpQuery文件
        //>>服务设置
        ignore_user_abort();//关掉浏览器，PHP脚本也可以继续执行.
        set_time_limit(0);// 通过set_time_limit(0)可以让程序无限制的执行下去
        ini_set('memory_limit','512M'); // 设置内存限制
        $interval=30;// 每隔半分钟运行
        for ($i=1;$i<=10;$i++){
            if(file_exists("$dataPath.'cjcjihua'.$i.'.txt'")){
                unlink("$dataPath.'cjcjihua'.$i.'.txt'");
            }
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
            file_put_contents($dataPath.'cjcjihua'.$i.'.txt',serialize($datas));
        }
        include CUR_VIEW_PATH."Stiming" . DS . "index_index.html";
    }
    public function indexAction(){
        include CUR_VIEW_PATH."Stiming" . DS . "index_other.html";
    }
    public function testAction(){
        //>>路径设置
        $rootPath = $_SERVER['DOCUMENT_ROOT']; //网站根目录
        $phpQueryPath = $rootPath.'/public/phpQuery/phpQuery/phpQuery.php'; //phpQuery文件路径
        $dataPath = $rootPath."/public/phpJqueryFile/";
        //>>加载文件
        include $phpQueryPath;//加载phpQuery文件
        //>>服务设置
        ignore_user_abort();//关掉浏览器，PHP脚本也可以继续执行.
        set_time_limit(0);// 通过set_time_limit(0)可以让程序无限制的执行下去
        ini_set('memory_limit','512M'); // 设置内存限制
        $interval=30;// 每隔半分钟运行
        for ($i=1;$i<=10;$i++){
            if(file_exists("$dataPath.'cjcjihua'.$i.'.txt'")){
                unlink("$dataPath.'cjcjihua'.$i.'.txt'");
            }
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
            file_put_contents($dataPath.'cjcjihua'.$i.'.txt',serialize($datas));
        }
    }
}