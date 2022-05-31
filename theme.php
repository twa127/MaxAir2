<?php
/*
             __  __                             _
            |  \/  |                    /\     (_)
            | \  / |   __ _  __  __    /  \     _   _ __
            | |\/| |  / _` | \ \/ /   / /\ \   | | |  __|
            | |  | | | (_| |  >  <   / ____ \  | | | |
            |_|  |_|  \__,_| /_/\_\ /_/    \_\ |_| |_|

                   S M A R T   T H E R M O S T A T

*************************************************************************"
* MaxAir is a Linux based Central Heating Control systems. It runs from *"
* a web interface and it comes with ABSOLUTELY NO WARRANTY, to the      *"
* extent permitted by applicable law. I take no responsibility for any  *"
* loss or damage to you or your property.                               *"
* DO NOT MAKE ANY CHANGES TO YOUR HEATING SYSTEM UNTILL UNLESS YOU KNOW *"
* WHAT YOU ARE DOING                                                    *"
*************************************************************************"
*/
require_once(__DIR__.'/st_inc/session.php');
confirm_logged_in();
require_once(__DIR__.'/st_inc/connection.php');
require_once(__DIR__.'/st_inc/functions.php');

$date_time = date('Y-m-d H:i:s');
$theme = settings($conn, 'theme');

if(isset($_GET['id'])) {
	$id = $_GET['id'];
} else {
	$id = 0;
}
//Form submit
if (isset($_POST['submit'])) {
	$name = $_POST['name'];
        $justification = $_POST['justification'];
	$background_color = $_POST['background_color'];
        $text_color = $_POST['text_color'];
        $border_color = $_POST['border_color'];
        $footer_color = $_POST['footer_color'];
        $btn_style = $_POST['btn_style'];
        $btn_primary = $_POST['btn_primary'];
        $sync = '0';
        $purge= '0';

	//Add or Edit
	$query = "INSERT INTO `theme` (`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`) VALUES ('{$id}', '{$sync}', '{$purge}', '{$name}', '{$justification}', '{$background_color}', '{$text_color}', '{$border_color}', '{$footer_color}', '{$btn_style}', '{$btn_primary}') ON DUPLICATE KEY UPDATE sync=VALUES(sync), `purge`=VALUES(`purge`), name=VALUES(name), row_justification='{$justification}', background_color='{$background_color}', text_color='{$text_color}', border_color='{$border_color}', footer_color='{$footer_color}', btn_style='{$btn_style}', btn_primary='{$btn_primmary}';";
	$result = $conn->query($query);
        $temp_id = mysqli_insert_id($conn);
	if ($result) {
                if ($id==0){
                        $message_success = "<p>".$lang['theme_record_add_success']."</p>";
                } else {
                        $message_success = "<p>".$lang['theme_record_update_success']."</p>";
                }
	} else {
		$error = "<p>".$lang['theme_record_fail']." </p> <p>" .mysqli_error($conn). "</p>";
	}
	$message_success .= "<p>".$lang['do_not_refresh']."</p>";
	header("Refresh: 10; url=home.php");
	// After update on all required tables, set $id to mysqli_insert_id.
	if ($id==0){$id=$temp_id;}
}
?>
<!-- ### Visible Page ### -->
<?php include("header.php");  ?>
<?php include_once("notice.php"); ?>

