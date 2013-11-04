<div class="wrap" style="margin-bottom:0;" xmlns="http://www.w3.org/1999/html">
    <h2>PMID Citations with Read Settings</h2>

    <form method="post" action="options.php">
        <?php settings_fields('pmidplus_options'); // adds nonce ?>
        <table class="pmidplus-options-table">
            <tr>
                <td>
                    Display abstract when hovering over citation
                </td>
                <td>
                    <label for="abstract_tooltip_on">on</label>
                    <input id="abstract_tooltip_on" name="pmidplus_options[abstract_tooltip]" type="radio"
                           value="true" <?php echo $pmidplus_options['abstract_tooltip'] ? 'checked="true"' : ''; ?> />
                    <label for="abstract_tooltip_off">off</label>
                    <input id="abstract_tooltip_off" name="pmidplus_options[abstract_tooltip]" type="radio"
                           value="false" <?php echo !$pmidplus_options['abstract_tooltip'] ? 'checked="true"' : ''; ?> />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="abstract_tooltip_length">Length of abstract in tooltip (in characters)</label>
                </td>
                <td>
                    <input id="abstract_tooltip_length" name="pmidplus_options[abstract_tooltip_length]" type="number"
                           value="<?php echo $pmidplus_options['abstract_tooltip_length']; ?>" />
                </td>
                <td>
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td>
                    Add target=&quot;_blank&quot; to links in bibliography
                </td>
                <td>
                    <label for="targetblank_on">on</label>
                    <input id="targetblank_on" name="pmidplus_options[targetblank]" type="radio"
                           value="true" <?php echo $pmidplus_options['targetblank'] ? 'checked="true"' : ''; ?>" />
                    <label for="targetblank_off">off</label>
                    <input id="targetblank_off" name="pmidplus_options[targetblank]" type="radio"
                           value="false" <?php echo !$pmidplus_options['targetblank'] ? 'checked="true"' : ''; ?>"/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="replace_with_button">Replace [PubMed] [Read by QxMD] with "Open in Read" button.</label>
                </td>
                <td>
                    <label for="replace_with_button_on">on</label>
                    <input id="replace_with_button_on" name="pmidplus_options[replace_with_button]" type="radio"
                           value="true" <?php echo $pmidplus_options['replace_with_button'] ? 'checked="true"' : ''; ?> />
                    <label for="replace_with_button_off">off</label>
                    <input id="replace_with_button_off" name="pmidplus_options[replace_with_button]" type="radio"
                           value="false" <?php echo !$pmidplus_options['replace_with_button'] ? 'checked="true"' : ''; ?> />
                </td>
                <td>
                    <img src="/wp-content/plugins/pmid-citations-with-read/images/Open-In-Read-2.png" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="numbered_references_list">Numbered References</label>
                </td>
                <td>
                    <label for="numbered_references_list_on">on</label>
                    <input id="numbered_references_list_on" name="pmidplus_options[numbered_references_list]" type="radio"
                           value="true" <?php echo $pmidplus_options['numbered_references_list'] ? 'checked="true"' : ''; ?> />
                    <label for="numbered_references_list_off">off</label>
                    <input id="numbered_references_list_off" name="pmidplus_options[numbered_references_list]" type="radio"
                           value="false" <?php echo !$pmidplus_options['numbered_references_list'] ? 'checked="true"' : ''; ?> />
                </td>
                <td>
                    &nbsp;
                </td>
            </tr>            
        </table>
        <p class="submit"><input type="Submit" name="submit" class="button" value="<?php esc_attr_e('Save Changes'); ?>"/></p>
    </form>
    <a href="mailto:contact@qxmd.com">Feedback or feature requests</a> for this plugin?
    <br />
    <br />
    <p>
        This plugin is designed to integrate with the <a href="http://readbyqxmd.com" target="_blank">Read by QxMD</a> service so that visitors to your site can get easy full text access to the references you include.
    </p>
    <p>
        <a href="https://itunes.apple.com/app/read-personalized-medical/id574041839?mt=8" target="_blank"><img src="http://www.qxmd.com/wp-content/uploads/2012/11/Download_on_the_App_Store_Badge_US-UK_135x40_0824.png"> </a>
    </p>

    <p>
        Follow us:
    </p>

    <p>
        <a href="https://www.facebook.com/QxMD.Medical.Apps" target="_blank"><img src="http://www.qxmd.com/wp-content/uploads/2012/12/f_logo_48X48.png" />
    </p>

    <p>
        <a href="http://www.twitter.com/qxmd" target="_blank"><img src="http://qxmd.com/wp-content/uploads/2010/03/twitter_logo.png"></a>
    </p>
    </div></div>
