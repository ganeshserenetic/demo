<?php if ($is_ajaxed) { ?>
<?php if ($total_rows > 0) { ?>
<div class="recorde_perpage">
	<div class="form-inline">
		<div class="form-group mr-2" id="DataTables_Table_0_length">
			<label for="user_type">Records Per Page </label>
		</div>
		<div class="form-group">
			<select size="1" id="pages" name="pages" onchange="$('#per_pages').val(this.value);
			ajax_submit();" class="form-control">
				<option value="10" <?php echo $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
				<option value="25" <?php echo $_GET['pages'] == 25 || $_GET['pages'] == "" ? 'selected' : ''; ?>>25</option>
				<option value="50" <?php echo $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
				<option value="100" <?php echo $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
			</select>
		</div>
	</div>
</div>
<?php } ?>
<div class="table-responsive">
	<table class="<?= $table_class ?>">
		<thead>
			<tr class="data-head">
				<th ><a href="javascript:void(0);" data-column="c.created_at" data-direction="<?php echo $SortBy == 'c.created_at' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Join Date</a></th>
				<th ><a href="javascript:void(0);" data-column="c.rep_id" data-direction="<?php echo $SortBy == 'c.rep_id' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Customer ID</a></th>
				<th ><a href="javascript:void(0);" data-column="c.fname" data-direction="<?php echo $SortBy == 'c.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Name</a></th>              
				<th ><a href="javascript:void(0);" data-column="c.email" data-direction="<?php echo $SortBy == 'c.email' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Email</a></th>
				<th ><a href="javascript:void(0);" data-column="s.fname" data-direction="<?php echo $SortBy == 's.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Sponsor</a></th>
				<th ><a href="javascript:void(0);" data-column="c.status" data-direction="<?php echo $SortBy == 'c.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
				
				<th width="180px">Actions</th> 
			</tr>
		</thead>         
		<tbody>
		<?php
		if ($total_rows > 0) {
		  foreach ($fetch_rows as $rows) {
			?>
			<tr>
				<td ><?php echo date($DATE_FORMAT, strtotime($rows['created_at'])); ?></td>
				<td ><a href="customer_detail.php?id=<?php echo $rows['id']; ?>" target="_blank"><?php echo $rows['rep_id']; ?></a></td>         
				<td><?php echo stripslashes($rows['fname'] . ' ' . $rows['lname']); ?></td>								
				<td><?php echo $rows['email']; ?></td>
				<td>
					<?php 
						$detail_url = '';
						if($rows['s_type'] == 'Customer'){
							$detail_url = 'customer_detail.php?id='.$rows['sponsor_id'];
						}
						else{
							$detail_url = 'partner_detail.php?id='.$rows['sponsor_id'];
						}
					?>
					<a data-toggle="tooltip" target="_blank" href="<?php echo $detail_url; ?>"   data-original-title="<?php echo $rows['sponsor_rep_id']; ?>"><?php echo $rows['s_fname'] . " " . $rows['s_lname']; ?></a>
				</td>
				<td>
					<select name="member_status" class="form-control" id="member_status_<?= $rows['id']; ?>"  onchange="return member_status('<?= $rows['id']; ?>', this.value, '<?= $rows['status']; ?>');" style="width:150px;" >
						<option value="Active"      <?php if ($rows['status'] == 'Active') echo "selected='selected'"; ?>>Active</option>
						<option value="Terminated"   <?php if ($rows['status'] == 'Terminated') echo "selected='selected'"; ?>>Terminated</option>
						<option value="Inactive"   <?php if ($rows['status'] == 'Inactive') echo "selected='selected'"; ?>>Inactive</option>
						<option value="Resigned"     <?php if ($rows['status'] == 'Resigned') echo "selected='selected'"; ?>>Resigned</option>
						<option value="Suspended" <?php if ($rows['status'] == 'Suspended') echo "selected='selected'"; ?>>Suspended</option>
						<option value="Chargeback" <?php if ($rows['status'] == 'Chargeback') echo "selected='selected'"; ?>>Chargeback</option>
						<option value="Fraud" <?php if ($rows['status'] == 'Fraud') echo "selected='selected'"; ?>>Fraud</option>
						<option value="Demoted" <?php if ($rows['status'] == 'Demoted') echo "selected='selected'"; ?>>Demoted</option>
					</select>
				</td>
				
				<td class="icons">                  
					<a href="customer_detail.php?id=<?php echo $rows['id']; ?>" target="_blank" data-toggle="tooltip" title="Edit"><i class="bx bx-edit"></i></a>
					<a href="mailto:<?php echo $rows['email']; ?>" data-toggle="tooltip" title="<?= (isset($rows['email']) ? $rows['email'] : '-'); ?>"><i class="bx bx-envelope"></i></a>		
					<?php if ($rows['cell_phone'] != "") { ?>
					<a href="tel:<?php echo "+1" . $rows['cell_phone'] ?>" data-toggle="tooltip" title="<?= (isset($rows['cell_phone']) ? "+1" . $rows['cell_phone'] : '-'); ?>"><i class="bx bx-phone"></i></a>
					<?php } ?>
					
					<a data-toggle="tooltip" target="_blank" href="<?php echo $UFORIABRANDS_HOST.'/' ?>switch_login.php?id=<?php echo md5($rows['id']); ?>&admin=<?php echo $_SESSION['admin']['id']?>&bypass" data-original-title="Access Customer Site"><i class="bx bx-lock"></i></a>

					<?php if (has_menu_access(78)) {?>	
					<?php if($rows['unilevel_placed'] == 'N' && $rows['id'] !=1 ) { ?>
						<a data-toggle="tooltip" class="upgrade_popup" target="_blank" href="change_personal_sponsor.php?id=<?=$rows['id']?>" data-original-title="Change Enrolling Sponsor"><i class="bx bxs-up-arrow-circle"></i></a>
					<?php } ?>
					<?php } ?>
				</td>
			</tr>
			<?php } ?>
			<?php } else { ?>
			<tr>
				<td colspan="7" class="text-center">No record(s) found</td>
			</tr>
			<?php } ?>
		</tbody>
		<?php if ($total_rows > 0) { ?>
		<tfoot>
			<tr>
				<td colspan="7">
				<?php echo $paginate->links_html; ?>
				</td>
			</tr>
		</tfoot>
		<?php } ?>
	</table>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(".upgrade_popup").colorbox({iframe: true, width: '600px', height: '400px'});
	});
