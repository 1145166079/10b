<?php

class IndexController extends ApiController{
    public function indexAction(){
        $this->getView()->assign("","");
    }

    public function addAction(){
        //接到图片的信息
        $img=$_FILES;
        //实例化封装图像类
        $obj=new Upload();
        $imgs=$obj->upload($img);
      //图片存储的路径
       $_POST['img']=$imgs['img']['savepath'].$imgs['img']['savename'];
       $insert=new BaseModel('lianxi');
      $res= $insert->insert($_POST);
       if($res){
           echo "<script>alert('添加成功');location.href='show'</script>";
       }
    }
     public function showAction(){
         $page = isset($_GET['page'])?$_GET['page']:1;

         $page = isset($_POST['page'])?$_POST['page']:$page;

         //搜索
         $sousuo=isset($_POST['sousuo'])?$_POST['sousuo']:'';

         //每页显示条数
         $size=5;
          //偏移量
         $start=($page-1)*$size;

         $obj=new BaseModel('lianxi');

         $where = "1=1";

         if($sousuo){
             $where.=" and title like '%".$sousuo."%'";
         }

         //总条数
         $count=$obj->where($where)->count();
         //总页数
         $max=ceil($count/$size);
         $arr['data']=$obj->where($where)->limit($start,$size)->select();

         $arr['up']=$page-1<1?1:$page-1;
         $arr['next']=$page+1>$max?$max:$page+1;
         $arr['wy']=$max;
         $arr['page']=$page;
         if(isset($_POST['page']))
         {
             echo json_encode($arr);

             return false;
         }
         $this->getView()->assign("content", $arr);
     }
     //删除
    public function delAction(){
        $id = $_POST['id'];

        $db =  new BaseModel('lianxi');

        $res = $db->where("id=$id")->del();

        if($res){
            echo 1;
            return false;
        }else{
            echo 0;
            return false;
        }
    }

    //分类新闻
    public function typeAction(){
        $this->getView()->assign("","");
    }
    //添加分类新闻
    public function add_typeAction(){
       $_POST['time']=date('Y-m-d H:i:s');
       $obj=new BaseModel('new_type');

       if($obj->insert($_POST)){
         echo "<script>alert('添加新闻分类成功');location.href='newtypelist'</script>";
       }else{
           echo "<script>alert('添加新闻分类失败');location.href='type'</script>";
       }
    }
    //展示新闻分类
    public function newtypelistAction(){
        $obj=new BaseModel('new_type');
       $arr= $obj->select();
       if($arr){
           $this->getView()->assign("arr",$arr);
       }
    }
    //删除
    public function deleAction(){
       $id=$_GET['id'];
       $obj=new BaseModel('new_type');
       $a=$obj->where("type_id=$id")->del();
       if($a){
           echo "<script>alert('删除新闻分类成功');location.href='newtypelist'</script>";
          die;
       }else{
           echo "<script>alert('删除新闻分类失败');location.href='newtypelist'</script>";
           die;
       }
    }
    //修改页面
    public function updateAction(){
        $id=$_GET['id'];
        $obj=new BaseModel('new_type');
      $arr= $obj->where("type_id=$id")->find();
        if($arr){
            $this->getView()->assign("arr",$arr);
        }
    }
   //修改分类
    public function update_doAction(){
        $id=$_POST['type_id'];
        $obj=new BaseModel('new_type');
        $a=$obj->where("type_id=$id")->update($_POST);
        if($a){
            echo "<script>alert('修改分类成功');location.href='newtypelist'</script>";
            die;
        }else{
            echo "<script>alert('修改分类失败');location.href='newtypelist'</script>";
           die;
        }
    }
          //第一个轮播图和首页列表数据展示
    public function signAction(){
       $arr= $this->api($_GET);
       echo 'callback'."(".json_encode($arr).")";
    die;
    }


