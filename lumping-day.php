<?php
/*
Plugin Name:Lumping-day
Plugin URI: 
Description:I update the date of the draft article collectively
Version: 1.0
Author: yukimaru222
Author URI: http://tool.potalstyle.net/
License: GPL2
*/

/*
Copyright 2017 yukimaru (email:tool@potalstyle.net)
 
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action( 'plugins_loaded', 'ykmrlp_load_textdomain' );
function ykmrlp_load_textdomain() {
    load_plugin_textdomain( 'lumping-day', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
}


//menu
function ykmrlumping_day() {
	add_posts_page('lumping_day', 'lumping_day', 'level_8', 'ykmrlumping_day_page', 'ykmrlumping_day_page');
}

//menuhook
add_action( 'admin_menu', 'ykmrlumping_day' );

//page
function ykmrlumping_day_page(){
?>

<?php

    $now = current_time('Y-m-d H:i:s');
	$nowutc = gmdate( 'Y-m-d H:i:s' );	
    $now2 = date('Y');
	$diff_hour = (strtotime($nowutc) - strtotime($now)) / 3600;



    global $wpdb; 
  
    if (isset($_POST["sendpost"])) {

	check_admin_referer('formnonce');
        $ld_yy = htmlspecialchars($_POST["ld_yy"],ENT_QUOTES);

        if (empty($_POST['lumpingcheck']) || empty($ld_yy)) {
        
        } else {
    
        $ld_yy = htmlspecialchars($_POST["ld_yy"],ENT_QUOTES);
        $ld_mm = htmlspecialchars($_POST["ld_mm"],ENT_QUOTES);
        $ld_mm = sprintf("%02d", $ld_mm);
        
        $ld_dd = htmlspecialchars($_POST["ld_dd"],ENT_QUOTES);
        $ld_dd = sprintf("%02d", $ld_dd);
        
        $ld_hh = htmlspecialchars($_POST["ld_hh"],ENT_QUOTES);
		$ld_hhgmt = $ld_hh + $diff_hour;
		$ld_hhgmt = sprintf("%02d", $ld_hhgmt);
		$ld_hh = sprintf("%02d", $ld_hh);
        
        $ld_m = htmlspecialchars($_POST["ld_m"],ENT_QUOTES);
        $ld_m = sprintf("%02d", $ld_m);
  
        $ld_day = $ld_yy.'-'.$ld_mm.'-'.$ld_dd.' '.$ld_hh.':'.$ld_m.':00';
		$ld_day02 = $ld_yy.'-'.$ld_mm.'-'.$ld_dd.' '.$ld_hhgmt.':'.$ld_m.':00';

	$lumpingcheck = array_map('htmlspecialchars', $_POST['lumpingcheck']); 
            foreach($lumpingcheck as $key => $value) {
                $lumpingcheck[$key] = htmlspecialchars($value,ENT_QUOTES);
                $lpid = $lumpingcheck[$key];
        
                $wpdb->update(
                    $wpdb->posts,
                 
                    array(
                        'post_date' => $ld_day,
                        'post_date_gmt' => $ld_day02,
                        'post_modified' => $now,
                        'post_modified_gmt' => $nowutc
                    ),
                    array(
                         'ID' => $lpid,
                    ),
                    array('%s'),
                    array('%d')
                );			
            }//endforech
    
        }//end chk
    }//endif
    
    
    echo _e('<h1>lumping-day</h1>' , 'lumping-day');
?>

	<p>
	<?php echo _e('I can update the date of the draft article that is in a state collectively.' , 'lumping-day'); ?>
	</p>



    
    <?php
    $sql = $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_status = %s",'draft' );
    $sql = $wpdb->get_results($sql);
    
    if (empty($sql)) {
	echo _e('<p>There is not the article of the draft</p>' , 'lumping-day');
   
    }else{
    
        ?>
    
        <form method="post" action="" >
        <input type="text" name="ld_yy" onKeyup="this.value=this.value.replace(/[^0-9]+/,'')" value="<?php echo $now2; ?>" size="4"  pattern="\d{4}" /><?php echo _e('Year' , 'lumping-day'); ?>
        
        <select name="ld_mm">
        <?php for ($i = 1; $i <= 12; $i++) : ?>
        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php endfor; ?>
        </select><?php echo _e('Month' , 'lumping-day'); ?>
        
        <select name="ld_dd">
        <?php for ($i = 1; $i <= 31; $i++) : ?>
        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php endfor; ?>
        </select><?php echo _e('Day' , 'lumping-day'); ?>
        
        <select name="ld_hh">
        <?php for ($i = 0; $i <= 23; $i++) : ?>
        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php endfor; ?>
        </select><?php echo _e('Hour' , 'lumping-day'); ?>
        
        <select name="ld_m">
        <?php for ($i = 0; $i <= 59; $i++) : ?>
        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php endfor; ?>
        </select><?php echo _e('Minute' , 'lumping-day'); ?>
        
        <p><?php echo _e('now time' , 'lumping-day'); ?>&nbsp;<?php echo $now;?></p>
        

        <?php
        if (isset($_POST["sendpost"])) {
            if (empty($_POST['lumpingcheck'])) {
            echo _e('<p style="color:#FF0000;">Please let a check enter to at least one</p>' , 'lumping-day');

            }
        
            if (empty($_POST["ld_yy"])) {
			echo _e('<p style="color:#FF0000;">Please enter in the calendar year.</p>' , 'lumping-day');

            }
        
            if (!empty($_POST['lumpingcheck']) and !empty($_POST["ld_yy"])){
			echo _e('<p style="color:#FF0000;">I updated a date!</p>' , 'lumping-day');

            }

        }//chkmess
        ?>


	<p>
	<?php echo _e('Article list of drafts.' , 'lumping-day'); ?>
	</p>        
        
        
        <ul class="ldtb">
        
            <li>
            <label style="padding-left:30px;">
            &nbsp;
		<span>ID</span>
		<?php echo _e('Day' , 'lumping-day'); ?>&nbsp;
		<?php echo _e('Title' , 'lumping-day'); ?>     
            </label>
            </li>
    
            <?php
            foreach ($sql as $value){
                $chk = 0;
                $id = $value->ID;
                $title = $value->post_title;
                $day = $value->post_date;
                ?>
        
                <li>
                <label>
                <input type="checkbox" name="lumpingcheck[]" value="<?php echo $id; ?>" />
                <span><?php echo $id; ?></span>
                <?php echo $day; ?>&nbsp;
                <?php echo $title; ?>     
                </label>
                </li>
                
            <?php
            }//endforeach
            ?>
        </ul>
    
        <p><input type="submit" name="sendpost" value="<?php echo _e('SEND' , 'lumping-day'); ?>  " class="formBtn" ></p>
	<?php wp_nonce_field('formnonce');?>    
        <?php
        }//end draft
        ?>
    
        </form>
    
<p style="padding-top:50px;"><?php echo _e('SITE' , 'lumping-day'); ?>:
<a href="http://tool.potalstyle.net/" target="_blank" rel="nofollow" >http://tool.potalstyle.net/</a>
</p>
   
    <style>
    .ldtb{
    width:100%;
    }
    
    .ldtb li{
    margin:0px;
    }
    
    .ldtb li label{
    display:block;
    padding:8px;
    border-bottom-width: 1px;
    border-bottom-style: solid;
    border-bottom-color: #CCCCCC;
    }
    
    .ldtb li label:hover{
        background-color: #FFFFDD;
    }
    
    .ldtb li span{
    width:50px;
    display:inline-block;
    }
    
    .ldtb li:nth-child(2n){
    background-color:#FFF;
    }
    
    </style>

<?php
} //function repost_page END
?>