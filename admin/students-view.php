<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

    if(empty($_POST['category_name'])) {
        $valid = 0;
        $error_message .= "Category Name can not be empty<br>";
    } else {
		// Duplicate Category checking
    	// current category name that is in the database
    	$statement = $pdo->prepare("SELECT * FROM tbl_category WHERE category_id=?");
		$statement->execute(array($_REQUEST['id']));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach($result as $row) {
			$current_category_name = $row['category_name'];
		}

		$statement = $pdo->prepare("SELECT * FROM tbl_category WHERE category_name=? and category_name!=?");
    	$statement->execute(array($_POST['category_name'],$current_category_name));
    	$total = $statement->rowCount();							
    	if($total) {
    		$valid = 0;
        	$error_message .= 'Category name already exists<br>';
    	}
    }

    if($valid == 1) {

    	if($_POST['category_slug'] == '') {
    		// generate slug
    		$temp_string = strtolower($_POST['category_name']);
    		$category_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $temp_string);;
    	} else {
    		$temp_string = strtolower($_POST['category_slug']);
    		$category_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $temp_string);
    	}

    	// if slug already exists, then rename it
		$statement = $pdo->prepare("SELECT * FROM tbl_category WHERE category_slug=? AND category_name!=?");
		$statement->execute(array($category_slug,$current_category_name));
		$total = $statement->rowCount();
		if($total) {
			$category_slug = $category_slug.'-1';
		}
    	
		// updating into the database
		$statement = $pdo->prepare("UPDATE tbl_category SET category_name=?, category_slug=?, meta_title=?, meta_keyword=?, meta_description=? WHERE category_id=?");
		$statement->execute(array($_POST['category_name'],$category_slug,$_POST['meta_title'],$_POST['meta_keyword'],$_POST['meta_description'],$_REQUEST['id']));

    	$success_message = 'Category is updated successfully.';
    }
}
?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_students WHERE id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Student Details</h1>
    </div>
    <div class="content-header-right">
     <!--    <a href="category.php" class="btn btn-primary btn-sm">View All</a> -->
    </div>
</section>


<section class="content">

    <div class="row">
        <div class="col-md-12">

            <?php if($error_message): ?>
            <div class="callout callout-danger">

                <p>
                    <?php echo $error_message; ?>
                </p>
            </div>
            <?php endif; ?>

            <?php if($success_message): ?>
            <div class="callout callout-success">

                <p><?php echo $success_message; ?></p>
            </div>
            <?php endif; ?>
