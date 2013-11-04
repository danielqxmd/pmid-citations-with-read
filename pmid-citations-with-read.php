<?php
/*
Plugin Name: PMID Citations with Read
Plugin URI: http://qxmd.com/pmid-citations-with-Read
Version: 1.0
Author: QxMD
Author URI: http://QxMD.com/
Description: This plugin makes it simple to include biomedical citations in the body of your post/page, as a reference section at the bottom of your page/page and provide full text access on web/mobile via the Read by QxMD service.
*/

add_action('wp_enqueue_scripts', 'enqueue_pmid_scripts');
add_action('admin_init', 'pmidplus_add_meta');
add_action('save_post', 'pmidplus_save_postdata'); // Execute save function on save.
add_filter('the_content', 'pmidplus_append_bibliography', 9);
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'pmid_settings_link' );
add_shortcode('PMID', 'shortcode_cite');

$pmidplus_options = get_option('pmidplus_options', false);
// Set some defaults options for settings page.
if (!$pmidplus_options or (count($pmidplus_options) < 4)) {
    $pmidplus_options = array(
        'abstract_tooltip' => false,
        'abstract_tooltip_length' => 450,
        'open_with_read' => true,
        'targetblank' => true,
        "replace_with_button" => false,
        "numbered_references_list" => false
    );
}

