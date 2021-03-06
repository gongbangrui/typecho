  <?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
  <header id="header" class="app-header navbar" role="menu">
      <!-- navbar header（交集处） -->
        <?php echo Content::slectNavbarHeader(); ?>

        <button class="pull-right visible-xs dk" ui-toggle-class="show animated animated-lento fadeIn" target=".navbar-collapse">
          <i class="fontello fontello-gear text-lg"></i>
        </button>
        <button class="pull-left visible-xs" ui-toggle-class="off-screen animated" target=".app-aside" ui-scroll="app">
          <i class="fontello fontello-menu text-lg"></i>
        </button>
        <!-- brand -->
        <a href="<?php $this->options->rootUrl(); ?>/" class="navbar-brand text-lt">
            <?php if ($this->options->logo!=""): ?>
            <img src="<?php echo $this->options->logo; ?>">
            <?php else: ?>
                <?php if ($this->options->indexNameIcon == ""): ?>
                    <i class="fontello fontello-home"></i>
                <?php else: ?>
                    <i class="<?php echo $this->options->indexNameIcon; ?>"></i>
                <?php endif; ?>
                <span class="hidden-folded m-l-xs"><?php $this->options->IndexName(); ?></span>
            <?php endif; ?>
        </a>
        <!-- / brand -->
      </div>
      <!-- / navbar header -->

      <!-- navbar collapse（顶部导航栏） -->
    <?php echo Content::selectNavbarCollapse() ?>
        <!-- buttons -->
        <div class="nav navbar-nav hidden-xs">
          <a href="#" class="btn no-shadow navbar-btn" ui-toggle-class="app-aside-folded" target=".app">
            <i class="fontello fontello-dedent text icon-fw"></i>
            <i class="fontello fontello-indent icon-fw text-active"></i>
          </a>
          <a href="#" class="btn no-shadow navbar-btn" ui-toggle-class="show" target="#aside-user">
            <i class="iconfont icon-user icon-fw"></i>
          </a>
        </div>
        <!-- / buttons -->


        <!-- search form -->
        <form id="searchform" class="navbar-form navbar-form-sm navbar-left shift" method="post" role="search">
          <div class="form-group">
            <div class="input-group rounded bg-light">
              <input id="search_input" type="search" name="s" class="transparent rounded form-control input-sm no-border padder" required placeholder="<?php _me("输入关键词搜索…") ?>">
                <!--搜索提示-->
                <!--<ul class="dropdown-menu" style="display: block; top: 30px; left: 0px;">
                    <li tabindex="0" style="">
                        <a>友人C</a>
                    </li>
                </ul>-->
              <span id="search_submit" class="transparent input-group-btn">
                  <button  type="submit" class="transparent btn btn-sm"><i class="fontello fontello-search"></i></button>
              </span>
            </div>
          </div>
        </form>
          <a href="" style="display: none" id="searchUrl"></a>
        <!-- / search form -->
        <?php
        $hideReadModeItem = false;
        $hideTalkItem = false;
        $headerItemsOutput = "";
        if (!empty(Typecho_Widget::widget('Widget_Options')->headerItems)){
          $json = '['.Typecho_Widget::widget('Widget_Options')->headerItems.']';
          $headerItems = json_decode($json);
          foreach ($headerItems as $headerItem){
            $itemName = $headerItem->name;
            @$itemStatus = $headerItem->status;
            @$itemLink = $headerItem->link;
            @$itemClass = $headerItem->class;
            @$itemTarget = $headerItem->target;
            if ($itemName === 'talk' && strtoupper($itemStatus) ==='HIDE'){
              $hideTalkItem = true;
              continue;
            }
            if ($itemName === "mode" && strtoupper($itemStatus === 'HIDE')){
              $hideTalkItem = true;
              continue;
            }
            if (@$itemTarget){
                $linkStatus = 'target="'.$itemTarget.'"';
            }else{
                $linkStatus = 'target="_blank"';
            }
            $headerItemsOutput .= '<li class="dropdown"><a '.$linkStatus.' href="'.$itemLink.'" class="dropdown-toggle"><i class="'.$itemClass.' icon-fw"></i><span class="visible-xs-inline">'._mt($itemName).'</span></a></li>';
          }
        }
        ?>
        <ul class="nav navbar-nav navbar-right">
            <?php if(@in_array('musicplayer',$this->options->featuresetup)): ?>
            <li class="music-box hidden-xs hidden-sm">
                <div id="skPlayer"></div>
            </li>
            <li class="dropdown "><a class="skPlayer-list-switch dropdown-toggle"><i class="fontello fontello-headphones"></i><span class="visible-xs-inline"></span></a></li>
            <?php endif; ?>
            <?php echo $headerItemsOutput; ?>
            <?php if (!$hideTalkItem): ?>
          <!--闲言碎语-->
          <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle">
              <i class="fontello fontello-bell icon-fw"></i>
              <span class="visible-xs-inline">
              <?php _me("闲言碎语") ?>
              </span>
              <span class="badge badge-sm up bg-danger pull-right-xs"></span>
            </a>
            <!-- dropdown -->
            <div class="dropdown-menu w-xl animated fadeInUp">
              <div class="panel bg-white">
                <div class="panel-heading b-light bg-light">
                  <strong>
              <?php _me("闲言碎语") ?>
                  </strong>
                </div>
                <div class="list-group" id="smallRecording">
                  <?php
                  //$comments->listComments();
                  $slug = "cross";    //页面缩略名
                  $limit = 3;    //调用数量
                  $length = 140;    //截取长度
                  $ispage = true;    //true 输出slug页面评论，false输出其它所有评论
                  $isGuestbook = $ispage ? " = " : " <> ";

                  $db = $this->db;    //Typecho_Db::get();
                  $options = $this->options;    //Typecho_Widget::widget('Widget_Options');

                  $page = $db->fetchRow($db->select()->from('table.contents')
                      ->where('table.contents.status = ?', 'publish')
                      ->where('table.contents.created < ?', $options->gmtTime)
                      ->where('table.contents.slug = ?', $slug));

                  if ($page) {
                      $type = $page['type'];
                      $routeExists = (NULL != Typecho_Router::get($type));
                      $page['pathinfo'] = $routeExists ? Typecho_Router::url($type, $page) : '#';
                      $page['permalink'] = Typecho_Common::url($page['pathinfo'], $options->index);

                      $comments = $db->fetchAll($db->select()->from('table.comments')
                          ->where('table.comments.status = ?', 'approved')
                          ->where('table.comments.created < ?', $options->gmtTime)
                          ->where('table.comments.type = ?', 'comment')
                          ->where('table.comments.cid ' . $isGuestbook . ' ?', $page['cid'])
                          ->order('table.comments.created', Typecho_Db::SORT_DESC)
                          ->limit($limit));
                      //Regex.Replace();
                      foreach ($comments AS $comment) {
                          $content = Content::postCommentContent(Markdown::convert($comment['text']),$this->user->hasLogin(),"","","");
                         $content = trim(strip_tags($content));
                       echo '<a href="'.$this->options->rootUrl.'/index.php/cross.html" class="list-group-item"><span class="clear block m-b-none words_contents">'.Content::returnExceptShortCodeContent($content).'<br><small class="text-muted">'.date('Y-n-j H:i:s',$comment['created']+($this->options->timezone - idate("Z"))).'</small></span></a>';
                      }
                  } else {
                      echo '<a href="'.$this->options->rootUrl.'/cross.html" class="list-group-item"><span class="clear block m-b-none">这是一条默认的说说，如果你看到这条动态，请去后台新建独立页面，地址填写cross,自定义模板选择时光机。具体说明请参见主题的使用攻略。<br><small class="text-muted">'.date("F jS, Y \a\t h:i a",time()+($this->options->timezone - idate("Z"))).'</small></span></a>';
                  }?>
                </div>
              </div>
            </div>
          </li>
          <!--/闲言碎语-->
            <?php endif; ?>
            <?php if (!in_array('hideLogin',$this->options->featuresetup)): ?>
          <!--登录管理-->
          <li class="dropdown" id="easyLogin">
            <a onclick="return false" data-toggle="dropdown" class="dropdown-toggle clear" data-toggle="dropdown">
            <?php if($this->user->hasLogin()): ?>
              <span class="thumb-sm avatar pull-right m-t-n-sm m-b-n-sm m-l-sm">
                <img src="<?php echo Utils::getAvator($this->user->mail,65) ?>">
                <i class="on md b-white bottom"></i>
              </span>
              <span class="hidden-sm hidden-md"><?php $this->user->screenName(); ?></span>
            <?php else: ?>
            <span class="text"><?php _me("登录") ?></span>
          <?php endif; ?>
              <b class="caret"></b><!--下三角符号-->
            </a>
            <!-- dropdown(已经登录) -->
            <?php if($this->user->hasLogin()): ?>
            <ul class="dropdown-menu animated fadeInRight" id="Logged-in">
              <li class="wrapper b-b m-b-sm bg-light m-t-n-xs">
                <div>
                <?php
                    $time= date("H",time()+($this->options->timezone - idate("Z")));
                    $percent= $time/24;
                    $percent= sprintf("%01.2f", $percent*100).'%';
                ?>
                <?php if($time>=6 && $time<=11): ?>
                  <p><?php _me("早上好，") ?><?php $this->user->screenName(); ?>.</p>
                <?php elseif($time>=12 && $time<=17): ?>
                  <p><?php _me("下午好，") ?><?php $this->user->screenName(); ?>.</p>
                <?php else : ?>
                <p><?php _me("晚上好，") ?><?php $this->user->screenName(); ?>.</p>
              <?php endif; ?>
                </div>
                <div class="progress progress-xs m-b-none dker">
                  <div class="progress-bar progress-bar-info" data-toggle="tooltip" data-original-title="<?php _me("时间已经度过"); echo $percent; ?>" style="width: <?php echo $percent; ?>"></div>
                </div>
              </li>
              <!--文章RSS订阅-->
              <li>
                <a target="_blank" href="<?php $this->options->adminUrl(); ?>write-post.php">
                  <i style="position: relative;width: 30px;margin: -11px -10px;margin-right: 0px;overflow: hidden;line-height: 30px;text-align: center;" class="fontello fontello-edit"></i><span><?php _me("新建文章") ?></span>
                </a>
              </li>
              <!--评论RSS订阅-->
              <li>
                <a target="_blank" href="<?php $this->options->adminUrl(); ?>manage-comments.php"><i style="position: relative;width: 30px;margin: -11px -10px;margin-right: 0px;overflow: hidden;line-height: 30px;text-align: center;" class="glyphicon glyphicon-comment"></i><span><?php _me("评论管理") ?></span></a>
              </li>
              <!--后台管理(登录时候才会显示)-->
              <?php if($this->user->hasLogin()): ?>
              <li>
                <a target="_blank" href="<?php $this->options->adminUrl(); ?>"><i style="position: relative;width: 30px;margin: -11px -10px;margin-right: 0px;overflow: hidden;line-height: 30px;text-align: center;" class="fontello fontello-cogs"></i><span><?php _me("后台管理") ?></span></a>
              </li>
              <?php else: ?>
              <?php endif; ?>

              <li class="divider"></li>
              <li>
                <a id="sign_out" no-pjax href="<?php $this->options->logoutUrl(); ?>"><?php _me("退出") ?></a>
              </li>
            </ul>
            <!-- / dropdown(已经登录) -->
          <?php else: ?>
          <div class="dropdown-menu w-lg wrapper bg-white animated shake" aria-labelledby="navbar-login-dropdown">
            <form id="Login_form" action="<?php $this->options->loginAction();?>" method="post">
              <div class="form-group">
                <label for="navbar-login-user"><?php _me("用户名") ?></label>
                <input type="text" name="name" id="navbar-login-user" class="form-control" placeholder="<?php _me("用户名或电子邮箱") ?>"></div>
              <div class="form-group">
                <label for="navbar-login-password"><?php _me("密码") ?></label>
                <input type="password" name="password" id="navbar-login-password" class="form-control" placeholder="<?php _me("密码") ?>"></div>
              <button type="submit" id="login-submit" name ="submitLogin" class="btn btn-block btn-primary">
              <span class="text"><?php _me("登录") ?></span>
              <span class="text-active"><?php _me("登录中") ?>...</span>
                  <span class="banLogin_text"><?php _me("刷新页面后登录") ?></span>
              <i class="animate-spin  fontello fontello-spinner hide" id="spin-login"></i>
                  <i class="animate-spin fontello fontello-refresh hide" id="ban-login"></i>
              </button>
              <?php $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://'; ?>

              <input type="hidden" name="referer" value="<?php $this->options->rootUrl(); ?>"
                     data-current-url="value"></form>
          </div>
          <?php endif; ?>
          </li>
          <!--/登录管理-->
            <?php endif;  ?>
        </ul>
      </div>
      <!-- / navbar collapse -->
  </header>
