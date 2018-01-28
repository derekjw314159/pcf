<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/pcf/class.user.php';
$user_home = new USER();

if(!$user_home->is_logged_in()) {
	$user_home->redirect('index.php');
	}

$stmt = $user_home->runQuery("SELECT * FROM tbl_users WHERE userID=:uid");
$stmt->execute(array(":uid"=>$_SESSION['userSession']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP CRUD Operations Using PDO Connection</title>

    <!-- Bootstrap CSS File  -->
    <link rel="sEtylesheet" type="text/css" href="bootstrap-3.3.5-dist/css/bootstrap.css"/>
	<link href="bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">
	<!-- Bootstrap --> 
	<link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
	<link href="assets/styles.css" rel="stylesheet" media="screen">
	
	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Need to reconcile some differences between the two bootstrap files -->
	<style>
		/*.modal .modal-dialog { width: 80%; }
		/*.tr {background-color: "#FFFFFF"; } */
		.navbar-inner { background-color: #04386b;} /* BB&O Blue */
		.btn.btn-primary{ margin-top:0 !important;
			vertical-align: top;
			}
		select[class*="span"] { margin-bottom: 0; }
		.big{ font-size: 1.2em;
			font-weight: bold;
			}
	</style>

</head>
<body>

<!-- Navbar -->
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
			 <span class="icon-bar"></span>
			 <span class="icon-bar"></span>
			</a>
			<a class="brand" href="#">Parental Consent System</a>
			
			<div class="nav-collapse collapse">
				<ul class="nav pull-right">
					<li class="dropdown">
						<a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-user"></i> 
						<?php echo $row['userEmail']; ?> <i class="caret"></i>
						</a>
						<ul class="dropdown-menu">
							<li>
								<a tabindex="-1" href="logout.php">Logout</a>
							</li>
						</ul>
					</li>
				</ul>

			</div>
		</div>
	</div>
</div> <!-- End of Navbar -->

<!-- Content Section -->
<div class="container">
	<form class="form-horizontal">
	<div class="row">
        <div class="col-md-12">
				<div class="form-inline col-sm-6" style="display: inline">
					<span class="big">Filter:</span>&nbsp;county
					<select class="form-control span1" id="county" name="county">
						<option>BBO</option>
						<option>Surrey</option>
					</select>
				</div>
				<div class="form-inline col-sm-3" style="display: inline">
					&nbsp; gender 
                    <select class="form-control span1" id="gender" name="gender">
                        <option>all</option>
                        <option>boy</option>
                        <option>girl</option>
                    </select>
				</div>
				<div class="form-group">
					&nbsp; squad
                    <select class="form-control span2" id="squad">
                        <option>all</option>
                        <option>player</option>
                        <option>U14</option>
                    </select>
				</div>
				<div class="form-group pull-left">
					&nbsp; DoB
                    <select multiple class="form-control" id="year" name="year[]" size="2">
                        <option>2004</option>
                        <option>2005</option>
                        <option>2006</option>
                    </select>
                    <button type="button" class="btn btn-primary">Go!</button>
				</div>
            <div class="pull-right">
                <button class="btn btn-success" data-toggle="modal" data-target="#add_new_record_modal">Add New Record</button>
            </div>
        </div>
	</div>
    </form> <!-- End of Form-->
    <div class="row">
        <div class="col-md-12">
            <h3>Records:</h3>

            <div class="records_content"></div>
        </div>
    </div>
</div>
<!-- /Content Section -->


<!-- Bootstrap Modals -->
<!-- Modal - Add New Record/User -->
<div class="modal fade" id="add_new_record_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add New Record</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" placeholder="First Name" class="form-control"/>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" placeholder="Last Name" class="form-control"/>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="text" id="email" placeholder="Email Address" class="form-control"/>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addRecord()">Add Record</button>
            </div>
        </div>
    </div>
</div>
<!-- // Modal -->

<!-- Modal - Update User details -->
<div class="modal fade" id="update_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Update</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="update_first_name">First Name</label>
                    <input type="text" id="update_first_name" placeholder="First Name" class="form-control"/>
                </div>

                <div class="form-group">
                    <label for="update_last_name">Last Name</label>
                    <input type="text" id="update_last_name" placeholder="Last Name" class="form-control"/>
                </div>

                <div class="form-group">
                    <label for="update_email">Email Address</label>
                    <input type="text" id="update_email" placeholder="Email Address" class="form-control"/>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="UpdateUserDetails()" >Save Changes</button>
                <input type="hidden" id="hidden_user_id">
            </div>
        </div>
    </div>
</div>
<!-- // Modal -->

<!-- Jquery JS file -->
<script type="text/javascript" src="assets/jquery-1.11.3.min.js"></script>

<!-- Bootstrap JS file -->
<script type="text/javascript" src="bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>

<!-- Custom JS file -->
<script type="text/javascript" src="assets/playerscript.js"></script>

</body>
</html>