function pmid_settings_link($links) { 
  $settings_link = '<a href="/wp-admin/admin.php?page=pmid-citations-with-read/includes/pmidplus-settings.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

function shortcode_cite($attrs, $contents = null) {
    global $post;
    if(!isset($contents) || $contents == null || $contents == "" || intval($contents) <= 0 )
    {
        return "<b>PMID:Wrong Shortcode Contents</b>";
    }

    $post->during_shortcode = 1;
    $processedarray = get_post_meta($post->ID, '_pcp_article_sources_shortcode', true);
    $pmidarray = array();
    foreach ($processedarray as $key => $arr) {
        if(clean_pmid($arr['pmid']) == $contents) {
            $pmidarray[] = $arr;
            break;
        }
    }
    if(sizeof($pmidarray) > 0) {
        $result = build_references_html($pmidarray);
    } else {
        $result = "<b>Wrong PMID provided in shortcode: " . $contents."</b>";
    }
    $post->during_shortcode = 0;
    return $result;

}

// Add script necessary to have abstract in tooltip.
function enqueue_pmid_scripts()
{
    wp_enqueue_script('jquery');
    wp_register_script('jquery-tooltip', plugins_url('/js/jquery-tooltip/jquery.tooltip.js', __FILE__));
    wp_enqueue_script('jquery-tooltip');
    wp_register_style('jquery-tooltip', plugins_url('/js/jquery-tooltip/jquery.tooltip.css', __FILE__));
    wp_enqueue_style('jquery-tooltip');
    wp_register_style('pmidplus-style', plugins_url('/css/pmidplus.css', __FILE__));
    wp_enqueue_style('pmidplus-style');
}

// Grabs pubmed page from URL, pulls into string, parses out an array with: title, journal, issue, authors, institution.
function scrape_pmid_abstract($pubmedid)
{
    $ret_array = array();
    try {
        $request = wp_remote_get('http://www.ncbi.nlm.nih.gov/pubmed/' . $pubmedid);
        $pubmedpage = $request['body'];
        //TODO replace try/catch with is_wp_error()
        $ret_array['url'] = 'http://www.ncbi.nlm.nih.gov/pubmed/' . $pubmedid;
        preg_match('/<div class="cit">(?P<journal>.*?)<\/a>(?P<issue>.*?\.).*?<\/div>/', $pubmedpage, $matches);
        $ret_array['journal'] = strip_tags($matches['journal']);
        $ret_array['issue'] = trim($matches['issue']);
        $ret_array['pmid'] = $pubmedid;
        preg_match('/<h1>(?P<title>.+)<\/h1><div class="auths">(?P<authors>.*?)<\/div>/', $pubmedpage, $matches);
        $ret_array['title'] = $matches['title'];
        $ret_array['authors'] = strip_tags($matches['authors']);
        preg_match('/<div class="aff"><h3.*Source<\/h3><p>(?P<institution>.*?)<\/p>/', $pubmedpage, $matches);
        $ret_array['institution'] = $matches['institution'];
        preg_match('/<div class="abstr">.*?\<p\>(?P<abstract>.*?)\<\/p>/', $pubmedpage, $matches);
        $ret_array['abstract'] = strip_tags($matches['abstract']);

        return $ret_array;
    } catch (Exception $e) {
        return false;
    }
}

function clean_pmid($pmid) {
    if($pmid[0] == "@") {
        $str = substr($pmid, 1);
    } else {
        $str = $pmid;
    }
    return $str;
}

// Takes a comma separated list, like the one constructed from build_simple_pmid_string, and creates a multi-dimensional array of all of the information produced by the scrape_pmid_abstract.
function process_pmid_input($fieldinput)
{
    $pmidarray = preg_split("/[\s,]+/", $fieldinput, null, PREG_SPLIT_NO_EMPTY);
    foreach ($pmidarray as &$pmid) {
        $old_pmid = $pmid;
        $pmid = scrape_pmid_abstract(clean_pmid($pmid));

        if($old_pmid[0] == "@") {
            $pmid['pmid'] = "@".$pmid['pmid'];
        }
    }
    $pmidarray = array_filter($pmidarray); // remove entries wp_remote_get failed on
    return $pmidarray;
}

// Takes the processed input -- an array -- and builds a comma separated listed of PMIDs from it.
function build_simple_pmid_string($processedarray)
{
    //TODO: filter pmids with "@" sign at the beginning - they are shortcoded
    if (is_array($processedarray)) {
        foreach ($processedarray as &$citation) {
            $citation = $citation['pmid'];
        }
        $processedarray = implode(",", $processedarray);
        // die($processedarray);
        return $processedarray;
    } else {
        return false;
    }
}

// Takes an array, like that built from process_pmid_input, and returns it as a string.
function build_references_html($processedarray)
{
    global $pmidplus_options;
    global $post;
    ob_start();

    $cssclass = "";
    if(!isset($post->during_shortcode) || $post->during_shortcode != 1)
        {
            echo "<h1>References</h1>";
        } else {
            $cssclass = " shortcode";
        }
    ?>
<div class="pmidcitationplus<?=$cssclass;?>">
    <div>
        <?php
        $i = 0;
        foreach ($processedarray as $singlecitation) {
            $i++;
            // echo "<li id=\"cit" . $singlecitation['pmid'] . "\">";
            if($pmidplus_options["replace_with_button"]) {
                echo '<div class="open_with_read">'.
                '&nbsp;<a href="http://qxmd.com/r/'. clean_pmid($singlecitation['pmid'])."\" class=\"open_with_read\" {$targetblank}>".
                    '<img src="/wp-content/plugins/pmid-citations-with-read/images/Open-In-Read-2.png">'.
                '</a></div>';
            }
            echo '<div><div class="quote_text">';
            if($pmidplus_options["numbered_references_list"] && 
                (!isset($post->during_shortcode) || $post->during_shortcode != 1)
               ){
                echo '<div class="number">'.$i.'.</div>';
            }
            $targetblank = $pmidplus_options["targetblank"] ? ' target="_blank"' : '';
            $openwithread = !$pmidplus_options["replace_with_button"] ? 
                " <a href=\"http://www.ncbi.nlm.nih.gov/pubmed/".clean_pmid($singlecitation['pmid'])."\"{$targetblank}>[PubMed] </a><a href=\"http://qxmd.com/r/".clean_pmid($singlecitation['pmid'])."\"{$targetblank}>[Read by QxMD]</a>" 
                    : 
                    '';
            echo "{$singlecitation['authors']} <a href=\"http://qxmd.com/r/".
                clean_pmid($singlecitation['pmid']).
                "\"{$targetblank}>{$singlecitation['title']}</a> {$singlecitation['journal']} {$singlecitation['issue']} " . 
                'PMID: ' . '<a href="http://qxmd.com/r/'. clean_pmid($singlecitation['pmid']). "\"{$targetblank}>" . 
                clean_pmid($singlecitation['pmid']) . '</a>.'.
                $openwithread;
            if ((strlen($singlecitation['abstract']) > 0) and $pmidplus_options['abstract_tooltip']) {
                echo '
                    <span style="display:none;" class="abstr">
                    ' . substr(trim($singlecitation['abstract']), 0, 
                        $pmidplus_options['abstract_tooltip_length']) . ' [...]
                    </span>
                    <script type="text/javascript">
                    jQuery(document).ready(function() {
                    jQuery("#cit' . $singlecitation['pmid'] . '").tooltip({
                        bodyHandler: function() {
                            return jQuery("#cit' . $singlecitation['pmid'] . ' .abstr").text();
                        },
                        showURL: false
                    });
                    });
                    </script>';
            }
            echo '</div>';

            echo "</div>";
        }
        ?></div>
</div>
<?php
    return ob_get_clean();
}