<!-- Don't display form after submit -->
<?php if (!(isset($_POST['submit']))) { ?>

<!-- If the request is to EDIT, retrieve selected items from DB   -->
<?php if ($id != 0) {
        $query = "SELECT * FROM `theme` WHERE `id` = {$id} limit 1;";
	$result = $conn->query($query);
	$row = mysqli_fetch_assoc($result);
}
?>

<!-- Title (e.g. Add Zone or Edit Zone) -->
<div class="container-fluid">
	<br>
        <div class="row">
        	<div class="col-lg-12">
                   	<div class="card <?php echo theme($conn, $theme, 'border_color'); ?>">
                        	<div class="card-header <?php echo theme($conn, $theme, 'text_color'); ?> <?php echo theme($conn, $theme, 'background_color'); ?>">
					<div class="d-flex justify-content-between">
						<div>
							<?php if ($id != 0) { echo $lang['edit_theme'] . ": " . $row['name']; }else{
                		            		echo '<i class="bi bi-plus-square" style="font-size: 1.2rem;"></i>&nbsp&nbsp'.$lang['add_theme'];} ?>
						</div>
						<div class="btn-group"><?php echo date("H:i"); ?></div>
					</div>
                        	</div>
                        	<!-- /.card-header -->
				<div class="card-body">
					<form data-bs-toggle="validator" role="form" method="post" action="<?php $_SERVER['PHP_SELF'];?>" id="form-join">
						<!-- Theme Name -->
						<div class="form-group" class="control-label"><label class="fs-6"><?php echo $lang['theme_name']; ?></label> <small class="text-muted"><?php echo $lang['theme_name_info'];?></small>
							<input class="form-control" placeholder="Theme Name" value="<?php if(isset($row['name'])) { echo $row['name']; } ?>" id="name" name="name" data-bs-error="<?php echo $lang['theme_name_help']; ?>" autocomplete="off" required>
							<div class="help-block with-errors"></div>
						</div>

						<!-- Justification -->
						<div class="form-group" class="control-label" id="justify_label" style="display:block"><label class="fs-6"><?php echo $lang['justify']; ?></label> <small class="text-muted"><?php echo $lang['justify_info'];?></small>
        						<select class="form-select" type="text" id="justification" name="justification" >
								<?php echo'<option value="left" ' . ($row['row_justification']=="left" ? 'selected' : '') . '>'.$lang['left'].'</option>'; ?>
                						<?php echo'<option value="center" ' . ($row['row_justification']=="center" ? 'selected' : '') . '>'.$lang['center'].'</option>'; ?>
                                                                <?php echo'<option value="right" ' . ($row['row_justification']=="right" ? 'selected' : '') . '>'.$lang['right'].'</option>'; ?>
							</select>
							<div class="help-block with-errors"></div>
						</div>

                                                <!-- Background Color -->
                                                <div class="form-group" class="control-label" id="background_color_label" style="display:block"><label class="fs-6"><?php echo $lang['background_color']; ?></label> <small class="text-muted"><?php echo $lang['background_color_info'];?></small>
                                                        <select class="form-select" type="text" id="background_color" name="background_color" >
						    		<?php echo'<option value="bg-red" ' . ($row['background_color']=="bg-red" ? 'selected' : '') . '>'.$lang['red'].'</option>'; ?>
                                                                <?php echo'<option value="bg-orange" ' . ($row['background_color']=="bg-orange" ? 'selected' : '') . '>'.$lang['orange'].'</option>'; ?>
		                                                <?php echo'<option value="bg-amber" ' . ($row['background_color']=="bg-amber" ? 'selected' : '') . '>'.$lang['amber'].'</option>'; ?>
                               					<?php echo'<option value="bg-blue" ' . ($row['background_color']=="bg-blue" ? 'selected' : '') . '>'.$lang['blue'].'</option>'; ?>
		                                                <?php echo'<option value="bg-violet" ' . ($row['background_color']=="bg-violet" ? 'selected' : '') . '>'.$lang['violet'].'</option>'; ?>
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                </div>

                                                <!-- Text Color -->
                                                <div class="form-group" class="control-label" id="text_color_label" style="display:block"><label class="fs-6"><?php echo $lang['text_color']; ?></label> <small class="text-muted"><?php echo $lang['text_color_info'];?></small>
                                                        <select class="form-select" type="text" id="text_color" name="text_color" >
                                                                <?php echo'<option value="text-white" ' . ($row['text_color']=="text-white" ? 'selected' : '') . '>'.$lang['white'].'</option>'; ?>
                                                                <?php echo'<option value="text-dark" ' . ($row['background_color']=="text-dark" ? 'selected' : '') . '>'.$lang['black'].'</option>'; ?>
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                </div>


                                                <!-- Boarder Color -->
                                                <div class="form-group" class="control-label" id="border_color_label" style="display:block"><label class="fs-6"><?php echo $lang['border_color']; ?></label> <small class="text-muted"><?php echo $lang['border_color_info'];?></small>
                                                        <select class="form-select" type="text" id="border_color" name="border_color" >
				                         	<?php echo'<option value="border-red" ' . ($row['border_red']=="border-red" ? 'selected' : '') . '>'.$lang['red'].'</option>'; ?>
                                                                <?php echo'<option value="border-orange" ' . ($row['border_color']=="border-orange" ? 'selected' : '') . '>'.$lang['orange'].'</option>'; ?>
                                                  		<?php echo'<option value="order-amber" ' . ($row['border_color']=="border-amber" ? 'selected' : '') . '>'.$lang['amber'].'</option>'; ?>
								<?php echo'<option value="border-blue" ' . ($row['border_color']=="border-blue" ? 'selected' : '') . '>'.$lang['blue'].'</option>'; ?>
								<?php echo'<option value="border-violet" ' . ($row['border_color']=="border-violet" ? 'selected' : '') . '>'.$lang['violet'].'</option>'; ?>
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                </div>

                                                <!-- Footer Color -->
                                                <div class="form-group" class="control-label" id="footer_color_label" style="display:block"><label class="fs-6"><?php echo $lang['footer_color']; ?></label> <small class="text-muted"><?php echo $lang['footer_color_info'];?></small>
                                                        <select class="form-select" type="text" id="footer_color" name="footer_color" >
                                    				<?php echo'<option value="card-footer-red" ' . ($row['footer-red']=="card-footer-red" ? 'selected' : '') . '>'.$lang['red'].'</option>'; ?>
                                                                <?php echo'<option value="card-footer-orange" ' . ($row['footer_color']=="card-footer-orange" ? 'selected' : '') . '>'.$lang['orange'].'</option>'; ?>
                                                  		<?php echo'<option value="card-footer-amber" ' . ($row['footer_color']=="card-footer-amber" ? 'selected' : '') . '>'.$lang['amber'].'</option>'; ?>
								<?php echo'<option value="card-footer-blue" ' . ($row['footer_color']=="card-footer-blue" ? 'selected' : '') . '>'.$lang['blue'].'</option>'; ?>
								<?php echo'<option value="card-footer-violet" ' . ($row['footer_color']=="card-footer-violet" ? 'selected' : '') . '>'.$lang['violet'].'</option>'; ?>
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                </div>

                                                <!-- Button Style -->
                                                <div class="form-group" class="control-label" id="btn_style_label" style="display:block"><label class="fs-6"><?php echo $lang['button_style']; ?></label> <small class="text-muted"><?php echo $lang['button_style_info'];?></small>
                                                        <select class="form-select" type="text" id="btn_style" name="btn_style" >
								<?php echo'<option value="btn-bm-red" ' . ($row['btn_style']=="btn-bm-red" ? 'selected' : '') . '>'.$lang['red'].'</option>'; ?>
								<?php echo'<option value="btn-bm-orange" ' . ($row['btn_style']=="btn-bm-orange" ? 'selected' : '') . '>'.$lang['orange'].'</option>'; ?>
								<?php echo'<option value="btn-bm-amber" ' . ($row['btn_style']=="btn-bm-amber" ? 'selected' : '') . '>'.$lang['amber'].'</option>'; ?>
								<?php echo'<option value="btn-bm-blue" ' . ($row['btn_style']=="btn-bm-blue" ? 'selected' : '') . '>'.$lang['blue'].'</option>'; ?>
								<?php echo'<option value="btn-bm-violet" ' . ($row['btn_style']=="btn-bm-violet" ? 'selected' : '') . '>'.$lang['violet'].'</option>'; ?>
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                </div>

                                                <!-- Button Primary -->
                                                <div class="form-group" class="control-label" id="btn_primary_label" style="display:block"><label class="fs-6"><?php echo $lang['button_primary']; ?></label> <small class="text-muted"><?php echo $lang['button_primary_info'];?></small>
                                                        <select class="form-select" type="text" id="btn_primary" name="btn_primary" >
								<?php echo'<option value="btn-primary-red" ' . ($row['btn_style']=="btn-primary-red" ? 'selected' : '') . '>'.$lang['red'].'</option>'; ?>
								<?php echo'<option value="btn-primary-orange" ' . ($row['btn_style']=="btn-primary-orange" ? 'selected' : '') . '>'.$lang['orange'].'</option>'; ?>
								<?php echo'<option value="btn-primary-amber" ' . ($row['btn_style']=="btn-primary-amber" ? 'selected' : '') . '>'.$lang['amber'].'</option>'; ?>
								<?php echo'<option value="btn-primary-blue" ' . ($row['btn_style']=="btn-primary-blue" ? 'selected' : '') . '>'.$lang['blue'].'</option>'; ?>
								<?php echo'<option value="btn-primary-violet" ' . ($row['btn_style']=="btn-primary-violet" ? 'selected' : '') . '>'.$lang['violet'].'</option>'; ?>
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                </div>

						<!-- Buttons -->
						<input type="submit" name="submit" value="<?php echo $lang['submit']; ?>" class="btn <?php echo theme($conn, $theme, 'btn_style'); ?> btn-sm">
						<a href="home.php"><button type="button" class="btn <?php echo theme($conn, $theme, 'btn_style'); ?> btn-sm"><?php echo $lang['cancel']; ?></button></a>
					</form>
					<!-- /.form -->
				</div>
                        	<!-- /.card-body -->
				<div class="card-footer <?php echo theme($conn, $theme, 'footer_color'); ?>">
					<div class="text-start">
						<?php
						ShowWeather($conn);
						?>
                            		</div>
                        	</div>
				<!-- /.card-footer -->
                    	</div>
			<!-- /.card -->
		</div>
                <!-- /.col-lg-4 -->
	</div>
        <!-- /.row -->
</div>
<!-- /#container -->
<?php }  ?>
<?php include("footer.php");  ?>

