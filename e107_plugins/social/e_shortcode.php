<?php
/*
* Copyright (c) e107 Inc e107.org, Licensed under GNU GPL (http://www.gnu.org/licenses/gpl.txt)
* $Id: e_shortcode.php 12438 2011-12-05 15:12:56Z secretr $
*
* Featurebox shortcode batch class - shortcodes available site-wide. ie. equivalent to multiple .sc files.
*/

if (!defined('e107_INIT')) { exit; }




class social_shortcodes extends e_shortcode
{
		
	public $var;	
	/**
	 * {XURL_ICONS: size=2x}
	 */	
	function sc_xurl_icons($parm='')
	{
							
		$social = array(

			'rss'			=> array('href'=> (e107::isInstalled('rss_menu') ? e_PLUGIN_ABS."rss_menu/rss.php?news.2" : ''), 'title'=>'Feed'),
			'facebook'		=> array('href'=> deftrue('XURL_FACEBOOK'), 	'title'=>'Facebook'),
			'twitter'		=> array('href'=> deftrue('XURL_TWITTER'),		'title'=>'Twitter'),
			'google-plus'	=> array('href'=> deftrue('XURL_GOOGLE'),		'title'=>'Google Plus'),
			'linkedin'		=> array('href'=> deftrue('XURL_LINKEDIN'),		'title'=>'LinkedIn'),
			'pinterest'		=> array('href'=> deftrue('XURL_PINTEREST'),	'title'=>'Pinterest'),
			'instagram'		=> array('href'=> deftrue('XURL_INSTAGRAM'),	'title'=>'Instagram'),
			'youtube'		=> array('href'=> deftrue('XURL_YOUTUBE'),		'title'=>'YouTube'),
			'vimeo'			=> array('href'=> deftrue('XURL_VIMEO'),		'title'=>'Vimeo')
		);
 			
		
	
		$class = (vartrue($parm['size'])) ?  'fa-'.$parm['size'] : '';

		$text = '';

		foreach($social as $id => $data)
		{

			if($data['href'] != '')
			{

				 $text .= '<a rel="external" href="'.$data['href'].'" class="e-tip social-icon social-'.$id.'">
				 	<span class="fa fa-'.$id.' '.$class.'"></span>
				 </a>';
				 
				 $text .= "\n";	
			}
		}

		if($text !='')
		{
			return 	'<p class="xurl-social-icons">'.$text.'</p>';
		}

	}	







