<?php
/**
 * Created by PhpStorm.
 * User: ZYQ
 * Date: 2018/3/22
 * Time: 10:03
 */



class indexController extends Controller
{
    public static $title;
    public static $qq;
    public static $xw = array(
        1=>"技巧心得",
        2=>"业界动态",
    );
    public function  __construct()
    {
        ob_end_clean();
        $model = new ModelNew("title");
        $titleData = $model->findBySql("select * from sl_title limit 1");
        self::$title = $titleData[0]["biaoti"];
        $model = new ModelNew("qun");
        $titleData = $model->findBySql("select * from sl_qun limit 1");
        self::$qq = $titleData[0]["qunhao"];
    }

    //>>首页
    public function indexAction(){
        $id = empty($_GET["id"])?1:$_GET["id"];

        //>>获取彩票数据
        $dataStr = file_get_contents("public/phpJqueryFile/cjcjihua".$id.".txt");
        $datas = unserialize($dataStr);
        $qiShu = $datas["qiShu"];//>>期数
        $mingCi = $datas["mingCi"];//>>名次
        $trueRate = $datas["trueRate"];//>>正确率
        $numbers = $datas["numbers"];//>>开奖号码
        $cjcjDatas = $datas["data"];//>>计划数据
        $time = $datas["time"];//>>爬取时间
        $daojishi = $datas["daojishi"];
//        var_dump($cjcjDatas);exit();
//        var_dump($qiShu,$mingCi,$trueRate,$numbers,$cjcjDatas);exit();

        //>>获取平台简介
        $jjModel = new ModelNew("jianjie");
        $jjDatas = $jjModel->findOne(1);
        $jjStr = $jjDatas["neirong"];
        $jjStr = htmlspecialchars_decode($jjStr);

        //>>获取心得技巧
        $wzglModel = new ModelNew("wzgl");
        $xdjqDatas = $wzglModel->findBySql("select * from sl_wzgl WHERE fenlei='技巧心得' ORDER by id DESC limit 6");

        //>>获得业界动态
        $yjdtDatas = $wzglModel->findBySql("select * from sl_wzgl WHERE fenlei='业界动态' ORDER by id DESC limit 5");

        include CUR_VIEW_PATH."Sindex" . DS . "index_index.html";
    }

    //>>平台简介
    public function ptjjDetailAction(){
        $jjModel = new ModelNew("jianjie");
        $jjDatas = $jjModel->findOne(1);
        $jjDatas["neirong"] = htmlspecialchars_decode($jjDatas["neirong"]);
        include CUR_VIEW_PATH."Sindex" . DS . "index_ptjj.html";
    }

    //>>新闻列表
    public function xwlbAction(){
        $leixing = $_GET["leixing"];
        $lx = self::$xw[$leixing];
        //>>设置分页数据
        $wzglModel = new ModelNew("wzgl");
        $count = $wzglModel->findBySql("select count(*) as total from sl_wzgl WHERE fenlei='{$lx}'");
        $totalNum = $count[0]["total"];//文章总数
        $pageSize = 10;  //每页数量
        $maxPage=$totalNum==0?1:ceil($totalNum/$pageSize); //总共有的页数
        $page=isset($_GET['page'])?$_GET['page']:1;
        if($page < 1)
        {
            $page=1;
        }
        if($page > $maxPage)
        {
            $page=$maxPage;
        }
        $limit=" limit ".($page-1)*$pageSize.",$pageSize";
        $wzDates = $wzglModel->findBySql("select * from sl_wzgl ORDER BY id DESC $limit");
        //>>页码设置
        $pageData = self::pageSetAction($page,$maxPage);
        $init = $pageData["init"];
        $max = $pageData["max"];

        include CUR_VIEW_PATH."Sindex" . DS . "index_xwlb.html";
    }

    //>>新闻详情
    public function xwDetailAction(){
        $id = $_GET["id"];
        $model = new ModelNew("wzgl");
        //>>增加人气
        $model->findBySql("update sl_wzgl set renqi = renqi+1 WHERE id=$id");
        $data = $model->findOne($id);
        //>>查询热门文章
        $hots = $model->findBySql("select * from sl_wzgl ORDER BY renqi DESC limit 9");
//        var_dump($hots);exit();
        include CUR_VIEW_PATH."Sindex" . DS . "index_xwDetail.html";
    }

    //>>页码设置
    public static function pageSetAction($page,$maxPage){
        $pageNum = 5;//页码个数
        $pageOffer = ($pageNum-1)/2;//页码偏移量
        if($maxPage<=$pageNum){
            $init=1;
            $max = $maxPage;
        }
        if($maxPage>$pageNum){
            if($page<=$pageOffer){
                $init=1;
                $max = $pageNum;
            }else{
                if($page+$pageOffer>=$maxPage+1){
                    $init = $maxPage-$pageNum+1;
                    $max = $pageNum;
                }else{
                    $init = $page-$pageOffer;
                    $max = $page+$pageOffer;
                }
            }
        }
        return ["init"=>$init,'max'=>$max];
    }

    public static function jqueryAction(){
        for ($i=1;$i<=10;$i++){
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
            file_put_contents("public/phpJqueryFile/cjcjihua".$i.".txt",serialize($datas));
        }
    }

    public function getQishuAction(){
        $dataStr = file_get_contents("public/phpJqueryFile/cjcjihua1.txt");
        $datas = unserialize($dataStr);
        $qiShu = $datas["qiShu"];//>>期数
//        //>>获取最新期数
//        include "public/phpQuery/phpQuery/phpQuery.php";
//        phpQuery::newDocumentFile("http://www.cjcjihua.com/wei/1");
//        //>>期数
//        $newQishu = pq(".textInfo div .t1:eq(0)")->text();
//        $newQishu = substr($newQishu,0,6);
//        if ($newQishu > $qiShu){
//            self::jqueryAction();
//        }
        echo $qiShu;
    }
}