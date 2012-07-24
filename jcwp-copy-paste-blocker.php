<?php
  /*
    Plugin Name: jcwp copy paste blocker
    Plugin URI: http://jaspreetchahal.org/wordpress-copy-paste-protection-blocker-plugin
    Description: This plugin blocks text selections and right clicks on your blog pages and posts.
    Author: Jaspreet Chahal
    Version: 1.0
    Author URI: http://jaspreetchahal.org
    License: GPLv2 or later
    */

    /*
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    */
    
    // if not an admin just block access
    if(preg_match('/admin\.php/',$_SERVER['REQUEST_URI']) && is_admin() == false) {
        return false;
    }
    
    register_activation_hook(__FILE__,'jcorgcpb_activate');
    function jcorgcpb_activate() {
            add_option('jcorgcpb_alert','disable');
            add_option('jcorgcpb_alert_message','Content copy is disabled on this site.');
            add_option('jcorgcpb_use_css','enable');
            add_option('jcorgcpb_disable_selection','enable');
            add_option('jcorgcpb_disable_right_mouse_click',"enable");
            add_option('jcorgcpb_disable_keys',"enable");
            add_option('jcorgcpb_where_on',"everywhere");
            add_option('jcorgcpb_linkback',"no");
    }
    
    add_action("admin_menu","jcorgcpb_menu");
    function jcorgcpb_menu() {
        add_options_page('JCWP CopyPaste Blocker', 'JCWP CopyPaste Blocker', 'manage_options', 'jcorgcpb-plugin', 'jcorgcpb_plugin_options');
    }
    add_action('admin_init','jcorgcpb_regsettings');
    function jcorgcpb_regsettings() {        
        register_setting("jcorgcpb-setting","jcorgcpb_alert");
        register_setting("jcorgcpb-setting","jcorgcpb_alert_message");
        register_setting("jcorgcpb-setting","jcorgcpb_use_css");
        register_setting("jcorgcpb-setting","jcorgcpb_disable_selection");     
        register_setting("jcorgcpb-setting","jcorgcpb_disable_right_mouse_click");     
        register_setting("jcorgcpb-setting","jcorgcpb_disable_keys");     
        register_setting("jcorgcpb-setting","jcorgcpb_where_on");     
        register_setting("jcorgcpb-setting","jcorgcpb_linkback");    
        wp_enqueue_script('jquery');
        wp_enqueue_script('jcorgcpb_script',plugins_url("jcorgcpbjs.js",__FILE__));
    }   
    
    add_action('wp_head','jcorgcpb_inclscript',100);
    function jcorgcpb_inclscript() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jcorgcpb_script',plugins_url("jcorgcpbjs.js",__FILE__),array('jquery'));
        if(
            (get_option("jcorgcpb_where_on") =="posts" && is_single()) ||
            (get_option("jcorgcpb_where_on") =="pages" && is_page()) ||        
            (get_option("jcorgcpb_where_on") =="postspages" && (is_single() || is_page())) ||
            get_option("jcorgcpb_where_on") =="everywhere"
        ) {
        ?> 
            <script type="text/javascript"> 
         jQuery(document).ready(function(){             
             var jcorgcpboptions = {
                 <?php 
                 $options = "";
                 if(get_option('jcorgcpb_alert') =='enable') {
                 $options.="alertUser:true,
                 alertMessage:".(strlen(get_option('jcorgcpb_alert_message'))>0?"'".addslashes(get_option('jcorgcpb_alert_message'))."'":"'Sorry! Content copy is not allowed?'");
                 }
                 if(get_option('jcorgcpb_use_css') =='enable') {
                    if(strlen($options)>0) $options.=',';
                    $options.='useCSS:true';
                 }
                 if(get_option('jcorgcpb_disable_keys') =='enable') {
                    if(strlen($options)>0) $options.=',';
                    $options.='blockPageSave:true';
                 }
                 if(get_option('jcorgcpb_disable_selection') =='enable') {
                   if(strlen($options)>0) $options.=',';
                    $options.='blockDocTextSelection:true';                    
                 }
                 if(get_option('jcorgcpb_disable_right_mouse_click') =='enable') {
                    if(strlen($options)>0) $options.=',';
                    $options.='blockRightClick:true';
                 }
                 echo $options;
                 ?>
             };
             jQuery().jccopyblock(jcorgcpboptions);
         });
         </script>
         
        <?php
        }
        if(get_option('jcorgcpb_linkback') =="Yes") {
            echo '<a style="font-size:0em !important;color:transparent !important" href="http://jaspreetchahal.org">Content protection is powered by http://jaspreetchahal.org</a>';
        }
    }
    
    function jcorgcpb_plugin_options() {
        jcorgcpbDonationDetail();           
        ?> 
        <style type="text/css">
        .jcorgbsuccess, .jcorgberror {   border: 1px solid #ccc; margin:0px; padding:15px 10px 15px 50px; font-size:12px;}
        .jcorgbsuccess {color: #FFF;background: green; border: 1px solid  #FEE7D8;}
        .jcorgberror {color: #B70000;border: 1px solid  #FEE7D8;}
        .jcorgb-errors-title {font-size:12px;color:black;font-weight:bold;}
        .jcorgb-errors { border: #FFD7C4 1px solid;padding:5px; background: #FFF1EA;}
        .jcorgb-errors ul {list-style:none; color:black; font-size:12px;margin-left:10px;}
        .jcorgb-errors ul li {list-style:circle;line-height:150%;/*background: url(/images/icons/star_red.png) no-repeat left;*/font-size:11px;margin-left:10px; margin-top:5px;font-weight:normal;padding-left:15px}
        td {font-weight: normal;}
        </style><br>
        <div class="wrap" style="float: left;" >
            <?php             
            
            screen_icon('tools');?>
            <h2>JaspreetChahal's Copy Paste Blocker settings</h2>
            <?php 
                $errors = get_settings_errors("",true);
                $errmsgs = array();
                $msgs = "";
                if(count($errors) >0)
                foreach ($errors as $error) {
                    if($error["type"] == "error")
                        $errmsgs[] = $error["message"];
                    else if($error["type"] == "updated")
                        $msgs = $error["message"];
                }

                echo jcorgcpbMakeErrorsHtml($errmsgs,'warning1');
                if(strlen($msgs) > 0) {
                    echo "<div class='jcorgbsuccess' style='width:90%'>$msgs</div>";
                }

            ?><br><br> 
            <form action="options.php" method="post" id="jcorgbotinfo_settings_form">
            <?php settings_fields("jcorgcpb-setting");?>
            <table class="widefat" style="width: 700px;" cellpadding="7">
                 <tr valign="top">
                    <th scope="row">Disable content selection</th>
                    <td><input type="radio" name="jcorgcpb_disable_selection" <?php if(get_option('jcorgcpb_disable_selection') == "enable") echo "checked='checked'";?>
                            value="enable" 
                            /> Yes
                            <input type="radio" name="jcorgcpb_disable_selection" <?php if(get_option('jcorgcpb_disable_selection') == "disable" || get_option('jcorgcpb_disable_selection') == "") echo "checked='checked'";?>
                            value="disable" 
                            /> No 
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Use CSS to disable text selection</th>
                    <td><input type="radio" name="jcorgcpb_use_css" <?php if(get_option('jcorgcpb_use_css') == "enable") echo "checked='checked'";?>
                            value="enable" 
                            /> Yes
                            <input type="radio" name="jcorgcpb_use_css" <?php if(get_option('jcorgcpb_use_css') == "disable" || get_option('jcorgcpb_use_css') == "") echo "checked='checked'";?>
                            value="disable" 
                            /> No 
                    </td>
                </tr> 
                <tr valign="top">
                    <th scope="row">Disable Ctrl+A, Ctrl+C and Ctrl+S</th>
                    <td><input type="radio" name="jcorgcpb_disable_keys" <?php if(get_option('jcorgcpb_disable_keys') == "enable") echo "checked='checked'";?>
                            value="enable" 
                            /> Yes
                            <input type="radio" name="jcorgcpb_disable_keys" <?php if(get_option('jcorgcpb_disable_keys') == "disable" || get_option('jcorgcpb_disable_keys') == "") echo "checked='checked'";?>
                            value="disable" 
                            /> No 
                    </td>
                </tr>  
                <tr valign="top">
                    <th scope="row">Disable right mouse click</th>
                    <td><input type="radio" name="jcorgcpb_disable_right_mouse_click" <?php if(get_option('jcorgcpb_disable_right_mouse_click') == "enable") echo "checked='checked'";?>
                            value="enable" 
                            /> Yes
                            <input type="radio" name="jcorgcpb_disable_right_mouse_click" <?php if(get_option('jcorgcpb_disable_right_mouse_click') == "disable" || get_option('jcorgcpb_disable_right_mouse_click') == "") echo "checked='checked'";?>
                            value="disable" 
                            /> No 
                    </td>
                </tr>  
                <tr valign="top">
                    <th scope="row">Protect</th>
                    <td>
                    <select name="jcorgcpb_where_on">
                    <option value="posts" <?php if(get_option('jcorgcpb_where_on') == "posts"){  _e('selected');}?> >Posts</option>
                    <option value="pages" <?php if(get_option('jcorgcpb_where_on') == "pages") { _e('selected');}?> >Pages</option>
                    <option value="postspages" <?php if(get_option('jcorgcpb_where_on') == "postspages") { _e('selected');}?> >Posts and Pages</option>
                    <option value="everywhere" <?php if(get_option('jcorgcpb_where_on') == "everywhere") { _e('selected');}?> >Posts, Pages and Home page</option>
                    </select>
               </tr>
                <tr valign="top">
                    <th scope="row">Enable Alert Message</th>
                    <td><input type="radio" name="jcorgcpb_alert" <?php if(get_option('jcorgcpb_alert') == "enable") echo "checked='checked'";?>
                            value="enable" 
                            /> Yes
                            <input type="radio" name="jcorgcpb_alert" <?php if(get_option('jcorgcpb_alert') == "disable" || get_option('jcorgcpb_alert') == "" ) echo "checked='checked'";?>
                            value="disable" 
                            /> No <br>
                            <strong>Alert message will show up if Use CSS is disabled and "Disble text selection" is ON or if user right click on a page and setting "Disable right click" is ON</strong>
                    </td>
                </tr>     
               <tr valign="top">
                    <th width="25%" scope="row">Alert message</th>
                    <td><input type="text" name="jcorgcpb_alert_message"
                            value="<?php echo get_option('jcorgcpb_alert_message'); ?>"  style="padding:5px" size="40"/></td>
                </tr>
               <tr valign="top">
                    <th scope="row">Include invisible 'Content protected by' link</th>
                    <td><input type="checkbox" name="jcorgcpb_linkback"
                            value="Yes" <?php if(get_option('jcorgcpb_linkback') =="Yes") echo "checked='checked'";?> /> <br>
                            <strong>An inivisible link will be placed in the footer which points to author's website</strong></td>
                </tr> 
        </table>
        <p class="submit">
            <input type="submit" class="button-primary"
                value="Save Changes" />
        </p>          
            </form>
        </div>
        <?php     
        echo "<div style='float:left;margin-left:20px;margin-top:75px'>".jcorgcpbfeeds()."</div>";
    }
    
    function jcorgcpbDonationDetail() {
        ?>    
        <style type="text/css"> .jcorgcr_donation_uses li {float:left; margin-left:20px;font-weight: bold;} </style> 
        <div style="padding: 10px; background: #f1f1f1;border:1px #EEE solid; border-radius:15px;width:98%"> 
        <h2>If you like this Plugin, please consider donating a small amount.</h2> 
        You can choose your own amount. Developing this awesome plugin took a lot of effort and time; days and weeks of continuous voluntary unpaid work. 
        If you like this plugin or if you are using it for commercial websites, please consider a donation to the author to 
        help support future updates and development. 
        <div class="jcorgcr_donation_uses"> 
        <span style="font-weight:bold">Main uses of Donations</span><ol ><li>Web Hosting Fees</li><li>Cable Internet Fees</li><li>Time/Value Reimbursement</li><li>Motivation for Continuous Improvements</li></ol> </div> <br class="clear"> <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MHMQ6E37TYW3N"><img src="https://www.paypalobjects.com/en_AU/i/btn/btn_donateCC_LG.gif" /></a> <br><br><strong>For help please visit </strong><br> 
        <a href="http://jaspreetchahal.org/wordpress-copy-paste-protection-blocker-plugin">http://jaspreetchahal.org/wordpress-copy-paste-protection-blocker-plugin</a> <br><strong> </div>
        
        <?php
        
    }
    function jcorgcpbfeeds() {
        $list = "
        <table style='width:400px;' class='widefat'>
        <tr>
            <th>
            Latest posts from JaspreetChahal.org
            </th>
        </tr>
        ";
        $max = 5;
        $feeds = fetch_feed("http://feeds.feedburner.com/jaspreetchahal/mtDg");
        $cfeeds = $feeds->get_item_quantity($max); 
        $feed_items = $feeds->get_items(0, $cfeeds); 
        if ($cfeeds > 0) {
            foreach ( $feed_items as $feed ) {    
                if (--$max >= 0) {
                    $list .= " <tr><td><a href='".$feed->get_permalink()."'>".$feed->get_title()."</a> </td></tr>";}
            }            
        }
        return $list."</table>";
    }
    
    
    function jcorgcpbMakeErrorsHtml($errors,$type="error")
    {
        $class="jcorgberror";
        $title=__("Please correct the following errors","jcorgbot");
        if($type=="warnings") {
            $class="jcorgberror";
            $title=__("Please review the following Warnings","jcorgbot");
        }
        if($type=="warning1") {
            $class="jcorgbwarning";
            $title=__("Please review the following Warnings","jcorgbot");
        }
        $strCompiledHtmlList = "";
        if(is_array($errors) && count($errors)>0) {
                $strCompiledHtmlList.="<div class='$class' style='width:90% !important'>
                                        <div class='jcorgb-errors-title'>$title: </div><ol>";
                foreach($errors as $error) {
                      $strCompiledHtmlList.="<li>".$error."</li>";
                }
                $strCompiledHtmlList.="</ol></div>";
        return $strCompiledHtmlList;
        }
    }