	/**
	 * {SOCIALSHARE: url=x&title=y}
	 * @example {SOCIALSHARE: type=basic} - Show only Email, Facebook, Twitter and Google. 
	 * @example {SOCIALSHARE: dropdown=1&type=basic} - Show only Email, Facebook, Twitter and Google in a drop-down button 
	 * @example for plugin developers:  send 'var' values for use by the social shortcode. (useful for loops where the value must change regularly) 
	 * 	$socialArray = array('url'=>'your-url-here', 'title'=>'your-title-here');
		e107::getScBatch('social')->setVars($socialArray);
	 */
	function sc_socialshare($parm='') // Designed so that no additional JS required. 
	{		
		$defaultUrl 	= vartrue($this->var['url'], e_REQUEST_URL);
		$defaultTitle	= vartrue($this->var['title'], deftrue('e_PAGETITLE'). " | ". SITENAME);
		$defaultDiz		= vartrue($this->var['description'], e107::getUrl()->response()->getMetaDescription());
		
		$tp 			= e107::getParser();
		
		$url 			= varset($parm['url'], 		$defaultUrl);
		$title 			= varset($parm['title'], 	$defaultTitle) ;
		$description 	= varset($parm['title'], 	$defaultDiz);
		$media 			= "";
		$label 			= varset($parm['label'], 	$tp->toGlyph('e-social-spread'));
		
		$size			= varset($parm['size'],		'md');
		
		//TODO LANS ie. "Share on [x]" in English_global.php 
		
		$providers = array(
			'email'				=> array('icon'	=> 'e-social-mail',			'title'=>"Email to someone",	'url' => "mailto:EMAIL_RECIPIENT?subject=[t]&body=Check out this link: [u]"),
			'facebook-like'		=> array('icon' => 'e-social-thumbs-up',	'title'=>"Like on Facebook",	'url' => "http://www.facebook.com/plugins/like.php?href=[u]"),
			'facebook-share'	=> array('icon' => 'e-social-facebook',		'title'=>"Share on Facebook",	'url' => "http://www.facebook.com/sharer.php?u=[u]&t=[t]"),
			'twitter'			=> array('icon' => 'e-social-twitter',		'title'=>"Share on Twitter",	'url' => "http://twitter.com/share?url=[u]&text=[t]"),
			'google-plus1'		=> array('icon' => 'e-social-gplus',		'title'=>"+1 on Google",		'url' => "https://apis.google.com/_/+1/fastbutton?usegapi=1&size=large&hl=en&url=[u]"),
		
		//	'google-plus'		=> array('icon' => 'fa-google-plus',		'title'=>"On Google Plus",		'url' => "https://plusone.google.com/_/+1/confirm?hl=en&url=[u]"),
			'linkedin'			=> array('icon' => 'e-social-linkedin',		'title'=>"Share on LinkedIn",	'url' => "http://www.linkedin.com/shareArticle?mini=true&url=[u]"),
			'pinterest'			=> array('icon'	=> 'e-social-pinterest',	'title'=>"Share on Pinterest",	'url' => "http://www.pinterest.com/pin/create/button/?url=[u]&description=[t]&media=[m]"),
		//	'thumblr'			=> array('icon'	=>	'fa-tumblr',			'title'=>"On Tumblr",			'url' => "http://www.tumblr.com/share/link?url=[u]&name=[t]&description=[d]"),
			'stumbleupon'		=> array('icon'	=> 'e-social-stumbleupon',	'title'=>"Share on StumbleUpon",		'url' => "http://www.stumbleupon.com/submit?url=[u]&title=[t]"),
			'reddit'			=> array('icon'	=> 'e-social-reddit',		'title'=>"Share on Reddit",			'url' => "http://reddit.com/submit?url=[u]&title=[t]"), 
			'digg'				=> array('icon'	=> 'e-social-digg',			'title'=>"Share on Digg",				'url' => "http://www.digg.com/submit?url=[u]"), 
		
			//http://reddit.com/submit?url=http%3A%2F%2Fwebsite.com&title=Website%20Title  // no fa icon available
			//http://www.digg.com/submit?url=http%3A%2F%2Fwebsite.com	  // no fa icon available		
		);
	
	
	
		$data = array('u'=> rawurlencode($url), 't'=> rawurlencode($title), 'd'	=> rawurlencode($description), 'm' => rawurlencode($media));
		
		if(!vartrue($parm['dropdown']))
		{
			$butSize 	= ($size == 'lg' || $size == 'sm' || $size == 'xs') ? 'btn-'.$size : '';
		}
		else 
		{
			$butSize = 'btn-social';
		}
		

		$opt = array();
		
		foreach($providers as $k=>$val)
		{
			$pUrl = str_replace("&","&amp;",$val['url']);
			
			$shareUrl = $tp->lanVars($pUrl,$data);
			
			$opt[$k] = "<a class='e-tip btn ".$butSize." btn-default social-share'  target='_blank' title='".$val["title"]."' href='".$shareUrl."'>".$tp->toIcon($val["icon"])."</a>";	
		}
		
		// Show only Email, Facebook, Twitter and Google. 
		if(varset($parm['type']) == 'basic')
		{
			$remove = array('linkedi','pinterest', 'stumbleupon', 'digg', 'reddit', 'linkedin');
			foreach($remove as $v)
			{
				unset($opt[$v]);	
			}	
		}
		
		if(vartrue($parm['dropdown']))
		{
			$dir = ($parm['dropdown'] == 'right') ? 'pull-right' : '';
	
			$text = '<div class="btn-group '.$dir.'">
				  <a class="e-tip btn btn-dropdown btn-default btn-'.$size.' dropdown-toggle" data-toggle="dropdown" href="#" title="Share">'.$label.'</a>
				 
				  <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel"  style="min-width:435px">
				  
				    <li><div class="btn-group" style="padding-left: 7px;">'.implode("\n",$opt).'</div></li>
				  </ul>
				</div>';
		
			return $text;
		}
		else
		{
			
		
			
			
			return '<div class="btn-group text-center">'.implode("\n",$opt)."</div>";
		
		}	
		
	}

	/**
	 * @example {TWITTER_TIMELINE: id=xxxxxxx&theme=light}
	 */
	function sc_twitter_timeline($parm)
	{
		$ns = e107::getRender();
		
		$account = dirname(XURL_TWITTER);
		//data-related="twitterapi,twitter"
		$text = '<a class="twitter-timeline" href="'.XURL_TWITTER.'" data-widget-id="'.varset($parm['id']).'" data-theme="'.varset($parm['theme'],'light').'" data-link-color="#cc0000"   data-aria-polite="assertive" width="100%" height="'.varset($parm['height'],300).'" lang="'.e_LAN.'">Tweets by @'.$account.'</a>';

		$text .= <<<TMPL
		
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
TMPL;
		return (vartrue($parm['render'])) ? $ns->tablerender('',$text,'twitter-timeline',true) : $text;
	}


}

?>