</script>  
<?php } else { ?>
<?php include_once('notify.inc.php'); ?>  
<div class="card card-search">
	<div class="card-left">
		<ul>
			<li><a href="javascript:void(0)" class="search-btn show"><i class="bx bx-search"></i></a></li>
		</ul>
	</div>
	<div class="card-right">
		<div id="search_tab">
			<div class="card-header ">
				<div class="card-search-title">Search</div>
				<a href="add_user.php?user_type=Customer" class="btn btn-primary-o  rep_popup float-right"> Add New Customer</a>
				<div class="clearfix"></div>
			</div>
			<div class="collapse-wrap collapse show">
				<div class="card-body">
					<form id="frm_search" action="customers.php" method="GET" class="">
						<div class="row">
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Customer ID</label><input type="text" class="form-control" name="rep_id" value="<?php echo $rep_id ?>">
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>First Name</label><input type="text" class="form-control NotAllowNum" name="fname" value="<?php echo $fname ?>">
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Last Name</label><input type="text" class="form-control NotAllowNum" name="lname" value="<?php echo $lname ?>">
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Email</label><input type="text" class="form-control" name="email" value="<?php echo $email ?>">
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Username</label><input type="text" class="form-control" name="user_name" value="<?php echo $user_name; ?>">
								</div>
							</div>

							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Sponsor Id:</label><input type="text" class="form-control" name="sponsor_id" value="<?php echo $sponsor_id; ?>">
								</div>
							</div>

							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Sponsor Name:</label><input type="text" class="form-control NotAllowNum " name="sponsor_name" value="<?php echo $sponsor_name; ?>">
								</div>
							</div>

							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Network Sponsor Id :</label><input type="text" class="form-control" name="network_sponsor_id" value="<?php echo cx_isset($network_sponsor_id); ?>">
								</div>
							</div>


							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Phone</label><input type="text" class="form-control" name="phone" value="<?php echo $phone ?>">
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Shipping Phone</label><input type="text" class="form-control" name="shipping_phone" value="<?php echo $shipping_phone ?>">
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Billing Phone</label><input type="text" class="form-control" name="billing_phone" value="<?php echo $billing_phone ?>">
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Address</label><input type="text" class="form-control" name="address" value="<?php echo $address ?>">
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>City</label><input type="text" class="form-control NotAllowNum" name="city" value="<?php echo $city ?>">
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Country</label>
									<select id="country" name="country" class="form-control">
										<option value="">&nbsp;</option>
										<?php foreach ($_SETTING['product_country'] as $country_id => $value) { ?>
										<option data-id="<?php echo $country_id ?>" value="<?php echo $value['name']; ?>" <?php echo cx_isset($country) == $value['name'] ? "selected" : ""; ?>><?php echo $value['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>State</label>
									<select id="state" name="state" class="form-control">
										<option value="">&nbsp;</option>	
										<?php foreach($temp_state as $states){ ?>
											<option value="<?php echo $states['name'] ?>"><?php echo $states['name'] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Zip Code</label><input type="text" class="form-control" name="zip" value="<?php echo $zip ?>">
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Premium</label>
									<select id="premium" name="premium" class="form-control  " >
										<option value="">&nbsp;</option>
										<option value="Y" >Yes</option>
										<option value="N" >No</option>
									</select>
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Status</label>
									<select name="s_member_status" class="form-control  " id="s_member_status" >
										<option value="">&nbsp;</option>
										<option value="Active"      <?php if ($s_member_status == 'Active') echo "selected='selected'"; ?>>Active</option>
										<option value="Terminated"   <?php if ($s_member_status == 'Terminated') echo "selected='selected'"; ?>>Terminated</option>              
										<option value="Inactive"   <?php if ($s_member_status == 'Inactive') echo "selected='selected'"; ?>>Inactive</option>  
										<option value="Resigned"     <?php if ($s_member_status == 'Resigned') echo "selected='selected'"; ?>>Resigned</option>            
										<option value="Suspended" <?php if ($s_member_status == 'Suspended') echo "selected='selected'"; ?>>Suspended</option>
										<option value="Chargeback" <?php if ($s_member_status == 'Chargeback') echo "selected='selected'"; ?>>Chargeback</option>
										<option value="Fraud" <?php if ($s_member_status == 'Fraud') echo "selected='selected'"; ?>>Fraud</option>
										<option value="Suspended With Commission" <?php if ($s_member_status == 'Suspended With Commission') echo "selected='selected'"; ?>>Suspended With Commission</option>
										<option value="Demoted" <?php if ($s_member_status == 'Demoted') echo "selected='selected'"; ?>>Demoted</option>
									</select>
								</div>
							</div>

							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Tax Exemption</label>
									<select id="tax_exempted" name="tax_exempted" class="form-control  " >
										<option value="">&nbsp;</option>
										<option value="Y" <?php echo $tax_exempted == "Y" ? "selected" : ""; ?>>Yes</option>
										<option value="N" <?php echo $tax_exempted == "N" ? "selected" : ""; ?>>No</option>
									</select>
								</div>
							</div>

							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Quick Date</label>
									<select class="form-control  select2-offscreen" name="custom_date" id="custom_date">              
										<option value="">&nbsp;</option>
										<option value="Today" <?php
										if ($custom_date == "Today") {
										  echo 'selected';
										}
										?>>Today</option>
										<option value="Yesterday" <?php
										if ($custom_date == "Yesterday") {
										  echo 'selected';
										}
										?>>Yesterday</option>
										<option value="Last7Days" <?php
										if ($custom_date == "Last7Days") {
										  echo 'selected';
										}
										?>>Last 7 Days</option>
										<option value="ThisMonth" <?php
										if ($custom_date == "ThisMonth") {
										  echo 'selected';
										}
										?>>This Month</option>
										<option value="LastMonth" <?php
										if ($custom_date == "LastMonth") {
										  echo 'selected';
										}
										?>>Last Month</option>
										<option value="ThisYear" <?php
										if ($custom_date == "ThisYear") {
										  echo 'selected';
										}
										?>>This Year</option>
									</select>
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>From Date</label><input type="text" name="fromdate" id="fromdate" value="<?php echo $fromdate ?>" class="datetimepicker-range form-control">
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>To Date</label><input type="text" name="todate" id="todate" value="<?php echo $todate ?>" class="datetimepicker-range form-control">
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="form-group">
									<label>Old Uforia ID</label><input type="text" class="form-control" name="old_uforia_customer_id" value="<?php echo $old_uforia_customer_id ?>">
								</div>
							</div>
							<div class="col-sm-12">
								<button type="button" class="btn btn-primary mr-2" name="search" id="search" onclick="ajax_submit()">
									<i class="bx bx-search"></i> Search
								</button>

								<button type="button" class="btn btn-primary-o mr-2" name="viewall" id="viewall" value="viewall" onClick="window.location = 'customers.php'">
									<i class="bx bx-zoom-in"></i> View All
								</button>

								<button type="button" class="btn btn-primary-o" name="export_e" id="export_e" onclick="export_data('export_excel')">
									 Export
								</button>
							</div>
							<input type="hidden" name="export" id="export" value="">
							<input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
							<input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>" />
							<input type="hidden" name="sort" id="sort_column" value="<?= $SortBy; ?>" />
							<input type="hidden" name="direction" id="sort_direction" value="<?= $SortDirection; ?>" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="search-handle text-center">
		<a href="javascript:void(0);" class="btn-box-tool" data-action="collapse-list">
		  <i class="fa fa-minus"></i>
		</a>
	</div>
</div>
<div class="card list-data">
	<div class="card-body">
		<div id="ajax_loader" class="ajex_loader" style="display: none;">
			<div class="loader"></div>
		</div>
		<div id="ajax_data">
		</div>
	</div>
</div>
  
<script type="text/javascript">
	$(document).ready(function() {
		$(".rep_popup").colorbox({iframe: true, width: '900px', height: '500px'});

		$('.list-data').css('display','none');
		<?php if(isset($_GET['view']) && $_GET['view'] == 'viewall' ) { ?>
			$(".list-data").css('display','block');
			ajax_submit();
		<?php } ?>

		$(document).off('click', '#ajax_data tr.data-head a');
		$(document).on('click', '#ajax_data tr.data-head a', function(e) {
			e.preventDefault();
			$('#sort_column').val($(this).attr('data-column'));
			$('#sort_direction').val($(this).attr('data-direction'));
			ajax_submit();
		});

		$(document).off('click', '#ajax_data ul.pagination li a');
		$(document).on('click', '#ajax_data ul.pagination li a', function(e) {
			e.preventDefault();
			$('#ajax_loader').show();
			$('#ajax_data').hide();
			
			$.ajax({
				url: $(this).attr('href'),
				type: 'GET',
				success: function(res) {
					$('#ajax_loader').hide();
					$('#ajax_data').html(res).show();
				}
			});
		});

		<?php if($is_redirect_to_event_order=='Y'){ ?>
			$('.rep_popup').trigger('click');
		<?php } ?>
		ajax_submit();
	});

	$(document).keypress(function (e) {
    if (e.which == 13) {
      ajax_submit();
    }
  });
	function export_data(type) {
	  $("input#export").val(type);
		$('input#is_ajaxed').val('');
		$('#frm_search').submit();
	}

	$(document).on("change","#pages",function(){
		$("#per_pages").val($(this).val());
		ajax_submit();
	});

	function ajax_submit() {
		$('.list-data').css('display','block');
		$("#export").val('');
		$('#ajax_loader').show();
		$('#ajax_data').hide();
		$('input#is_ajaxed').val('1');
		$data=$('#frm_search').serialize();
		$data+="&pages="+$("#per_pages").val()+"&sort="+$("#sort_column").val()+"&direction="+$("#sort_direction").val();
		$.ajax({
		  url: $('#frm_search').attr('action'),
		  type: 'GET',
		  data: $data,
		  success: function(res) {
			$('#ajax_loader').hide();
			$('#ajax_data').html(res).show();
			$('[data-toggle="tooltip"]').tooltip();
		  }
		});
		return false;
	}

	function member_status(cust_id, status, old_status) {
		swal({
		  title: "Are you sure?",   
		  text: "This will change the status to"+" "+status+".",   
		  type: "warning",   
		  showCancelButton: true,   
		  cancelButtonText: "No, Cancel.",   
		  confirmButtonText: "Yes, Change!",   
		  showCloseButton: true 
		}).then(function(){
		  $.ajax({
				url: 'change_customers_status.php',
				data: {customer_id: cust_id, status: status},
				method: 'POST',
				dataType: 'json',
				success: function(res) {
				  swal({title:res.msg});
				  ajax_submit();
				  if (res.status == "error") {
						$("#member_status_" + cust_id).val(old_status);
				  }
				}
			 });
		}, function (dismiss) {
			window.location.reload();
		})
	}

	function member_v_status(cust_id, status, old_status) {
		swal({
		  title: "Are you sure?",   
		  text: "This will change the status to"+" "+status+".",   
		  type: "warning",   
		  showCancelButton: true,   
		  cancelButtonText: "No, Cancel.",   
		  confirmButtonText: "Yes, Change!",   
		  showCloseButton: true 
		}).then(function(){
			$.ajax({
				url: 'change_verification_status.php',
				data: {customer_id: cust_id, status: status},
				method: 'POST',
				dataType: 'json',
				success: function(res) {
				  swal({title:res.msg});
				  ajax_submit();
				  if (res.status == "error") {
					$("#v_status_" + cust_id).val(old_status);
				  }
				}
			});
		}, function (dismiss) {
			ajax_submit();
		})
	}

	$("#country").change(function () {
        var c_id = $(this).find(':selected').data('id');
        
        control_id = "#state";
        if (c_id != "") {
            $.ajax({
                url: 'ajax_get_state.php',
                data: 'country_id=' + c_id,
                type: 'GET',
                dataType: 'json',
                success: function (result) {
                    $(control_id).html(result.data);
                }
            });
        } else {
            $(control_id).html('<option value="">&nbsp;</option>');
        }
    });

</script>
<?php } ?>