<?php foreach ($result as $row) { ?>
            <form class="form-horizontal" action="" method="post">




                <h4
                    style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                    How do you know The Future University Link?</h4>

                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
                       
						<input  type="text" class="form-control" Value="From <?php echo $row['know']; ?>" disabled>

                    </div>

                    <div class="col-sm-4">
                        <input name="agent_name" type="text" Value="Agent Name: <?php echo $row['agent_name']; ?>" disabled class="form-control" placeholder="Agent Name">
                    </div>

                    <div class="col-sm-4">
                        <input name="agent_code" class="form-control" disabled Value="Agent Code: <?php echo $row['agent_code']; ?>"  type="text" placeholder="Agent Code">
                    </div>
                </div>

                <h4
                    style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                    STUDENT INFO</h4>

                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
                        <input name="first_name" class="form-control" Value="First Name: <?php echo $row['first_name']; ?>" disabled type="text" placeholder="First Name">
                    </div>
                    <div class="col-sm-4">
                        <input name="middle_name" class="form-control" disabled Value="Middle Name: <?php echo $row['middle_name']; ?>" type="text" placeholder="Middle Name">
                    </div>
                    <div class="col-sm-4">
                        <input name="last_name" class="form-control" disabled Value="Last Name: <?php echo $row['last_name']; ?>" type="text" placeholder="Sir Name">
                    </div>
                </div>

                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
                        <input name="phone" type="text" disabled Value="Phone: <?php echo $row['phone']; ?>" class="form-control" placeholder="Student Phone Number">
                    </div>
                    <div class="col-sm-4">
                        <input name="email" type="text" disabled Value="Email: <?php echo $row['email']; ?>" class="form-control" placeholder="Student Email">
                    </div>
                    <div class="col-sm-4">
                        <input name="city" type="text" class="form-control" disabled Value="City: <?php echo $row['city']; ?>" placeholder="City/Town applying From">
                    </div>

                </div>

                <h4
                    style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                    SPONSOR INFO</h4>

                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
                        <input name="sname" type="text" disabled Value="Sponsor Name: <?php echo $row['sname']; ?>" class="form-control" placeholder="Sponsor Name">
                    </div>
                    <div class="col-sm-4">
                        <input name="sphone" type="text" disabled Value="Sponsor Phone: <?php echo $row['sphone']; ?>" class="form-control" placeholder="Sponsor Phone">
                    </div>
                    <div class="col-sm-4">
                        <input name="occupation" type="text" disabled Value="Occupation: <?php echo $row['occupation']; ?>" class="form-control" placeholder="Occupation">
                    </div>
                </div>


                <h4
                    style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                    COURSE AND COUNTRY OPTIONS</h4>

                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
                     
						<input type="text" class="form-control" disabled Value="Course: <?php echo $row['course']; ?>">
                    </div>
                    <div class="col-sm-4">
                        <input name="course_name" type="text" disabled Value="Course Name: <?php echo $row['course_name']; ?>" class="form-control" placeholder="Course Name">
                    </div>
                    <div class="col-sm-4">
						<input type="text" disabled class="form-control" Value="Country: <?php echo $row['country']; ?>">
                        
                    </div>
                </div>


                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="Second Course: <?php echo $row['second_course']; ?>">
                        
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="Course Name: <?php echo $row['second_course_name']; ?>">

                        
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="Country: <?php echo $row['second_country']; ?>">

                       
                    </div>
                </div>

                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="Third Course: <?php echo $row['third_course']; ?>">

                
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="Course Name: <?php echo $row['third_course_name']; ?>">

                        
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="Country: <?php echo $row['third_country']; ?>">

                      
                    </div>
                </div>

                <h4
                    style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                    QUALIFICATION - O-LEVEL</h4>

                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="School Type: <?php echo $row['school_type']; ?>">

                    
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="O-level School Name: <?php echo $row['school_name']; ?>">

                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="Stream: <?php echo $row['stream']; ?>">

                     
                    </div>
                </div>

                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="O-level Completion year: <?php echo $row['years']; ?>">

                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="O-level result(e.g 1.7,2.3 etc): <?php echo $row['result']; ?>">

                      
                    
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="grading: <?php echo $row['grading']; ?>">

                    </div>
                </div>


                <h4
                    style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                    QUALIFICATION - A-LEVEL</h4>

                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="type of school: <?php echo $row['school_type_second']; ?>">

                
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="A-level School Name: <?php echo $row['school_name_second']; ?>">

                        
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="A-level combination: <?php echo $row['combination']; ?>">

                        
                    </div>
                </div>

                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="A-level Completion year: <?php echo $row['second_year']; ?>">

                        
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="A-level result(e.g 1.7,2.3 etc): <?php echo $row['second_result']; ?>">

                        
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="Grading system: <?php echo $row['second_grading']; ?>">


                    </div>
                </div>


                <h4
                    style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                    OTHER QUALIFICATION </h4>

                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="Institution name: <?php echo $row['institute_name']; ?>">

                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="level attained: <?php echo $row['levels']; ?>">

                       
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="employement type: <?php echo $row['emp_type']; ?>">

                      
                    </div>
                </div>

                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="Studies Completion year: <?php echo $row['s_complete_year']; ?>">

                       
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="Your final year result: <?php echo $row['final_result']; ?>">

                        
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="Course taken: <?php echo $row['course_taken']; ?>">

                    </div>
                </div>


                <h4
                    style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                    OTHER INFO</h4>

                <div class="row input-row" style="margin-bottom:10px;">
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="Possible budget: <?php echo $row['budget']; ?>">

                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="have passport: <?php echo $row['passport']; ?>">

                    
                    </div>
                    <div class="col-sm-4">
					<input type="text" disabled class="form-control" Value="intake: <?php echo $row['intake']; ?>">

                    </div>
                </div>


                <div class="row">
                    
                </div>



            </form>
<?php    } ?>


        </div>
    </div>

</section>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                Are you sure want to delete this item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>