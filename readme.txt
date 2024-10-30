=== Ludou Simple Vote ===

Contributors: 露兜
Donate link: http://www.ludou.org/wordpress-simple-vote.html
Tags: ratings, rating,vote, up, down, digg, ajax, post
Requires at least: 2.7
Tested up to: 4.0
Stable tag: 1.2


== Description ==

在WordPress中实现简单的支持/反对投票插件，界面样式模仿Discuz!，使用WordPress的自定义栏目来记录投票得分。卸载该插件后将会自动删除插件创建的自定义栏目。

这个插件使用cookie来实现简单的防作弊的功能，如果访客的浏览器关闭了cookie功能将无法投票，启动cookie后在只能对同一篇文章投一次票。

= 使用说明 =

在后台启动该插件即可开始使用，打开博客的文章页，内容底部可看到投票按钮。


== Installation ==

1. 下载插件，解压缩，你将会看到一个文件夹ludou-simple-vote，然后将其放置到插件目录下，插件目录通常是 `wp-content/plugins/`
2. 在后台对应的插件管理页激活该插件 Ludou Simple Vote
3. 完成


= 卸载插件 =

1. 进入后台 -&gt; 插件，停用 选择 Ludou Simple Vote
2. 如果您打算不再使用该插件，您可以将wp-content/plugins/ludou-simple-vote/目录删除；


== Frequently Asked Questions ==
= 投票排行 =
如果你想在侧边栏或博客的其他地方显示投票排行榜，显示一个按投票得分排序的文章标题列表，可以使用以下代码：
<code>
<?php
    $hight_voting = $wpdb->get_results("SELECT post_title, ID
        FROM $wpdb->posts,$wpdb->postmeta
        WHERE meta_key = 'ludou_ratings_score'
        AND ID = post_id
        ORDER BY meta_value DESC
        LIMIT 10");
?>
<ul>
    <?php foreach($hight_voting as $vote_post) : ?>
    <li><a href="<?php echo get_permalink( $vote_post->ID ); ?>" title="<?php echo $vote_post->post_title; ?>"><?php echo $vote_post->post_title; ?></a></li>
    <?php endforeach; ?>
</ul>
</code>

== Screenshots ==

1. ludou-simple-vote.

== Changelog ==

= 1.0 =

* 第一个版本

= 1.1 =

* 增强了反作弊功能，优化了部分代码

= 1.2 =

* 优化AJAX运行方式，提高兼容性