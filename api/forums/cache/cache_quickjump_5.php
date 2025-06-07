<?php

if (!defined('PUN')) exit;
define('PUN_QJ_LOADED', 1);
$forum_id = isset($forum_id) ? $forum_id : 0;

?>				<form id="qjump" method="get" action="viewforum.php">
					<div><label><span><?php echo $lang_common['Jump to'] ?><br /></span>
					<select name="id" onchange="window.location=('viewforum.php?id='+this.options[this.selectedIndex].value)">
						<optgroup label="General">
							<option value="10"<?php echo ($forum_id == 10) ? ' selected="selected"' : '' ?>>Announcements</option>
							<option value="11"<?php echo ($forum_id == 11) ? ' selected="selected"' : '' ?>>General talk</option>
							<option value="14"<?php echo ($forum_id == 14) ? ' selected="selected"' : '' ?>>Spotlight</option>
						</optgroup>
						<optgroup label="Premium">
							<option value="15"<?php echo ($forum_id == 15) ? ' selected="selected"' : '' ?>>CS:GO Discussion</option>
							<option value="12"<?php echo ($forum_id == 12) ? ' selected="selected"' : '' ?>>Feedback</option>
							<option value="13"<?php echo ($forum_id == 13) ? ' selected="selected"' : '' ?>>Lua Workshop</option>
							<option value="16"<?php echo ($forum_id == 16) ? ' selected="selected"' : '' ?>>CS:GO Lua Scripts</option>
							<option value="17"<?php echo ($forum_id == 17) ? ' selected="selected"' : '' ?>>MarketPlace</option>
						</optgroup>
					</select></label>
					<input type="submit" value="<?php echo $lang_common['Go'] ?>" accesskey="g" />
					</div>
				</form>
