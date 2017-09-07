<?php
namespace Portal\Controller;

use Common\Controller\HomebaseController;

class UserArticleController extends HomebaseController {
    protected $posts_model;
    protected $term_relationships_model;
    protected $terms_model;

    function _initialize() {
        parent::_initialize();
        $this->posts_model = D("Portal/Posts");
        $this->terms_model = D("Portal/Terms");
        $this->term_relationships_model = D("Portal/TermRelationships");
    }

    // 用户中心 文章管理列表
    public function index(){
        $this->_lists(array("post_status"=>array('neq',3)));
        $this->_getTree();
        $this->display();
    }

    // 文章添加
    public function add(){
        $terms = $this->terms_model->order(array("listorder"=>"asc"))->select();
        $term_id = I("get.term",0,'intval');
        $this->_getTermTree();
        $term=$this->terms_model->where(array('term_id'=>$term_id))->find();
        $this->assign("term",$term);
        $this->assign("terms",$terms);
        $this->display();
    }

    // 文章添加提交
    public function add_post(){
        if (IS_POST) {
            if(empty($_POST['term'])){
                $this->error("请至少选择一个分类！");
            }
            if(!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])){
                foreach ($_POST['photos_url'] as $key=>$url){
                    $photourl=sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][]=array("url"=>$photourl,"alt"=>$_POST['photos_alt'][$key]);
                }
            }
            $_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);

            $_POST['post']['post_modified']=date("Y-m-d H:i:s",time());
            $_POST['post']['post_author']=get_current_userid();
            $article=I("post.post");
            $article['smeta']=json_encode($_POST['smeta']);
            $article['post_content']=htmlspecialchars_decode($article['post_content']);
            $result=$this->posts_model->add($article);
            if ($result) {
                foreach ($_POST['term'] as $mterm_id){
                    $this->term_relationships_model->add(array("term_id"=>intval($mterm_id),"object_id"=>$result));
                }

                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }

        }
    }

    // 文章编辑
    public function edit(){
        $id=  I("get.id",0,'intval');

        $term_relationship = M('TermRelationships')->where(array("object_id"=>$id,"status"=>1))->getField("term_id",true);
        $this->_getTermTree($term_relationship);
        $terms=$this->terms_model->select();
        $post=$this->posts_model->where("id=$id")->find();
        $this->assign("post",$post);
        $this->assign("smeta",json_decode($post['smeta'],true));
        $this->assign("terms",$terms);
        $this->assign("term",$term_relationship);
        $this->display();
    }

    // 文章编辑提交
    public function edit_post(){
        if (IS_POST) {
            if(empty($_POST['term'])){
                $this->error("请至少选择一个分类！");
            }
            $post_id=intval($_POST['post']['id']);

            $this->term_relationships_model->where(array("object_id"=>$post_id,"term_id"=>array("not in",implode(",", $_POST['term']))))->delete();
            foreach ($_POST['term'] as $mterm_id){
                $find_term_relationship=$this->term_relationships_model->where(array("object_id"=>$post_id,"term_id"=>$mterm_id))->count();
                if(empty($find_term_relationship)){
                    $this->term_relationships_model->add(array("term_id"=>intval($mterm_id),"object_id"=>$post_id));
                }else{
                    $this->term_relationships_model->where(array("object_id"=>$post_id,"term_id"=>$mterm_id))->save(array("status"=>1));
                }
            }

            if(!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])){
                foreach ($_POST['photos_url'] as $key=>$url){
                    $photourl=sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][]=array("url"=>$photourl,"alt"=>$_POST['photos_alt'][$key]);
                }
            }
            $_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);
            unset($_POST['post']['post_author']);
            $_POST['post']['post_modified']=date("Y-m-d H:i:s",time());
            $article=I("post.post");
            $article['smeta']=json_encode($_POST['smeta']);
            $article['post_content']=htmlspecialchars_decode($article['post_content']);
            $result=$this->posts_model->save($article);
            if ($result!==false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
    }

    // 文章排序
    public function listorders() {
        $status = parent::_listorders($this->term_relationships_model);
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }

    /**
     * 文章列表处理方法,根据不同条件显示不同的列表
     * @param array $where 查询条件
     */
    private function _lists($where=array()){
        $term_id=I('request.term',0,'intval');

        $where['post_type']=array(array('eq',1),array('exp','IS NULL'),'OR');

        if(!empty($term_id)){
            $where['b.term_id']=$term_id;
            $term=$this->terms_model->where(array('term_id'=>$term_id))->find();
            $this->assign("term",$term);
        }

        $start_time=I('request.start_time');
        if(!empty($start_time)){
            $where['post_date']=array(
                array('EGT',$start_time)
            );
        }

        $end_time=I('request.end_time');
        if(!empty($end_time)){
            if(empty($where['post_date'])){
                $where['post_date']=array();
            }
            array_push($where['post_date'], array('ELT',$end_time));
        }

        $keyword=I('request.keyword');
        if(!empty($keyword)){
            $where['post_title']=array('like',"%$keyword%");
        }

        $where['post_author'] = get_current_userid();
        $this->posts_model
            ->alias("a")
            ->where($where);

        if(!empty($term_id)){
            $this->posts_model->join("__TERM_RELATIONSHIPS__ b ON a.id = b.object_id");
        }

        $count=$this->posts_model->count();

        $page = $this->page($count, 10);

        $this->posts_model
            ->alias("a")
            ->join("__USERS__ c ON a.post_author = c.id")
            ->where($where)
            ->limit($page->firstRow , $page->listRows)
            ->order("a.post_date DESC");
        if(empty($term_id)){
            $this->posts_model->field('a.*,c.user_login,c.user_nicename');
        }else{
            $this->posts_model->field('a.*,c.user_login,c.user_nicename,b.listorder,b.tid');
            $this->posts_model->join("__TERM_RELATIONSHIPS__ b ON a.id = b.object_id");
        }
        $posts=$this->posts_model->select();

        $this->assign("page", $page->show('Admin'));
        $this->assign("formget",array_merge($_GET,$_POST));
        $this->assign("posts",$posts);
    }

    // 获取文章分类树结构 select 形式
    private function _getTree(){
        $term_id=empty($_REQUEST['term'])?0:intval($_REQUEST['term']);
        $result = $this->terms_model->order(array("listorder"=>"asc"))->select();

        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $r) {
            $r['str_manage'] = '<a href="' . U("AdminTerm/add", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("AdminTerm/edit", array("id" => $r['term_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("AdminTerm/delete", array("id" => $r['term_id'])) . '">删除</a> ';
            $r['visit'] = "<a href='#'>访问</a>";
            $r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
            $r['id']=$r['term_id'];
            $r['parentid']=$r['parent'];
            $r['selected']=$term_id==$r['term_id']?"selected":"";
            $array[] = $r;
        }

        $tree->init($array);
        $str="<option value='\$id' \$selected>\$spacer\$name</option>";
        $taxonomys = $tree->get_tree(0, $str);
        $this->assign("taxonomys", $taxonomys);
    }

    // 获取文章分类树结构
    private function _getTermTree($term=array()){
        $result = $this->terms_model->order(array("listorder"=>"asc"))->select();

        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $r) {
            $r['str_manage'] = '<a href="' . U("AdminTerm/add", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("AdminTerm/edit", array("id" => $r['term_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("AdminTerm/delete", array("id" => $r['term_id'])) . '">删除</a> ';
            $r['visit'] = "<a href='#'>访问</a>";
            $r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
            $r['id']=$r['term_id'];
            $r['parentid']=$r['parent'];
            $r['selected']=in_array($r['term_id'], $term)?"selected":"";
            $r['checked'] =in_array($r['term_id'], $term)?"checked":"";
            $array[] = $r;
        }

        $tree->init($array);
        $str="<option value='\$id' \$selected>\$spacer\$name</option>";
        $taxonomys = $tree->get_tree(0, $str);
        $this->assign("taxonomys", $taxonomys);
    }
}
