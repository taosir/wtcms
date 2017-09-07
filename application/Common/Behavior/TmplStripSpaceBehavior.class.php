<?php

namespace Common\Behavior;

use Think\Behavior;

// 初始化钩子信息
class TmplStripSpaceBehavior extends Behavior {

    // 行为扩展的执行入口必须是run
    public function run(&$tmplContent){
    	if(C('TMPL_STRIP_SPACE')) {
    		/* 去除html空格与换行 */
    		$find           = array('~>\s+<~','~>(\s+\n|\r)~');
    		$replace        = array('> <','>');
    		$tmplContent    = preg_replace($find, $replace, $tmplContent);
    	}
        
    }
}