   public function newsAction(){
     $this->header();
       $start=isset($_GET['start'])?$_GET['start']:5;
       $limit=$_GET['limit'];
       $obj=new BaseModel('lianxi');
       $arr=$obj->limit($limit,$start)->select();
       echo json_encode($arr);die;
   }
         //新闻详情
    public function detailAction(){
       $id=$_GET['id'];
       $obj=new BaseModel('lianxi');
       $arr=$obj->where("id=$id")->find();
        $this->json($arr);
    }
    //注册验证签名
    public function regAction(){
        $arr=$this->api($_GET);
        $this->json($arr);
    }
    public function reg_doAction(){
        $obj=new OpensslClass();
        $obj->loadKey();
        $arr['name']= $obj->privateDecrypt($_GET['account']);
        $arr['pwd']= $obj->privateDecrypt($_GET['password']);
        $arr['email']= $obj->privateDecrypt($_GET['email']);
        $a=new BaseModel('reg');
        $b=$a->insert($arr);
        if($b){
            $this->json(1);
        }
    }
    //登录
    public function loginAction(){
        $obj=new OpensslClass();
        $obj->loadKey();
        $name=$obj->privateDecrypt($_GET['account']);
        $pwd=$obj->privateDecrypt($_GET['password']);
        $a=new BaseModel('reg');
        $b=$a->where("name=$name")->find();
        if($b){
            $arr=[
                'id'=>$b['id'],
                'name'=>$b['name'],
                'code'=>1
            ];
            $this->json($arr);
        }else{
            $arr=[
                'code'=>2
            ];
            $this->json($arr);
        }
    }

    public function login_doAction(){
        $id=$_GET['id'];
         $a=new BaseModel('reg');
         $res=$a->where("id=$id")->find();
        if($res){
            $r=[
                'code'=>1,
                'id'=>$res['id'],
                'name'=>$res['name']
            ];
            $this->json($r);
        }else{
            $r=[
                'code'=>2,
                'success'=>'失败'
            ];
            $this->json($r);
        }
    }
    //点赞
    public function clickAction(){
        $arr['u_id']=$_GET['ids'];
        $arr['new_id']=$_GET['id'];
        $arr['status']=1;
        $arr['createtime']=date('Y-m-d H:i:s');
        $obj=new BaseModel('click');
        $a=$obj->insert($arr);
        if($a){
            $r=new BaseModel('click');
           $c= $r->where("id=".$a)->find();
            $b=[
               'code'=>1,
                'status'=>$c['status'],
            ];
            $this->json($b);
        }
    }
    //判断是否点赞过
    public function q_clickAction(){
        $this->header();
        $id=$_GET['ids'];
        $new_id=$_GET['id'];
        $obj=new BaseModel('click');
       $arr=$obj->where("u_id=$id and new_id=$new_id")->find();
        if($arr){
            echo 1;die;
        }else{
            echo 2;die;
        }
    }

    //收藏功能
    public function scAction(){
        $arr['user_id']=$_GET['ids'];
        $arr['n_id']=$_GET['id'];
        $obj=new BaseModel('sc');
        $a=$obj->insert($arr);
        if($a){
            $b=[
                'code'=>1,
            ];
          $this->json($b);
        }else{
            $b=[
                'code'=>2,
            ];
            $this->json($b);
        }
    }

    //如果有收藏过就不能让他收藏了
    public function shoucangAction(){
       $this->header();
        $user_id=$_GET['ids'];
        $new_id=$_GET['id'];
        $obj=new BaseModel('sc');
        $arr=$obj->where("user_id=$user_id and n_id=$new_id")->select();
        if($arr){
          echo 1;die;
        }
    }

    //评论
    public function pinglunAction(){
        $arr['user_id']=$_GET['ids'];
        $arr['new_id']=$_GET['id'];
        $arr['content']=$_GET['content'];
        $arr['time']=date('Y-m-d H:i:s');
        $obj=new BaseModel('pinglun');
        $a=$obj->insert($arr);
        if($a){
            $this->json(1);
        }else{
            $this->json(2);
        }
    }






    //hedera
    public function header(){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST");
        header("Access-Control-Allow-Headers: Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With");
    }
    //json
    public function json($data){
        echo "callback"."(".json_encode($data).")";
        die;
    }
}
