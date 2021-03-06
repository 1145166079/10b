<?php
  //分页工具类
  class Page{
      /*
       * 获取分页字符串
       * @param1 string $uri，分页要请求的脚本url
       * @param3 int $counts，总记录数
       * @param4 int $length，每页显示的记录数
       * @param5 int $page = 1，当前页码
       * @return string，带有a标签的，可以点击发起请求的字符串
      */
      public static function getPageStr($uri,$counts,$length,$page = 1){
          //构造一个能够点击的字符串
          //得到数据显示的字符串
          $pagecount = ceil($counts/$length);        //总页数
          $str_info = "当前一共有{$counts}条记录，每页显示{$length}条记录，一共{$pagecount}页，当前是第{$page}页";
          //生成可以操作的连接：首页 上一页 下一页 末页
          //求出上一页和下一页页码
          $prev = ($page <= 1) ? 1 : $page - 1;
          $next = ($page >= $pagecount) ? $pagecount : $page + 1;
          $str_click = <<<END
        <a href="{$uri}?page=1">首页</a>
        <a href="{$uri}?page={$prev}">上一页</a>
        <a href="{$uri}?page={$next}">下一页</a>
        <a href="{$uri}?page={$pagecount}">末页</a>
END;
          //按照页码分页字符串
          $str_number = '';
          for($i = 1;$i <= $pagecount;$i++){
              $str_number .= "<a href='{$uri}?page={$i}'>{$i}</a> ";
          }
          //下拉框分页字符串：利用js的onchang事件来改变当前脚本的href
          $str_select = "<select onchange=\"location.href='{$uri}?page='+this.value\">";
          //将所有的页码放入到option
          for($i = 1;$i <= $pagecount;$i++){
              if($i == $page)
                  $str_select .= "<option value='{$i}' selected='selected'>{$i}</option>";
              else
                  $str_select .= "<option value='{$i}'>{$i}</option>";
          }
          $str_select .= "</select>";
          //返回值
          return $str_info . $str_click . $str_number . $str_select;
      }
  }

//class Page {
//
//    public $firstRow; // 起始行数
//    public $listRows; // 列表每页显示行数
//    public $parameter; // 分页跳转时要带的参数
//    public $totalRows; // 总行数
//    public $totalPages; // 分页总页面数
//    public $rollPage = 11; // 分页栏每页显示的页数
//    public $lastSuffix = true; // 最后一页是否显示总页数
//    private $p = 'p'; //分页参数名
//    private $url = ''; //当前链接URL
//    private $nowPage = 1;
//    // 分页显示定制
//    private $config = array(
//        'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
//        'prev' => '<<',
//        'next' => '>>',
//        'first' => '1...',
//        'last' => '...%TOTAL_PAGE%',
//        'theme' => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
//    );
//
//    /**
//     * 架构函数
//     * @param array $totalRows  总的记录数
//     * @param array $listRows  每页显示记录数
//     * @param array $parameter  分页跳转的参数
//     */
//    public function __construct($totalRows, $listRows = 20, $parameter = array()) {
////        C('VAR_PAGE') && $this->p = C('VAR_PAGE'); //设置分页参数名称
//        /* 基础设置 */
//        $this->totalRows = $totalRows; //设置总记录数
//        $this->listRows = $listRows;  //设置每页显示行数
////        $this->parameter = empty($parameter) ? get_param($_GET) : $parameter;
//        $this->nowPage = empty($_GET[$this->p]) ? 1 : intval($_GET[$this->p]);
//        $this->nowPage = $this->nowPage > 0 ? $this->nowPage : 1;
//        $this->firstRow = $this->listRows * ($this->nowPage - 1);
//    }
//
//    /**
//     * 定制分页链接设置
//     * @param string $name  设置名称
//     * @param string $value 设置值
//     */
//    public function setConfig($name, $value) {
//        if (isset($this->config[$name])) {
//            $this->config[$name] = $value;
//        }
//    }
//
//    /**
//     * 生成链接URL
//     * @param  integer $page 页码
//     * @return string
//     */
//    private function url($page) {
//        return str_replace(urlencode('[PAGE]'), $page, $this->url);
//    }
//
//    /**
//     * 组装分页链接
//     * @return string
//     */
//    public function show() {
//        if (0 == $this->totalRows)
//            return '';
//
//        /* 生成URL */
//        $this->parameter[$this->p] = urlencode('[PAGE]');
//        $this->url = U(CONTROLLER_NAME . '/' . ACTION_NAME, $this->parameter);
//        /* 计算分页信息 */
//        $this->totalPages = ceil($this->totalRows / $this->listRows); //总页数
//        if (!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
//            $this->nowPage = $this->totalPages;
//        }
//
//        /* 计算分页临时变量 */
//        $now_cool_page = $this->rollPage / 2;
//        $now_cool_page_ceil = ceil($now_cool_page);
//        $this->lastSuffix && $this->config['last'] = $this->totalPages;
//
//        //上一页
//        $up_row = $this->nowPage - 1;
//        $up_page = $up_row > 0 ? '<a class="prev" data-page="'.$up_row.'" href="' . $this->url($up_row) . '">' . $this->config['prev'] . '</a>' : '';
//
//        //下一页
//        $down_row = $this->nowPage + 1;
//        $down_page = ($down_row <= $this->totalPages) ? '<a class="next" data-page="'.$down_row.'" href="' . $this->url($down_row) . '">' . $this->config['next'] . '</a>' : '';
//
//        //第一页
//        $the_first = '';
//        if ($this->totalPages > $this->rollPage && ($this->nowPage - $now_cool_page) >= 1) {
//            $the_first = '<a class="first" data-page="1" href="' . $this->url(1) . '">' . $this->config['first'] . '</a>';
//        }
//
//        //最后一页
//        $the_end = '';
//        if ($this->totalPages > $this->rollPage && ($this->nowPage + $now_cool_page) < $this->totalPages) {
//            $the_end = '<a class="end" data-page="'.$this->totalPages.'" href="' . $this->url($this->totalPages) . '">' . $this->config['last'] . '</a>';
//        }
//
//        //数字连接
//        $link_page = "";
//        for ($i = 1; $i <= $this->rollPage; $i++) {
//            if (($this->nowPage - $now_cool_page) <= 0) {
//                $page = $i;
//            } elseif (($this->nowPage + $now_cool_page - 1) >= $this->totalPages) {
//                $page = $this->totalPages - $this->rollPage + $i;
//            } else {
//                $page = $this->nowPage - $now_cool_page_ceil + $i;
//            }
//            if ($page > 0 && $page != $this->nowPage) {
//
//                if ($page <= $this->totalPages) {
//                    $link_page .= '<a class="num" data-page="'.$page.'" href="' . $this->url($page) . '">' . $page . '</a>';
//                } else {
//                    break;
//                }
//            } else {
//                if ($page > 0 && $this->totalPages != 1) {
//                    $link_page .= '<span class="current">' . $page . '</span>';
//                }
//            }
//        }
//
//        //替换分页内容
//        $page_str = str_replace(
//                array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%DOWN_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%TOTAL_ROW%', '%TOTAL_PAGE%'), array($this->config['header'], $this->nowPage, $up_page, $down_page, $the_first, $link_page, $the_end, $this->totalRows, $this->totalPages), $this->config['theme']);
//        return "<div>{$page_str}</div>";
//    }
//
//}