// Called to fill the new meta box about to be created.
function pmidplus_input_fields()
{
    global $post;
    wp_nonce_field(plugin_basename(__FILE__), 'pmidplus_nonce');
    // The actual fields for data entry
    echo '<label for="pmidinput">Comma separated list of PMIDs</label>';
    echo '<textarea id="pmidinput" name="pmidinput" value="' . build_simple_pmid_string(get_post_meta($post->ID, '_pcp_article_sources', true)) . '" rows="6" cols="35">' . build_simple_pmid_string(get_post_meta($post->ID, '_pcp_article_sources', true)) . '</textarea>';
    echo '<label>To insert a citation anywhere in your post/page, use shortcode [PMID] insert-PMID-here [/PMID]</label>';

}

//SYNTAX: add_meta_box( $id, $title, $callback, $page, $context, $priority, $callback_args ); 
function pmidplus_add_meta()
{
    add_meta_box("pmidplusmeta", "PMID Citations with Read", 
        "pmidplus_input_fields", "post", "side", "high", $post);
    add_meta_box("pmidplusmeta", "PMID Citations with Read", 
        "pmidplus_input_fields", "page", "side", "high", $post);
}

function pmidplus_save_postdata($post_id)
{
    global $post;
    // Make sure save is intentional, not just autosave.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;

    // Verify this came from the our screen and with proper authorization
    if (!wp_verify_nonce($_POST['pmidplus_nonce'], plugin_basename(__FILE__)))
        return $post_id;

    // Check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
    } else {
        if (!current_user_can('edit_post', $post_id))
            return $post_id;
    }
    if($post_id!=$post->ID) {
        $post_id = $post->ID;
    }
    //process present shortcodes and add pmids from them
    if(has_shortcode($_POST['content'], 'PMID')) {
        preg_match_all("/\[PMID\](.*)\[\/PMID\]/", $_POST['content'], $matches);
        $pmids=array();
        foreach ($matches[1] as $key => $value) {
            $pmids[] = "@".$value;
        }

        if (build_simple_pmid_string(get_post_meta($post_id, '_pcp_article_sources_shortcode', 
                    true)) != join(",", $pmids) && $_POST['wp-preview'] != 'dopreview') {

            $neverusedbefore = process_pmid_input(join(",", $pmids));
            if (!update_post_meta($post_id, '_pcp_article_sources_shortcode', $neverusedbefore)) {
                die('Unable to post PMID Citations with Read update to meta.');
            }
        }
        
    }

// So far so good. Now we need to save the data. Only do it if the field doesn't match.
    if (empty($_POST['pmidinput'])) {
        delete_post_meta($post_id, '_pcp_article_sources');
    } else
    if (build_simple_pmid_string(get_post_meta($post_id, '_pcp_article_sources', true)) != $_POST['pmidinput'] 
        && $_POST['wp-preview'] != 'dopreview') {
        // Take the form input, scrape the info from the pubmed pages, output multidimensional array, and save update.
        $neverusedbefore = process_pmid_input($_POST['pmidinput']);
        if (!update_post_meta($post_id, '_pcp_article_sources', $neverusedbefore)) {
            die('Unable to post PMID Citations with Read update to meta.');
        }

    }
}

