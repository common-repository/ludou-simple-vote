<?php
/* 
Plugin Name: Ludou Simple Vote
Plugin URI: http://www.ludou.org/wordpress-simple-vote.html
Version: 1.2
Author: 露兜
Description: 在WordPress中实现简单的支持/反对投票插件
Author URI: http://www.ludou.org/
*/

function ludou_simplevote_content($content) {
	global $post;

	$rate = get_post_meta($post->ID, "ludou_ratings_score", true);
	$rate = ($rate == '') ? 0 : $rate;
	
	$content .= '<div id="useraction">
				<div id="ajax_recommendlink">
					<div title="主题评价指数" id="recommendv">'.$rate.'</div>
					<ul class="recommend_act">
						<li><a onclick="ludou_simple_vote(this, ' . $post->ID . ', 1);" href="javascript:void(0);" id="recommend_add" title="点击支持本文">支持</a></li>
						<li><a onclick="ludou_simple_vote(this, ' . $post->ID.', -1);" href="javascript:void(0);" id="recommend_subtract" title="点击反对本文">反对</a></li>
					</ul>
				</div>
			</div>';
			
	return $content;
}

function ludou_simplevote_head() {
	$css_html = '<link rel="stylesheet" href="' .  plugin_dir_url( __FILE__ ) . 'ludou_simplevote.css' . '" type="text/css" media="screen" />';
  echo "\n" . $css_html . "\n";
}

function ludou_simplevote_footer() {
  wp_enqueue_script( 'ludou_simplevote_ajax', plugin_dir_url( __FILE__ ) . 'ludou_simplevote.js' );
  wp_localize_script( 'ludou_simplevote_ajax', 'ludousvote', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

function load_simgplevote() {
	if(is_single()) {
		// 在主题中头部插入css/jss
		add_action("wp_head", "ludou_simplevote_head");
		// 在主题中底部插入js
    add_action("wp_footer", "ludou_simplevote_footer");
		// 在文章内容部分插入投票代码
		add_filter("the_content", "ludou_simplevote_content");
	}
}

add_action("wp", "load_simgplevote");


// 停用插件时，删除插件创建的自定义栏目
function ludou_simplevote_deactivation() {
	global $wpdb;
	$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = 'ludou_ratings_score'");
}
register_deactivation_hook( __FILE__, 'ludou_simplevote_deactivation' );


add_action('wp_ajax_ludousvote', 'process_ludousvote');
add_action('wp_ajax_nopriv_ludousvote', 'process_ludousvote');
function process_ludousvote() {
  if ('GET' != $_SERVER['REQUEST_METHOD']) {
    header('Allow: GET');
    header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type: text/plain');
    die('非法请求');
  }
  
  if(	!isset($_GET['id']) || !isset($_GET['fen']) || isset($_COOKIE["ludou_simple_vote_" . $_GET['id']]) ) {
	  die('非法请求');
  }
  
  $id =  intval($_GET['id']);
  $fen = intval($_GET['fen']);
  if($id <= 0 || ($fen != -1 && $fen != 1) ) {
    die('非法请求');
  }
  
  global $wpdb;
  $id_exist = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE ID = %d AND post_type = 'post';", $id));

  if($id_exist != $id) {
	 die('非法请求');
	}

  $rate = get_post_meta($id, "ludou_ratings_score", true);

  if( $rate != '') {
	  $rate = $rate + $fen;
	  update_post_meta($id, "ludou_ratings_score", $rate);
	  setcookie("ludou_simple_vote_" . $id, $rate, time() + 3000000, COOKIEPATH);
  }
  else {
	  add_post_meta($id, "ludou_ratings_score", $fen, true);
	  setcookie("ludou_simple_vote_" . $id, $fen, time() + 3000000, COOKIEPATH);
  }
  
  die();
}
?>