<?php

namespace Portal\Controller;

use Common\Controller\HomebaseController;

class SearchController extends HomebaseController {
    
    //搜索结果页面
    public function index() {
		$keyword = I('request.keyword/s','');
		
		if (empty($keyword)) {
			$this -> error("关键词不能为空！请重新输入！");
		}
		
		$this -> assign("keyword", $keyword);
		$this -> display(":search");
    }
    
}
