<?php init_head(); ?>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body" style="overflow-x: auto;">
						<div class="dt-loader hide"></div>
						<?php $this->load->view('admin/utilities/calendar_filters'); ?>
						<div id="calendar"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php  $this->load->view('admin/utilities/calendar_template'); ?>

<script>
google_api = '<?php echo $google_api_key; ?>';
calendarIDs = '<?php echo json_encode($google_ids_calendars); ?>';

$(document).ready(function () {

}); // end of document ready

</script>

<?php init_tail(); ?>
</body>
</html>