// Adds references to the bottom of posts
function pmidplus_append_bibliography($contentofpost)
{
    global $post;
    if (get_post_meta($post->ID, '_pcp_article_sources', true))
        $contentofpost .= build_references_html(get_post_meta($post->ID, '_pcp_article_sources', true));
    return $contentofpost;
}

/******************** below this point, admin area code **********************/

if (is_admin()) {
    // Show the PMID Citations with Read option under the Settings section.
    add_action('admin_menu', 'pmidplus_admin_menu', 9);
    add_action('admin_init', 'register_pmidplus_settings', 9);
    // Show nag screen asking to rate plugin (first time only).
    add_action('admin_notices', 'pmidplus_rate_plugin_notice');
    add_action('admin_enqueue_scripts', 'pmidplus_enqueue_admin_scripts');
    wp_register_style('pmidplus-style', plugins_url('/css/pmidplus.css', __FILE__));
}

function pmidplus_enqueue_admin_scripts()
{
    wp_enqueue_style('pmidplus-style');
}

function pmidplus_admin_menu()
{
    // TODO make an actual icon instead of using my personal gravatar
    $icon = '/wp-content/plugins/pmid-citations-with-read/images/icon.png';
    add_object_page('PMID Citations with Read', 'Read by QxMD', 'edit_theme_options', 
        'pmid-citations-with-read/includes/pmidplus-settings.php', '', $icon, 79);
}

function register_pmidplus_settings()
{
    register_setting('pmidplus_options', 'pmidplus_options', 'pmidplus_options_sanitization');
}

function pmidplus_rate_plugin_notice()
{
    if ($_GET['dismiss_rate_notice'] == '1') {
        update_option('pmidplus_rate_notice_dismissed', '1');
    } elseif ($_GET['remind_rate_later'] == '1') {
        update_option('pmidplus_reminder_date', strtotime('+10 days'));
    } else {
        // If no dismiss & no reminder, this is fresh install. Lets give it a few days before nagging.
        update_option('pmidplus_reminder_date', strtotime('+3 days'));
    }

    $rateNoticeDismissed = get_option('pmidplus_rate_notice_dismissed');
    $reminderDate = get_option('pmidplus_reminder_date');
    if (!$rateNoticeDismissed && (!$reminderDate || ($reminderDate < strtotime('now')))) {
        ?>
    <div id="pmidplus-rating-reminder" class="updated"><p>
        Hey, you've been using <a href="admin.php?page=pmid-citations-with-read/includes/pmidplus-settings.php">PMID Citations with Read</a>
        for a while. Will you please take a moment to rate it? <br/><br/><a
        href="http://wordpress.org/extend/plugins/pmid-citations-with-read" target="_blank" onclick="jQuery.ajax({'type': 'get', 'url':'options-general.php?page=pmidplus_options&dismiss_rate_notice=1'});">sure, i'll
        rate it right now</a>
        <a href="options-general.php?page=pmidplus_options&remind_rate_later=1" class="remind-later">remind me later</a>
    </p></div>
    <?php
    }
}

// ALL of the options array passes through this. This must be amended for new options.  
function pmidplus_options_sanitization($input)
{
    $safe = array();
    $input['abstract_tooltip_length'] = trim($input['abstract_tooltip_length']);

    if (preg_match('/[\d]+/', $input['abstract_tooltip_length'], $matches) and (intval($matches[0]) > 1)) {
        $safe['abstract_tooltip_length'] = intval($matches[0]);
    }

    foreach(array('abstract_tooltip', 'open_with_read', 'targetblank', 'replace_with_button', 'numbered_references_list') as $value) {
        if ($input[$value] == "true") {
            $safe[$value] = true;
        } else {
            $safe[$value] = false;
        }
    }

    return $safe;
}

?>
