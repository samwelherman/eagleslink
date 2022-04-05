<?php
require_once('header.php');

// Preventing the direct access of this page.
if(!isset($_REQUEST['slug']))
{
	header('location: index.php');
	exit;
}
else
{
	// Check the page slug is valid or not.
	$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE page_slug=? AND status=?");
	$statement->execute(array($_REQUEST['slug'],'Active'));
	$total = $statement->rowCount();
	if( $total == 0 )
	{
		header('location: index.php');
		exit;
	}
}

// Getting the detailed data of a page from page slug
$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE page_slug=?");
$statement->execute(array($_REQUEST['slug']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) 
{
	$page_name    = $row['page_name'];
	$page_slug    = $row['page_slug'];
	$page_content = $row['page_content'];
	$page_layout  = $row['page_layout'];
	$banner       = $row['banner'];
	$status       = $row['status'];
}

// If a page is not active, redirect the user while direct URL press
if($status == 'Inactive')
{
	header('location: index.php');
	exit;
}
?>


<!-- Banner Start -->
<div class="page-banner" style="background-image: url(<?php echo BASE_URL; ?>assets/uploads/<?php echo $banner; ?>)">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="banner-text">
                    <h1><?php echo $page_name; ?></h1>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Banner End -->


<?php if($page_layout == 'Full Width Page Layout'): ?>
<section class="about-v2">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php echo $page_content; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if($page_layout == 'downloads'): ?>
<section class="about-v2">
	<div class="container">
		<div class="row">
		<div class="col-md-12">
		<center>	<h2> Documents List  </h2> </center>
		</div>
		</div>
		<hr>
<?php
$statement = $pdo->prepare("SELECT * FROM tbl_file");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
 ?>


		<div class="row">
	 <?php  
	   $i = 0;
		foreach ($result as $row) 
{ 
     $i++;
?>
		<div class="col-md-6">

		
			<center> <?php echo $i; ?>.	<a download="<?php echo $row['file_name']; ?>" href="../assets/uploads/<?php echo $row['file_name']; ?>"><?php echo $row['file_title']; ?> <i class="fa fa-download" style="font-size:13px"></i></a></center>
		</div>
		
<?php
if (isset($_GET['file'])) {
$file = $_GET['file'];
if (file_exists($file) && is_readable($file) && preg_match('/\.pdf$/',$file)) {
	header('Content-Type: application/pdf');
	header("Content-Disposition: attachment; filename=\"$file\"");
	readfile($file);
	}
}



	if($i % 2 == 0 ){
?>
		<hr><br>
<?php
	}
}	
 ?>		
		
		</div>
		
		
	</div>
</section>
<?php endif; ?>


<?php if($page_layout == 'Contact Us Page Layout'): ?>
<?php
	$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
	$statement->execute();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) 
	{
		$contact_map_iframe = $row['contact_map_iframe'];
	}
?>
<section class="contact-v1">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="heading-normal">
                    <h2>Contact Form</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">

                <?php
// After form submit checking everything for email sending
if(isset($_POST['form_contact']))
{
	$error_message = '';
	$success_message = '';
	$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
	$statement->execute();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) 
	{
		$send_email_from  = $row['send_email_from'];
		$receive_email_to = $row['receive_email_to'];
		$smtp_active      = $row['smtp_active'];
		$smtp_ssl         = $row['smtp_ssl'];
		$smtp_host        = $row['smtp_host'];
		$smtp_port        = $row['smtp_port'];
		$smtp_username    = $row['smtp_username'];
		$smtp_password    = $row['smtp_password'];

		$recaptcha_status = $row['recaptcha_status'];
	}

    $valid = 1;

    if(empty($_POST['visitor_name']))
    {
        $valid = 0;
        $error_message .= 'Please enter your name.<br>';
    }

    if(empty($_POST['visitor_phone']))
    {
        $valid = 0;
        $error_message .= 'Please enter your phone number.<br>';
    }


    if(empty($_POST['visitor_email']))
    {
        $valid = 0;
        $error_message .= 'Please enter your email address.<br>';
    }
    else
    {
    	// Email validation check
        if(!filter_var($_POST['visitor_email'], FILTER_VALIDATE_EMAIL))
        {
            $valid = 0;
            $error_message .= 'Please enter a valid email address.<br>';
        }
    }

    if(empty($_POST['visitor_comment']))
    {
        $valid = 0;
        $error_message .= 'Please enter your comment.<br>';
    }

    if($recaptcha_status == 'On')
    {
	    if(empty($_POST['g-recaptcha-response'])) {
	    	$valid = 0;
	        $error_message .= 'Please check the the captcha form.<br>';
	    }	
    }
    

    if($valid == 1)
    {

    	$visitor_name = $_POST['visitor_name'];
    	$visitor_email = $_POST['visitor_email'];
    	$visitor_phone = $_POST['visitor_phone'];
    	$visitor_comment = $_POST['visitor_comment'];

        // sending email
		$message = '
<html><body>
<table>
<tr>
<td>Name</td>
<td>'.$visitor_name.'</td>
</tr>
<tr>
<td>Email</td>
<td>'.$visitor_email.'</td>
</tr>
<tr>
<td>Phone</td>
<td>'.$visitor_phone.'</td>
</tr>
<tr>
<td>Comment</td>
<td>'.nl2br($visitor_comment).'</td>
</tr>
</table>
</body></html>
';

		require_once('assets/mail/class.phpmailer.php');
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';

        if($smtp_active == 'Yes')
	    {
	    	if($smtp_ssl == 'Yes')
	    	{
	    		$mail->SMTPSecure = "ssl";
	    	}
	    	else
	    	{
	    		$mail->SMTPSecure = "tls";
	    	}
            $mail->IsSMTP();
			$mail->SMTPAuth = true;
			$mail->Host     = $smtp_host;
			$mail->Port     = $smtp_port;
			$mail->Username = $smtp_username;
			$mail->Password = $smtp_password;
        }

        $mail->addReplyTo($visitor_email);
	    $mail->setFrom($send_email_from);
	    $mail->addAddress($receive_email_to);
	    
	    $mail->isHTML(true);
	    $mail->Subject = 'Contact form email';

	    $mail->Body = $message;
	    $mail->send();
		
        $success_message = 'Thank you for sending the email. We will contact you shortly.';

    }
}
?>


                <form action="<?php echo BASE_URL; ?>page/<?php echo $_REQUEST['slug']; ?>"
                    class="form-horizontal cform-1" method="post">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" placeholder="Name" name="visitor_name">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input type="email" class="form-control" placeholder="Email Address" name="visitor_email">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" placeholder="Phone Number" name="visitor_phone">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <textarea name="visitor_comment" class="form-control" cols="30" rows="10"
                                placeholder="Message"></textarea>
                        </div>
                    </div>

                    <?php if($recaptcha_status == 'On'): ?>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_site_key; ?>"></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <input type="submit" value="Save" class="btn btn-success" name="form_contact">
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-5">
                <div class="google-map">
                    <?php echo $contact_map_iframe; ?>
                </div>
            </div>

        </div>
    </div>
</section>
<?php endif; ?>

<?php if($page_layout == 'Online Form'): ?>
<section class="contact-v1">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="heading-normal">
                    <h2>Online Form</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form action="<?php echo BASE_URL; ?>page/<?php echo $_REQUEST['slug']; ?>" method="post">

                    <h4
                        style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                        How do you know The Future University Link?</h4>

                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <select class="form-control" name="know" id="1ct" value="">
                                <option selected="selected" value="0">Select</option>
                                <option value="Mr. Sven Kamugisha (CEO)">Mr. Sven Kamugisha (CEO)</option>
                                <option value="The Future University Link">The Future University Link</option>
                                <option value="Instagram">Instagram</option>
                                <option value="Facebook">Facebook</option>
                                <option value="Agent">Agent</option>
                                <option value="Agent">Agent</option>
                            </select>

                        </div>

                        <div class="col-sm-4">
                            <input name="agent_name" type="text" class="form-control" placeholder="Agent Name">
                        </div>

                        <div class="col-sm-4">
                            <input name="agent_code" class="form-control" type="text" placeholder="Agent Code">
                        </div>
                    </div>

                    <h4
                        style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                        STUDENT INFO</h4>

                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <input name="first_name" class="form-control" type="text" placeholder="First Name">
                        </div>
                        <div class="col-sm-4">
                            <input name="middle_name" class="form-control" type="text" placeholder="Middle Name">
                        </div>
                        <div class="col-sm-4">
                            <input name="last_name" class="form-control" type="text" placeholder="Sir Name">
                        </div>
                    </div>

                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <input name="phone" type="text" class="form-control" placeholder="Student Phone Number">
                        </div>
                        <div class="col-sm-4">
                            <input name="email" type="text" class="form-control" placeholder="Student Email">
                        </div>
                        <div class="col-sm-4">
                            <input name="city" type="text" class="form-control" placeholder="City/Town applying From">
                        </div>

                    </div>

                    <h4
                        style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                        SPONSOR INFO</h4>

                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <input name="sname" type="text" class="form-control" placeholder="Sponsor Name">
                        </div>
                        <div class="col-sm-4">
                            <input name="sphone" type="text" class="form-control" placeholder="Sponsor Phone">
                        </div>
                        <div class="col-sm-4">
                            <input name="occupation" type="text" class="form-control" placeholder="Occupation">
                        </div>
                    </div>


                    <h4
                        style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                        COURSE AND COUNTRY OPTIONS</h4>

                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <select class="form-control" name="course" id="1ct" value="">
                                <option value="1st Course category"> 1st Course category</option>
                                <option value="Accounts & Commerce field category">Accounts & Commerce field category
                                </option>
                                <option value="Architecture, Land valuation & Planning Category">Architecture, Land
                                    valuation & Planning Category</option>
                                <option value="Arts,Education & Law fields category">Arts,Education & Law fields
                                    category</option>
                                <option value="Basic science & Research eg, zoology,botany,chemistry">Basic science &
                                    Research eg, zoology,botany,chemistry</option>
                                <option value="Business courses & Economic studies category">Business courses & Economic
                                    studies category</option>
                                <option value="Design, Creative & Media studies">Design, Creative & Media studies
                                </option>
                                <option value="Engineering Fields category">Engineering Fields category</option>
                                <option value="Medicine & Health Allied Science">Medicine & Health Allied Science
                                </option>
                                <option value="Secondary school / High school">Secondary school / High school</option>
                                <option value="Sports academy, Fitness & Related courses category">Sports academy,
                                    Fitness & Related courses category</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <input name="course_name" type="text" class="form-control" placeholder="Course Name">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="country" id="1cnt" value="">
                                <option value="Select country">Select country</option>
                                <option value="Afganistan">Afghanistan</option>
                                <option value="Albania">Albania</option>
                                <option value="Algeria">Algeria</option>
                                <option value="American Samoa">American Samoa</option>
                                <option value="Andorra">Andorra</option>
                                <option value="Angola">Angola</option>
                                <option value="Anguilla">Anguilla</option>
                                <option value="Antigua & Barbuda">Antigua & Barbuda</option>
                                <option value="Argentina">Argentina</option>
                                <option value="Armenia">Armenia</option>
                                <option value="Aruba">Aruba</option>
                                <option value="Australia">Australia</option>
                                <option value="Austria">Austria</option>
                                <option value="Azerbaijan">Azerbaijan</option>
                                <option value="Bahamas">Bahamas</option>
                                <option value="Bahrain">Bahrain</option>
                                <option value="Bangladesh">Bangladesh</option>
                                <option value="Barbados">Barbados</option>
                                <option value="Belarus">Belarus</option>
                                <option value="Belgium">Belgium</option>
                                <option value="Belize">Belize</option>
                                <option value="Benin">Benin</option>
                                <option value="Bermuda">Bermuda</option>
                                <option value="Bhutan">Bhutan</option>
                                <option value="Bolivia">Bolivia</option>
                                <option value="Bonaire">Bonaire</option>
                                <option value="Bosnia & Herzegovina">Bosnia & Herzegovina</option>
                                <option value="Botswana">Botswana</option>
                                <option value="Brazil">Brazil</option>
                                <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
                                <option value="Brunei">Brunei</option>
                                <option value="Bulgaria">Bulgaria</option>
                                <option value="Burkina Faso">Burkina Faso</option>
                                <option value="Burundi">Burundi</option>
                                <option value="Cambodia">Cambodia</option>
                                <option value="Cameroon">Cameroon</option>
                                <option value="Canada">Canada</option>
                                <option value="Canary Islands">Canary Islands</option>
                                <option value="Cape Verde">Cape Verde</option>
                                <option value="Cayman Islands">Cayman Islands</option>
                                <option value="Central African Republic">Central African Republic</option>
                                <option value="Chad">Chad</option>
                                <option value="Channel Islands">Channel Islands</option>
                                <option value="Chile">Chile</option>
                                <option value="China">China</option>
                                <option value="Christmas Island">Christmas Island</option>
                                <option value="Cocos Island">Cocos Island</option>
                                <option value="Colombia">Colombia</option>
                                <option value="Comoros">Comoros</option>
                                <option value="Congo">Congo</option>
                                <option value="Cook Islands">Cook Islands</option>
                                <option value="Costa Rica">Costa Rica</option>
                                <option value="Cote DIvoire">Cote DIvoire</option>
                                <option value="Croatia">Croatia</option>
                                <option value="Cuba">Cuba</option>
                                <option value="Curaco">Curacao</option>
                                <option value="Cyprus">Cyprus</option>
                                <option value="Czech Republic">Czech Republic</option>
                                <option value="Denmark">Denmark</option>
                                <option value="Djibouti">Djibouti</option>
                                <option value="Dominica">Dominica</option>
                                <option value="Dominican Republic">Dominican Republic</option>
                                <option value="East Timor">East Timor</option>
                                <option value="Ecuador">Ecuador</option>
                                <option value="Egypt">Egypt</option>
                                <option value="El Salvador">El Salvador</option>
                                <option value="Equatorial Guinea">Equatorial Guinea</option>
                                <option value="Eritrea">Eritrea</option>
                                <option value="Estonia">Estonia</option>
                                <option value="Ethiopia">Ethiopia</option>
                                <option value="Falkland Islands">Falkland Islands</option>
                                <option value="Faroe Islands">Faroe Islands</option>
                                <option value="Fiji">Fiji</option>
                                <option value="Finland">Finland</option>
                                <option value="France">France</option>
                                <option value="French Guiana">French Guiana</option>
                                <option value="French Polynesia">French Polynesia</option>
                                <option value="French Southern Ter">French Southern Ter</option>
                                <option value="Gabon">Gabon</option>
                                <option value="Gambia">Gambia</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Germany">Germany</option>
                                <option value="Ghana">Ghana</option>
                                <option value="Gibraltar">Gibraltar</option>
                                <option value="Great Britain">Great Britain</option>
                                <option value="Greece">Greece</option>
                                <option value="Greenland">Greenland</option>
                                <option value="Grenada">Grenada</option>
                                <option value="Guadeloupe">Guadeloupe</option>
                                <option value="Guam">Guam</option>
                                <option value="Guatemala">Guatemala</option>
                                <option value="Guinea">Guinea</option>
                                <option value="Guyana">Guyana</option>
                                <option value="Haiti">Haiti</option>
                                <option value="Hawaii">Hawaii</option>
                                <option value="Honduras">Honduras</option>
                                <option value="Hong Kong">Hong Kong</option>
                                <option value="Hungary">Hungary</option>
                                <option value="Iceland">Iceland</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="India">India</option>
                                <option value="Iran">Iran</option>
                                <option value="Iraq">Iraq</option>
                                <option value="Ireland">Ireland</option>
                                <option value="Isle of Man">Isle of Man</option>
                                <option value="Israel">Israel</option>
                                <option value="Italy">Italy</option>
                                <option value="Jamaica">Jamaica</option>
                                <option value="Japan">Japan</option>
                                <option value="Jordan">Jordan</option>
                                <option value="Kazakhstan">Kazakhstan</option>
                                <option value="Kenya">Kenya</option>
                                <option value="Kiribati">Kiribati</option>
                                <option value="Korea North">Korea North</option>
                                <option value="Korea Sout">Korea South</option>
                                <option value="Kuwait">Kuwait</option>
                                <option value="Kyrgyzstan">Kyrgyzstan</option>
                                <option value="Laos">Laos</option>
                                <option value="Latvia">Latvia</option>
                                <option value="Lebanon">Lebanon</option>
                                <option value="Lesotho">Lesotho</option>
                                <option value="Liberia">Liberia</option>
                                <option value="Libya">Libya</option>
                                <option value="Liechtenstein">Liechtenstein</option>
                                <option value="Lithuania">Lithuania</option>
                                <option value="Luxembourg">Luxembourg</option>
                                <option value="Macau">Macau</option>
                                <option value="Macedonia">Macedonia</option>
                                <option value="Madagascar">Madagascar</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Malawi">Malawi</option>
                                <option value="Maldives">Maldives</option>
                                <option value="Mali">Mali</option>
                                <option value="Malta">Malta</option>
                                <option value="Marshall Islands">Marshall Islands</option>
                                <option value="Martinique">Martinique</option>
                                <option value="Mauritania">Mauritania</option>
                                <option value="Mauritius">Mauritius</option>
                                <option value="Mayotte">Mayotte</option>
                                <option value="Mexico">Mexico</option>
                                <option value="Midway Islands">Midway Islands</option>
                                <option value="Moldova">Moldova</option>
                                <option value="Monaco">Monaco</option>
                                <option value="Mongolia">Mongolia</option>
                                <option value="Montserrat">Montserrat</option>
                                <option value="Morocco">Morocco</option>
                                <option value="Mozambique">Mozambique</option>
                                <option value="Myanmar">Myanmar</option>
                                <option value="Nambia">Nambia</option>
                                <option value="Nauru">Nauru</option>
                                <option value="Nepal">Nepal</option>
                                <option value="Netherland Antilles">Netherland Antilles</option>
                                <option value="Netherlands">Netherlands (Holland, Europe)</option>
                                <option value="Nevis">Nevis</option>
                                <option value="New Caledonia">New Caledonia</option>
                                <option value="New Zealand">New Zealand</option>
                                <option value="Nicaragua">Nicaragua</option>
                                <option value="Niger">Niger</option>
                                <option value="Nigeria">Nigeria</option>
                                <option value="Niue">Niue</option>
                                <option value="Norfolk Island">Norfolk Island</option>
                                <option value="Norway">Norway</option>
                                <option value="Oman">Oman</option>
                                <option value="Pakistan">Pakistan</option>
                                <option value="Palau Island">Palau Island</option>
                                <option value="Palestine">Palestine</option>
                                <option value="Panama">Panama</option>
                                <option value="Papua New Guinea">Papua New Guinea</option>
                                <option value="Paraguay">Paraguay</option>
                                <option value="Peru">Peru</option>
                                <option value="Phillipines">Philippines</option>
                                <option value="Pitcairn Island">Pitcairn Island</option>
                                <option value="Poland">Poland</option>
                                <option value="Portugal">Portugal</option>
                                <option value="Puerto Rico">Puerto Rico</option>
                                <option value="Qatar">Qatar</option>
                                <option value="Republic of Montenegro">Republic of Montenegro</option>
                                <option value="Republic of Serbia">Republic of Serbia</option>
                                <option value="Reunion">Reunion</option>
                                <option value="Romania">Romania</option>
                                <option value="Russia">Russia</option>
                                <option value="Rwanda">Rwanda</option>
                                <option value="St Barthelemy">St Barthelemy</option>
                                <option value="St Eustatius">St Eustatius</option>
                                <option value="St Helena">St Helena</option>
                                <option value="St Kitts-Nevis">St Kitts-Nevis</option>
                                <option value="St Lucia">St Lucia</option>
                                <option value="St Maarten">St Maarten</option>
                                <option value="St Pierre & Miquelon">St Pierre & Miquelon</option>
                                <option value="St Vincent & Grenadines">St Vincent & Grenadines</option>
                                <option value="Saipan">Saipan</option>
                                <option value="Samoa">Samoa</option>
                                <option value="Samoa American">Samoa American</option>
                                <option value="San Marino">San Marino</option>
                                <option value="Sao Tome & Principe">Sao Tome & Principe</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="Senegal">Senegal</option>
                                <option value="Seychelles">Seychelles</option>
                                <option value="Sierra Leone">Sierra Leone</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Slovakia">Slovakia</option>
                                <option value="Slovenia">Slovenia</option>
                                <option value="Solomon Islands">Solomon Islands</option>
                                <option value="Somalia">Somalia</option>
                                <option value="South Africa">South Africa</option>
                                <option value="Spain">Spain</option>
                                <option value="Sri Lanka">Sri Lanka</option>
                                <option value="Sudan">Sudan</option>
                                <option value="Suriname">Suriname</option>
                                <option value="Swaziland">Swaziland</option>
                                <option value="Sweden">Sweden</option>
                                <option value="Switzerland">Switzerland</option>
                                <option value="Syria">Syria</option>
                                <option value="Tahiti">Tahiti</option>
                                <option value="Taiwan">Taiwan</option>
                                <option value="Tajikistan">Tajikistan</option>
                                <option value="Tanzania">Tanzania</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Togo">Togo</option>
                                <option value="Tokelau">Tokelau</option>
                                <option value="Tonga">Tonga</option>
                                <option value="Trinidad & Tobago">Trinidad & Tobago</option>
                                <option value="Tunisia">Tunisia</option>
                                <option value="Turkey">Turkey</option>
                                <option value="Turkmenistan">Turkmenistan</option>
                                <option value="Turks & Caicos Is">Turks & Caicos Is</option>
                                <option value="Tuvalu">Tuvalu</option>
                                <option value="Uganda">Uganda</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Ukraine">Ukraine</option>
                                <option value="United Arab Erimates">United Arab Emirates</option>
                                <option value="United States of America">United States of America</option>
                                <option value="Uraguay">Uruguay</option>
                                <option value="Uzbekistan">Uzbekistan</option>
                                <option value="Vanuatu">Vanuatu</option>
                                <option value="Vatican City State">Vatican City State</option>
                                <option value="Venezuela">Venezuela</option>
                                <option value="Vietnam">Vietnam</option>
                                <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
                                <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
                                <option value="Wake Island">Wake Island</option>
                                <option value="Wallis & Futana Is">Wallis & Futana Is</option>
                                <option value="Yemen">Yemen</option>
                                <option value="Zaire">Zaire</option>
                                <option value="Zambia">Zambia</option>
                                <option value="Zimbabwe">Zimbabwe</option>
                            </select>
                        </div>
                    </div>


                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <select class="form-control" name="second_course" id="1ct" value="">
                                <option value=""> 2nd Course category</option>
                                <option value="Accounts & Commerce field category">Accounts & Commerce field category
                                </option>
                                <option value="Architecture, Land valuation & Planning Category">Architecture, Land
                                    valuation & Planning Category</option>
                                <option value="Arts,Education & Law fields category">Arts,Education & Law fields
                                    category</option>
                                <option value="Basic science & Research eg, zoology,botany,chemistry">Basic science &
                                    Research eg, zoology,botany,chemistry</option>
                                <option value="Business courses & Economic studies category">Business courses & Economic
                                    studies category</option>
                                <option value="Design, Creative & Media studies">Design, Creative & Media studies
                                </option>
                                <option value="Engineering Fields category">Engineering Fields category</option>
                                <option value="Medicine & Health Allied Science">Medicine & Health Allied Science
                                </option>
                                <option value="Secondary school / High school">Secondary school / High school</option>
                                <option value="Sports academy, Fitness & Related courses category">Sports academy,
                                    Fitness & Related courses category</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <input name="second_course_name" class="form-control" type="text" placeholder="Course Name">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="second_country" id="1cnt" value="">
                                <option value="Select country">Select country</option>
                                <option value="Afganistan">Afghanistan</option>
                                <option value="Albania">Albania</option>
                                <option value="Algeria">Algeria</option>
                                <option value="American Samoa">American Samoa</option>
                                <option value="Andorra">Andorra</option>
                                <option value="Angola">Angola</option>
                                <option value="Anguilla">Anguilla</option>
                                <option value="Antigua & Barbuda">Antigua & Barbuda</option>
                                <option value="Argentina">Argentina</option>
                                <option value="Armenia">Armenia</option>
                                <option value="Aruba">Aruba</option>
                                <option value="Australia">Australia</option>
                                <option value="Austria">Austria</option>
                                <option value="Azerbaijan">Azerbaijan</option>
                                <option value="Bahamas">Bahamas</option>
                                <option value="Bahrain">Bahrain</option>
                                <option value="Bangladesh">Bangladesh</option>
                                <option value="Barbados">Barbados</option>
                                <option value="Belarus">Belarus</option>
                                <option value="Belgium">Belgium</option>
                                <option value="Belize">Belize</option>
                                <option value="Benin">Benin</option>
                                <option value="Bermuda">Bermuda</option>
                                <option value="Bhutan">Bhutan</option>
                                <option value="Bolivia">Bolivia</option>
                                <option value="Bonaire">Bonaire</option>
                                <option value="Bosnia & Herzegovina">Bosnia & Herzegovina</option>
                                <option value="Botswana">Botswana</option>
                                <option value="Brazil">Brazil</option>
                                <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
                                <option value="Brunei">Brunei</option>
                                <option value="Bulgaria">Bulgaria</option>
                                <option value="Burkina Faso">Burkina Faso</option>
                                <option value="Burundi">Burundi</option>
                                <option value="Cambodia">Cambodia</option>
                                <option value="Cameroon">Cameroon</option>
                                <option value="Canada">Canada</option>
                                <option value="Canary Islands">Canary Islands</option>
                                <option value="Cape Verde">Cape Verde</option>
                                <option value="Cayman Islands">Cayman Islands</option>
                                <option value="Central African Republic">Central African Republic</option>
                                <option value="Chad">Chad</option>
                                <option value="Channel Islands">Channel Islands</option>
                                <option value="Chile">Chile</option>
                                <option value="China">China</option>
                                <option value="Christmas Island">Christmas Island</option>
                                <option value="Cocos Island">Cocos Island</option>
                                <option value="Colombia">Colombia</option>
                                <option value="Comoros">Comoros</option>
                                <option value="Congo">Congo</option>
                                <option value="Cook Islands">Cook Islands</option>
                                <option value="Costa Rica">Costa Rica</option>
                                <option value="Cote DIvoire">Cote DIvoire</option>
                                <option value="Croatia">Croatia</option>
                                <option value="Cuba">Cuba</option>
                                <option value="Curaco">Curacao</option>
                                <option value="Cyprus">Cyprus</option>
                                <option value="Czech Republic">Czech Republic</option>
                                <option value="Denmark">Denmark</option>
                                <option value="Djibouti">Djibouti</option>
                                <option value="Dominica">Dominica</option>
                                <option value="Dominican Republic">Dominican Republic</option>
                                <option value="East Timor">East Timor</option>
                                <option value="Ecuador">Ecuador</option>
                                <option value="Egypt">Egypt</option>
                                <option value="El Salvador">El Salvador</option>
                                <option value="Equatorial Guinea">Equatorial Guinea</option>
                                <option value="Eritrea">Eritrea</option>
                                <option value="Estonia">Estonia</option>
                                <option value="Ethiopia">Ethiopia</option>
                                <option value="Falkland Islands">Falkland Islands</option>
                                <option value="Faroe Islands">Faroe Islands</option>
                                <option value="Fiji">Fiji</option>
                                <option value="Finland">Finland</option>
                                <option value="France">France</option>
                                <option value="French Guiana">French Guiana</option>
                                <option value="French Polynesia">French Polynesia</option>
                                <option value="French Southern Ter">French Southern Ter</option>
                                <option value="Gabon">Gabon</option>
                                <option value="Gambia">Gambia</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Germany">Germany</option>
                                <option value="Ghana">Ghana</option>
                                <option value="Gibraltar">Gibraltar</option>
                                <option value="Great Britain">Great Britain</option>
                                <option value="Greece">Greece</option>
                                <option value="Greenland">Greenland</option>
                                <option value="Grenada">Grenada</option>
                                <option value="Guadeloupe">Guadeloupe</option>
                                <option value="Guam">Guam</option>
                                <option value="Guatemala">Guatemala</option>
                                <option value="Guinea">Guinea</option>
                                <option value="Guyana">Guyana</option>
                                <option value="Haiti">Haiti</option>
                                <option value="Hawaii">Hawaii</option>
                                <option value="Honduras">Honduras</option>
                                <option value="Hong Kong">Hong Kong</option>
                                <option value="Hungary">Hungary</option>
                                <option value="Iceland">Iceland</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="India">India</option>
                                <option value="Iran">Iran</option>
                                <option value="Iraq">Iraq</option>
                                <option value="Ireland">Ireland</option>
                                <option value="Isle of Man">Isle of Man</option>
                                <option value="Israel">Israel</option>
                                <option value="Italy">Italy</option>
                                <option value="Jamaica">Jamaica</option>
                                <option value="Japan">Japan</option>
                                <option value="Jordan">Jordan</option>
                                <option value="Kazakhstan">Kazakhstan</option>
                                <option value="Kenya">Kenya</option>
                                <option value="Kiribati">Kiribati</option>
                                <option value="Korea North">Korea North</option>
                                <option value="Korea Sout">Korea South</option>
                                <option value="Kuwait">Kuwait</option>
                                <option value="Kyrgyzstan">Kyrgyzstan</option>
                                <option value="Laos">Laos</option>
                                <option value="Latvia">Latvia</option>
                                <option value="Lebanon">Lebanon</option>
                                <option value="Lesotho">Lesotho</option>
                                <option value="Liberia">Liberia</option>
                                <option value="Libya">Libya</option>
                                <option value="Liechtenstein">Liechtenstein</option>
                                <option value="Lithuania">Lithuania</option>
                                <option value="Luxembourg">Luxembourg</option>
                                <option value="Macau">Macau</option>
                                <option value="Macedonia">Macedonia</option>
                                <option value="Madagascar">Madagascar</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Malawi">Malawi</option>
                                <option value="Maldives">Maldives</option>
                                <option value="Mali">Mali</option>
                                <option value="Malta">Malta</option>
                                <option value="Marshall Islands">Marshall Islands</option>
                                <option value="Martinique">Martinique</option>
                                <option value="Mauritania">Mauritania</option>
                                <option value="Mauritius">Mauritius</option>
                                <option value="Mayotte">Mayotte</option>
                                <option value="Mexico">Mexico</option>
                                <option value="Midway Islands">Midway Islands</option>
                                <option value="Moldova">Moldova</option>
                                <option value="Monaco">Monaco</option>
                                <option value="Mongolia">Mongolia</option>
                                <option value="Montserrat">Montserrat</option>
                                <option value="Morocco">Morocco</option>
                                <option value="Mozambique">Mozambique</option>
                                <option value="Myanmar">Myanmar</option>
                                <option value="Nambia">Nambia</option>
                                <option value="Nauru">Nauru</option>
                                <option value="Nepal">Nepal</option>
                                <option value="Netherland Antilles">Netherland Antilles</option>
                                <option value="Netherlands">Netherlands (Holland, Europe)</option>
                                <option value="Nevis">Nevis</option>
                                <option value="New Caledonia">New Caledonia</option>
                                <option value="New Zealand">New Zealand</option>
                                <option value="Nicaragua">Nicaragua</option>
                                <option value="Niger">Niger</option>
                                <option value="Nigeria">Nigeria</option>
                                <option value="Niue">Niue</option>
                                <option value="Norfolk Island">Norfolk Island</option>
                                <option value="Norway">Norway</option>
                                <option value="Oman">Oman</option>
                                <option value="Pakistan">Pakistan</option>
                                <option value="Palau Island">Palau Island</option>
                                <option value="Palestine">Palestine</option>
                                <option value="Panama">Panama</option>
                                <option value="Papua New Guinea">Papua New Guinea</option>
                                <option value="Paraguay">Paraguay</option>
                                <option value="Peru">Peru</option>
                                <option value="Phillipines">Philippines</option>
                                <option value="Pitcairn Island">Pitcairn Island</option>
                                <option value="Poland">Poland</option>
                                <option value="Portugal">Portugal</option>
                                <option value="Puerto Rico">Puerto Rico</option>
                                <option value="Qatar">Qatar</option>
                                <option value="Republic of Montenegro">Republic of Montenegro</option>
                                <option value="Republic of Serbia">Republic of Serbia</option>
                                <option value="Reunion">Reunion</option>
                                <option value="Romania">Romania</option>
                                <option value="Russia">Russia</option>
                                <option value="Rwanda">Rwanda</option>
                                <option value="St Barthelemy">St Barthelemy</option>
                                <option value="St Eustatius">St Eustatius</option>
                                <option value="St Helena">St Helena</option>
                                <option value="St Kitts-Nevis">St Kitts-Nevis</option>
                                <option value="St Lucia">St Lucia</option>
                                <option value="St Maarten">St Maarten</option>
                                <option value="St Pierre & Miquelon">St Pierre & Miquelon</option>
                                <option value="St Vincent & Grenadines">St Vincent & Grenadines</option>
                                <option value="Saipan">Saipan</option>
                                <option value="Samoa">Samoa</option>
                                <option value="Samoa American">Samoa American</option>
                                <option value="San Marino">San Marino</option>
                                <option value="Sao Tome & Principe">Sao Tome & Principe</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="Senegal">Senegal</option>
                                <option value="Seychelles">Seychelles</option>
                                <option value="Sierra Leone">Sierra Leone</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Slovakia">Slovakia</option>
                                <option value="Slovenia">Slovenia</option>
                                <option value="Solomon Islands">Solomon Islands</option>
                                <option value="Somalia">Somalia</option>
                                <option value="South Africa">South Africa</option>
                                <option value="Spain">Spain</option>
                                <option value="Sri Lanka">Sri Lanka</option>
                                <option value="Sudan">Sudan</option>
                                <option value="Suriname">Suriname</option>
                                <option value="Swaziland">Swaziland</option>
                                <option value="Sweden">Sweden</option>
                                <option value="Switzerland">Switzerland</option>
                                <option value="Syria">Syria</option>
                                <option value="Tahiti">Tahiti</option>
                                <option value="Taiwan">Taiwan</option>
                                <option value="Tajikistan">Tajikistan</option>
                                <option value="Tanzania">Tanzania</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Togo">Togo</option>
                                <option value="Tokelau">Tokelau</option>
                                <option value="Tonga">Tonga</option>
                                <option value="Trinidad & Tobago">Trinidad & Tobago</option>
                                <option value="Tunisia">Tunisia</option>
                                <option value="Turkey">Turkey</option>
                                <option value="Turkmenistan">Turkmenistan</option>
                                <option value="Turks & Caicos Is">Turks & Caicos Is</option>
                                <option value="Tuvalu">Tuvalu</option>
                                <option value="Uganda">Uganda</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Ukraine">Ukraine</option>
                                <option value="United Arab Erimates">United Arab Emirates</option>
                                <option value="United States of America">United States of America</option>
                                <option value="Uraguay">Uruguay</option>
                                <option value="Uzbekistan">Uzbekistan</option>
                                <option value="Vanuatu">Vanuatu</option>
                                <option value="Vatican City State">Vatican City State</option>
                                <option value="Venezuela">Venezuela</option>
                                <option value="Vietnam">Vietnam</option>
                                <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
                                <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
                                <option value="Wake Island">Wake Island</option>
                                <option value="Wallis & Futana Is">Wallis & Futana Is</option>
                                <option value="Yemen">Yemen</option>
                                <option value="Zaire">Zaire</option>
                                <option value="Zambia">Zambia</option>
                                <option value="Zimbabwe">Zimbabwe</option>
                            </select>
                        </div>
                    </div>

                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <select class="form-control" name="third_course" id="1ct" value="">
                                <option value=""> 3rd Course category</option>
                                <option value="3">Accounts & Commerce field category</option>
                                <option value="4">Architecture, Land valuation & Planning Category</option>
                                <option value="20">Arts,Education & Law fields category</option>
                                <option value="1">Basic science & Research eg, zoology,botany,chemistry</option>
                                <option value="7">Business courses & Economic studies category</option>
                                <option value="2">Design, Creative & Media studies</option>
                                <option value="9">Engineering Fields category</option>
                                <option value="14">Medicine & Health Allied Science</option>
                                <option value="6">Secondary school / High school</option>
                                <option value="5">Sports academy, Fitness & Related courses category</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <input name="third_course_name" class="form-control" type="text" placeholder="Course Name">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="third_country" id="1cnt" value="">
                                <option value="Select country">Select country</option>
                                <option value="Afganistan">Afghanistan</option>
                                <option value="Albania">Albania</option>
                                <option value="Algeria">Algeria</option>
                                <option value="American Samoa">American Samoa</option>
                                <option value="Andorra">Andorra</option>
                                <option value="Angola">Angola</option>
                                <option value="Anguilla">Anguilla</option>
                                <option value="Antigua & Barbuda">Antigua & Barbuda</option>
                                <option value="Argentina">Argentina</option>
                                <option value="Armenia">Armenia</option>
                                <option value="Aruba">Aruba</option>
                                <option value="Australia">Australia</option>
                                <option value="Austria">Austria</option>
                                <option value="Azerbaijan">Azerbaijan</option>
                                <option value="Bahamas">Bahamas</option>
                                <option value="Bahrain">Bahrain</option>
                                <option value="Bangladesh">Bangladesh</option>
                                <option value="Barbados">Barbados</option>
                                <option value="Belarus">Belarus</option>
                                <option value="Belgium">Belgium</option>
                                <option value="Belize">Belize</option>
                                <option value="Benin">Benin</option>
                                <option value="Bermuda">Bermuda</option>
                                <option value="Bhutan">Bhutan</option>
                                <option value="Bolivia">Bolivia</option>
                                <option value="Bonaire">Bonaire</option>
                                <option value="Bosnia & Herzegovina">Bosnia & Herzegovina</option>
                                <option value="Botswana">Botswana</option>
                                <option value="Brazil">Brazil</option>
                                <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
                                <option value="Brunei">Brunei</option>
                                <option value="Bulgaria">Bulgaria</option>
                                <option value="Burkina Faso">Burkina Faso</option>
                                <option value="Burundi">Burundi</option>
                                <option value="Cambodia">Cambodia</option>
                                <option value="Cameroon">Cameroon</option>
                                <option value="Canada">Canada</option>
                                <option value="Canary Islands">Canary Islands</option>
                                <option value="Cape Verde">Cape Verde</option>
                                <option value="Cayman Islands">Cayman Islands</option>
                                <option value="Central African Republic">Central African Republic</option>
                                <option value="Chad">Chad</option>
                                <option value="Channel Islands">Channel Islands</option>
                                <option value="Chile">Chile</option>
                                <option value="China">China</option>
                                <option value="Christmas Island">Christmas Island</option>
                                <option value="Cocos Island">Cocos Island</option>
                                <option value="Colombia">Colombia</option>
                                <option value="Comoros">Comoros</option>
                                <option value="Congo">Congo</option>
                                <option value="Cook Islands">Cook Islands</option>
                                <option value="Costa Rica">Costa Rica</option>
                                <option value="Cote DIvoire">Cote DIvoire</option>
                                <option value="Croatia">Croatia</option>
                                <option value="Cuba">Cuba</option>
                                <option value="Curaco">Curacao</option>
                                <option value="Cyprus">Cyprus</option>
                                <option value="Czech Republic">Czech Republic</option>
                                <option value="Denmark">Denmark</option>
                                <option value="Djibouti">Djibouti</option>
                                <option value="Dominica">Dominica</option>
                                <option value="Dominican Republic">Dominican Republic</option>
                                <option value="East Timor">East Timor</option>
                                <option value="Ecuador">Ecuador</option>
                                <option value="Egypt">Egypt</option>
                                <option value="El Salvador">El Salvador</option>
                                <option value="Equatorial Guinea">Equatorial Guinea</option>
                                <option value="Eritrea">Eritrea</option>
                                <option value="Estonia">Estonia</option>
                                <option value="Ethiopia">Ethiopia</option>
                                <option value="Falkland Islands">Falkland Islands</option>
                                <option value="Faroe Islands">Faroe Islands</option>
                                <option value="Fiji">Fiji</option>
                                <option value="Finland">Finland</option>
                                <option value="France">France</option>
                                <option value="French Guiana">French Guiana</option>
                                <option value="French Polynesia">French Polynesia</option>
                                <option value="French Southern Ter">French Southern Ter</option>
                                <option value="Gabon">Gabon</option>
                                <option value="Gambia">Gambia</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Germany">Germany</option>
                                <option value="Ghana">Ghana</option>
                                <option value="Gibraltar">Gibraltar</option>
                                <option value="Great Britain">Great Britain</option>
                                <option value="Greece">Greece</option>
                                <option value="Greenland">Greenland</option>
                                <option value="Grenada">Grenada</option>
                                <option value="Guadeloupe">Guadeloupe</option>
                                <option value="Guam">Guam</option>
                                <option value="Guatemala">Guatemala</option>
                                <option value="Guinea">Guinea</option>
                                <option value="Guyana">Guyana</option>
                                <option value="Haiti">Haiti</option>
                                <option value="Hawaii">Hawaii</option>
                                <option value="Honduras">Honduras</option>
                                <option value="Hong Kong">Hong Kong</option>
                                <option value="Hungary">Hungary</option>
                                <option value="Iceland">Iceland</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="India">India</option>
                                <option value="Iran">Iran</option>
                                <option value="Iraq">Iraq</option>
                                <option value="Ireland">Ireland</option>
                                <option value="Isle of Man">Isle of Man</option>
                                <option value="Israel">Israel</option>
                                <option value="Italy">Italy</option>
                                <option value="Jamaica">Jamaica</option>
                                <option value="Japan">Japan</option>
                                <option value="Jordan">Jordan</option>
                                <option value="Kazakhstan">Kazakhstan</option>
                                <option value="Kenya">Kenya</option>
                                <option value="Kiribati">Kiribati</option>
                                <option value="Korea North">Korea North</option>
                                <option value="Korea Sout">Korea South</option>
                                <option value="Kuwait">Kuwait</option>
                                <option value="Kyrgyzstan">Kyrgyzstan</option>
                                <option value="Laos">Laos</option>
                                <option value="Latvia">Latvia</option>
                                <option value="Lebanon">Lebanon</option>
                                <option value="Lesotho">Lesotho</option>
                                <option value="Liberia">Liberia</option>
                                <option value="Libya">Libya</option>
                                <option value="Liechtenstein">Liechtenstein</option>
                                <option value="Lithuania">Lithuania</option>
                                <option value="Luxembourg">Luxembourg</option>
                                <option value="Macau">Macau</option>
                                <option value="Macedonia">Macedonia</option>
                                <option value="Madagascar">Madagascar</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Malawi">Malawi</option>
                                <option value="Maldives">Maldives</option>
                                <option value="Mali">Mali</option>
                                <option value="Malta">Malta</option>
                                <option value="Marshall Islands">Marshall Islands</option>
                                <option value="Martinique">Martinique</option>
                                <option value="Mauritania">Mauritania</option>
                                <option value="Mauritius">Mauritius</option>
                                <option value="Mayotte">Mayotte</option>
                                <option value="Mexico">Mexico</option>
                                <option value="Midway Islands">Midway Islands</option>
                                <option value="Moldova">Moldova</option>
                                <option value="Monaco">Monaco</option>
                                <option value="Mongolia">Mongolia</option>
                                <option value="Montserrat">Montserrat</option>
                                <option value="Morocco">Morocco</option>
                                <option value="Mozambique">Mozambique</option>
                                <option value="Myanmar">Myanmar</option>
                                <option value="Nambia">Nambia</option>
                                <option value="Nauru">Nauru</option>
                                <option value="Nepal">Nepal</option>
                                <option value="Netherland Antilles">Netherland Antilles</option>
                                <option value="Netherlands">Netherlands (Holland, Europe)</option>
                                <option value="Nevis">Nevis</option>
                                <option value="New Caledonia">New Caledonia</option>
                                <option value="New Zealand">New Zealand</option>
                                <option value="Nicaragua">Nicaragua</option>
                                <option value="Niger">Niger</option>
                                <option value="Nigeria">Nigeria</option>
                                <option value="Niue">Niue</option>
                                <option value="Norfolk Island">Norfolk Island</option>
                                <option value="Norway">Norway</option>
                                <option value="Oman">Oman</option>
                                <option value="Pakistan">Pakistan</option>
                                <option value="Palau Island">Palau Island</option>
                                <option value="Palestine">Palestine</option>
                                <option value="Panama">Panama</option>
                                <option value="Papua New Guinea">Papua New Guinea</option>
                                <option value="Paraguay">Paraguay</option>
                                <option value="Peru">Peru</option>
                                <option value="Phillipines">Philippines</option>
                                <option value="Pitcairn Island">Pitcairn Island</option>
                                <option value="Poland">Poland</option>
                                <option value="Portugal">Portugal</option>
                                <option value="Puerto Rico">Puerto Rico</option>
                                <option value="Qatar">Qatar</option>
                                <option value="Republic of Montenegro">Republic of Montenegro</option>
                                <option value="Republic of Serbia">Republic of Serbia</option>
                                <option value="Reunion">Reunion</option>
                                <option value="Romania">Romania</option>
                                <option value="Russia">Russia</option>
                                <option value="Rwanda">Rwanda</option>
                                <option value="St Barthelemy">St Barthelemy</option>
                                <option value="St Eustatius">St Eustatius</option>
                                <option value="St Helena">St Helena</option>
                                <option value="St Kitts-Nevis">St Kitts-Nevis</option>
                                <option value="St Lucia">St Lucia</option>
                                <option value="St Maarten">St Maarten</option>
                                <option value="St Pierre & Miquelon">St Pierre & Miquelon</option>
                                <option value="St Vincent & Grenadines">St Vincent & Grenadines</option>
                                <option value="Saipan">Saipan</option>
                                <option value="Samoa">Samoa</option>
                                <option value="Samoa American">Samoa American</option>
                                <option value="San Marino">San Marino</option>
                                <option value="Sao Tome & Principe">Sao Tome & Principe</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="Senegal">Senegal</option>
                                <option value="Seychelles">Seychelles</option>
                                <option value="Sierra Leone">Sierra Leone</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Slovakia">Slovakia</option>
                                <option value="Slovenia">Slovenia</option>
                                <option value="Solomon Islands">Solomon Islands</option>
                                <option value="Somalia">Somalia</option>
                                <option value="South Africa">South Africa</option>
                                <option value="Spain">Spain</option>
                                <option value="Sri Lanka">Sri Lanka</option>
                                <option value="Sudan">Sudan</option>
                                <option value="Suriname">Suriname</option>
                                <option value="Swaziland">Swaziland</option>
                                <option value="Sweden">Sweden</option>
                                <option value="Switzerland">Switzerland</option>
                                <option value="Syria">Syria</option>
                                <option value="Tahiti">Tahiti</option>
                                <option value="Taiwan">Taiwan</option>
                                <option value="Tajikistan">Tajikistan</option>
                                <option value="Tanzania">Tanzania</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Togo">Togo</option>
                                <option value="Tokelau">Tokelau</option>
                                <option value="Tonga">Tonga</option>
                                <option value="Trinidad & Tobago">Trinidad & Tobago</option>
                                <option value="Tunisia">Tunisia</option>
                                <option value="Turkey">Turkey</option>
                                <option value="Turkmenistan">Turkmenistan</option>
                                <option value="Turks & Caicos Is">Turks & Caicos Is</option>
                                <option value="Tuvalu">Tuvalu</option>
                                <option value="Uganda">Uganda</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Ukraine">Ukraine</option>
                                <option value="United Arab Erimates">United Arab Emirates</option>
                                <option value="United States of America">United States of America</option>
                                <option value="Uraguay">Uruguay</option>
                                <option value="Uzbekistan">Uzbekistan</option>
                                <option value="Vanuatu">Vanuatu</option>
                                <option value="Vatican City State">Vatican City State</option>
                                <option value="Venezuela">Venezuela</option>
                                <option value="Vietnam">Vietnam</option>
                                <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
                                <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
                                <option value="Wake Island">Wake Island</option>
                                <option value="Wallis & Futana Is">Wallis & Futana Is</option>
                                <option value="Yemen">Yemen</option>
                                <option value="Zaire">Zaire</option>
                                <option value="Zambia">Zambia</option>
                                <option value="Zimbabwe">Zimbabwe</option>
                            </select>
                        </div>
                    </div>

                    <h4
                        style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                        QUALIFICATION - O-LEVEL</h4>

                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <select class="form-control" name="school_type" value="">
                                <option value="">Select type of school</option>
                                <option value="Government">Government </option>
                                <option value="Private">Private </option>

                            </select>
                        </div>
                        <div class="col-sm-4">
                            <input name="school_name" class="form-control" type="text"
                                placeholder="O-level School Name">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="stream" value="">
                                <option value="">Select stream </option>
                                <option value="Arts">Arts </option>
                                <option value="Science">Science </option>
                                <option value="Commerce">Commerce </option>
                            </select>
                        </div>
                    </div>

                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <input name="year" type="text" class="form-control" placeholder="O-level Completion year">
                        </div>
                        <div class="col-sm-4">
                            <input name="result" type="text" class="form-control"
                                placeholder="O-level result(e.g 1.7,2.3 etc)">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="grading" id="ograding" value="">
                                <option value="">Select Grading system </option>
                                <option value="GPA">GPA </option>
                                <option value="Dvision">Dvision </option>
                            </select>
                        </div>
                    </div>


                    <h4
                        style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                        QUALIFICATION - A-LEVEL</h4>

                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <select class="form-control" name="school_type_second" value="">
                                <option value="">Select type of school</option>
                                <option value="Government">Government </option>
                                <option value="Private">Private </option>

                            </select>
                        </div>
                        <div class="col-sm-4">
                            <input name="school_name_second" class="form-control" type="text"
                                placeholder="A-level School Name">
                        </div>
                        <div class="col-sm-4">
                            <input name="combination" type="text" class="form-control"
                                placeholder="A-level combination">
                        </div>
                    </div>

                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <input name="second_year" type="text" class="form-control"
                                placeholder="A-level Completion year">
                        </div>
                        <div class="col-sm-4">
                            <input name="second_result" type="text" class="form-control"
                                placeholder="A-level result(e.g 1.7,2.3 etc)">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="second_grading" id="ograding" value="">
                                <option value="">Select Grading system </option>
                                <option value="GPA">GPA </option>
                                <option value="Dvision">Dvision </option>
                            </select>
                        </div>
                    </div>


                    <h4
                        style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                        OTHER QUALIFICATION </h4>

                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <input name="institute_name" type="text" class="form-control"
                                placeholder="Institution name">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="level" id="ilevel" value="">
                                <option value="">level attained </option>
                                <option value="Certificate">Certificate </option>
                                <option value="Diploma">Diploma </option>
                                <option value="Advance Diploma ">Advance Diploma </option>
                                <option value="Bachelor">Bachelor </option>
                                <option value="Post Graduate">Post Graduate </option>
                                <option value="Masters">Masters </option>
                                <option value="PHD">PHD </option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="emp_type" id="empptype" value="">
                                <option value="">Your employement type</option>
                                <option value="Government">Government </option>
                                <option value="Private">Private </option>
                                <option value="self">self </option>
                                <option value="others">others </option>

                            </select>
                        </div>
                    </div>

                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <input name="s_complete_year" type="text" class="form-control"
                                placeholder="Studies Completion year">
                        </div>
                        <div class="col-sm-4">
                            <input name="final_result" type="text" class="form-control"
                                placeholder="Your final year result">
                        </div>
                        <div class="col-sm-4">
                            <input name="course_taken" type="text" class="form-control" placeholder="Course taken">
                        </div>
                    </div>


                    <h4
                        style="background:#2d3566; color:#FFFFFF; padding:10px 10px 10px 10px; font-size:18px; margin-bottom:10px;">
                        OTHER INFO</h4>

                    <div class="row input-row" style="margin-bottom:10px;">
                        <div class="col-sm-4">
                            <select class="form-control" name="budget" value="">
                                <option value="">Select your Possible budget </option>
                                <option value="1250-1500">$1250-$1500 </option>
                                <option value="1500-3000">$1500-$3000 </option>
                                <option value="3000-5000">$3000-$5000 </option>
                                <option value="5000-7000">$5000-$7000 </option>
                                <option value="7000-10000">$7000-$10000 </option>
                                <option value="10000-15000">$10000-$15000 </option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="passport" value="">
                                <option value="">Do you have passport?</option>
                                <option value="Yes">Yes </option>
                                <option value="No">No </option>

                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="intake" value="">
                                <option value="">Select intake </option>
                                <option value="2022-2023">2022-2023 </option>
                                <option value="2023-2024">2023-2024 </option>
                            </select>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn" name="online_form">Submit <span class="icon-more-icon"></span></button>
                            <div class="msg"></div>
                        </div>
                    </div>

                </form>
                <?php      if(isset($_POST['online_form'])){
$valid = 1;

if(empty($_POST['agent_code']))
{
    $valid = 0;
    $error_message .= 'Please enter your name.<br>';
}
$know = $_REQUEST['know'];
$agent_name 		    =$_REQUEST['agent_name'];
$agent_code			    =$_REQUEST['agent_code'];
$first_name			    =$_REQUEST['first_name'];
$middle_name			=$_REQUEST['middle_name'];
$last_name				=$_REQUEST['last_name'];
$phone			        =$_REQUEST['phone'];
$email		            =$_REQUEST['email'];
$city			        =$_REQUEST['city'];
$sname			        =$_REQUEST['sname'];
$sphone			        =$_REQUEST['sphone'];
$occupation			    =$_REQUEST['occupation'];
$course			        =$_REQUEST['course'];
$course_name			=$_REQUEST['course_name'];
$country			    =$_REQUEST['country'];
$second_course			=$_REQUEST['second_course'];
$second_course_name		=$_REQUEST['second_course_name'];
$second_country			=$_REQUEST['second_country'];
$third_course			=$_REQUEST['third_course'];
$third_course_name		=$_REQUEST['third_course_name'];
$third_country			=$_REQUEST['third_country'];
$school_type			=$_REQUEST['school_type'];
$school_name			=$_REQUEST['school_name'];

$stream			        =$_REQUEST['stream'];
$years			        =$_REQUEST['year'];
$result			        =$_REQUEST['result'];
$grading			    =$_REQUEST['grading'];
$school_type_second		=$_REQUEST['school_type_second'];
$school_name_second		=$_REQUEST['school_name_second'];
$combination			=$_REQUEST['combination'];
$second_year			=$_REQUEST['second_year'];
$second_result			=$_REQUEST['second_result'];
$second_grading			=$_REQUEST['second_grading'];
$institute_name			=$_REQUEST['institute_name'];
$levels			        =$_REQUEST['level'];
$emp_type			    =$_REQUEST['emp_type'];
$s_complete_year		=$_REQUEST['s_complete_year'];
$final_result			=$_REQUEST['final_result'];
$course_taken			=$_REQUEST['course_taken'];
$budget			        =$_REQUEST['budget'];
$passport			    =$_REQUEST['passport'];
$intake			        =$_REQUEST['intake'];



if($valid == 1) {

    // saving into the database
    $statement = $pdo->prepare("INSERT INTO tbl_students (intake,passport,
    budget,course_taken,final_result,s_complete_year,emp_type,levels,
    institute_name,second_grading,second_result,second_year,combination,
    school_name_second,school_type_second,grading,result,years,stream,school_name,
    school_type,third_country,third_course_name,second_country,second_course_name,second_course,country,
    course_name,course,occupation,sphone,sname,city,email,phone,last_name,
    middle_name,first_name,agent_code,agent_name,know) VALUES (?,?,?,?,?,?,?,?,?,?
    ,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

    $statement->execute(array($intake,$passport,$budget,$course_taken,$final_result,$s_complete_year,$emp_type,
    $levels,$institute_name,$second_grading,$second_result,$second_year,$combination,$school_name_second,
    $school_type_second,$grading,$result,$years,$stream,$school_name,$school_type,$third_country,
    $third_course_name,$second_country,$second_course_name,$second_course,$country,$course_name,$course,
    $occupation,$sphone,$sname,$city,$email,$phone,$last_name,$middle_name,$first_name,$agent_code,$agent_name,$know));

    $success_message = 'Information has been sent successfully.';
}

                }
        ?>
            </div>
        </div>

</section>

<?php endif; ?>



<?php if($page_layout == 'FAQ Page Layout'): ?>
<section class="faq">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <?php
				$i=0;
				$j=0;
				$statement = $pdo->prepare("SELECT * FROM tbl_faq_category ORDER BY faq_category_id ASC");
				$statement->execute();
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
				foreach ($result as $row) {
					$i++;
					?>
                <h1><?php echo $row['faq_category_name']; ?></h1>
                <div class="panel-group" id="accordion<?php echo $i; ?>" role="tablist" aria-multiselectable="true">
                    <?php
						$statement1 = $pdo->prepare("SELECT * FROM tbl_faq WHERE faq_category_id=?");
						$statement1->execute(array($row['faq_category_id']));
						$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);							
						foreach ($result1 as $row1) {
							$j++;
							?>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="heading1">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse"
                                    data-parent="#accordion<?php echo $i; ?>" href="#collapse<?php echo $j; ?>"
                                    aria-expanded="false" aria-controls="collapse<?php echo $j; ?>">
                                    <?php echo $row1['faq_title']; ?>
                                </a>
                            </h4>

                        </div>
                        <div id="collapse<?php echo $j; ?>" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="heading1">
                            <div class="panel-body">
                                <?php echo $row1['faq_content']; ?>
                            </div>
                        </div>
                    </div>
                    <?php
						}
						?>
                </div>
                <?php
				}
				?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>



<?php if($page_layout == 'Photo Gallery Page Layout'): ?>
<section class="gallery">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <ul class="gallery-menu">
                    <li class="filter" data-filter="all" data-role="button">All</li>
                    <?php
					$statement = $pdo->prepare("SELECT * FROM tbl_category_photo WHERE status=?");
					$statement->execute(array('Active'));
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
					foreach ($result as $row) {
						$temp_string = strtolower($row['p_category_name']);
    					$temp_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $temp_string);
    					?>
                    <li class="filter" data-filter=".<?php echo $temp_slug; ?>" data-role="button">
                        <?php echo $row['p_category_name']; ?></li>
                    <?php
					}
					?>
                </ul>

                <div id="mix-container">
                    <?php
					$i=0;
					$statement = $pdo->prepare("SELECT
					                           	t1.photo_id,
												t1.photo_caption,
												t1.photo_name,
												t1.p_category_id,
												t2.p_category_id,
												t2.p_category_name,
												t2.status
					                            FROM tbl_photo t1
					                            JOIN tbl_category_photo t2
					                            ON t1.p_category_id = t2.p_category_id 
					                            ");
					$statement->execute();
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
					foreach ($result as $row) {
						$i++;
						$temp_string = strtolower($row['p_category_name']);
    					$temp_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $temp_string);
						?>
                    <div class="col-md-4 mix <?php echo $temp_slug; ?> all" data-my-order="<?php echo $i; ?>">
                        <div class="inner">
                            <div class="photo"
                                style="background-image:url(<?php echo BASE_URL; ?>assets/uploads/<?php echo $row['photo_name']; ?>);">
                            </div>
                            <div class="overlay"></div>
                            <div class="icons">
                                <div class="icons-inner">
                                    <a class="gallery-photo"
                                        href="<?php echo BASE_URL; ?>assets/uploads/<?php echo $row['photo_name']; ?>"><i
                                            class="fa fa-search-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
					}
					?>

                </div>

            </div>
        </div>
    </div>
</section>
<?php endif; ?>





<?php if($page_layout == 'Video Gallery Page Layout'): ?>
<section class="gallery">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <ul class="gallery-menu">
                    <li class="filter" data-filter="all" data-role="button">All</li>
                    <?php
					$statement = $pdo->prepare("SELECT * FROM tbl_category_video WHERE status=?");
					$statement->execute(array('Active'));
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
					foreach ($result as $row) {
						$temp_string = strtolower($row['v_category_name']);
    					$temp_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $temp_string);
    					?>
                    <li class="filter" data-filter=".<?php echo $temp_slug; ?>" data-role="button">
                        <?php echo $row['v_category_name']; ?></li>
                    <?php
					}
					?>
                </ul>

                <div id="mix-container">
                    <?php
					$i=0;
					$statement = $pdo->prepare("SELECT
					                           	t1.video_id,
												t1.video_title,
												t1.video_iframe,
												t1.v_category_id,
												t2.v_category_id,
												t2.v_category_name,
												t2.status
					                            FROM tbl_video t1
					                            JOIN tbl_category_video t2
					                            ON t1.v_category_id = t2.v_category_id 
					                            ");
					$statement->execute();
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
					foreach ($result as $row) {
						$i++;
						$temp_string = strtolower($row['v_category_name']);
    					$temp_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $temp_string);
						?>
                    <div class="col-md-4 mix <?php echo $temp_slug; ?> all" data-my-order="<?php echo $i; ?>">
                        <div class="inner viframe">
                            <?php echo $row['video_iframe']; ?>
                        </div>
                    </div>
                    <?php
					}
					?>

                </div>

            </div>
        </div>
    </div>
</section>
<?php endif; ?>



<?php if($page_layout == 'Blog Page Layout'): ?>
<section class="blog">
    <div class="container">
        <div class="row">
            <div class="col-md-9">

                <!-- Blog Classic Start -->
                <div class="blog-grid">
                    <div class="row">
                        <div class="col-md-12">


                            <?php
							$statement = $pdo->prepare("SELECT * FROM tbl_news ORDER BY news_id DESC");
							$statement->execute();
							$total = $statement->rowCount();
							?>

                            <?php if(!$total): ?>
                            <p style="color:red;">Sorry! No News is found.</p>
                            <?php else: ?>




                            <?php
/* ===================== Pagination Code Starts ================== */
		$adjacents = 10;	
		
		$statement = $pdo->prepare("SELECT * FROM tbl_news ORDER BY news_id DESC");
		$statement->execute();
		$total_pages = $statement->rowCount();
		
		$targetpage = $_SERVER['PHP_SELF'];
		$limit = 5;                                 
		$page = @$_GET['page'];
		if($page) 
			$start = ($page - 1) * $limit;          
		else
			$start = 0;	
		

		$statement = $pdo->prepare("SELECT
								   t1.news_title,
		                           t1.news_slug,
		                           t1.news_content,
		                           t1.news_date,
		                           t1.photo,
		                           t1.category_id,

		                           t2.category_id,
		                           t2.category_name,
		                           t2.category_slug
		                           FROM tbl_news t1
		                           JOIN tbl_category t2
		                           ON t1.category_id = t2.category_id 		                           
		                           ORDER BY t1.news_id 
		                           LIMIT $start, $limit");
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		
		
		$s1 = $_REQUEST['slug'];
				
		if ($page == 0) $page = 1;                  
		$prev = $page - 1;                          
		$next = $page + 1;                          
		$lastpage = ceil($total_pages/$limit);      
		$lpm1 = $lastpage - 1;   
		$pagination = "";
		if($lastpage > 1)
		{   
			$pagination .= "<div class=\"pagination\">";
			if ($page > 1) 
				$pagination.= "<a href=\"$targetpage?slug=$s1&page=$prev\">&#171; previous</a>";
			else
				$pagination.= "<span class=\"disabled\">&#171; previous</span>";    
			if ($lastpage < 7 + ($adjacents * 2))   //not enough pages to bother breaking it up
			{   
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?slug=$s1&page=$counter\">$counter</a>";                 
				}
			}
			elseif($lastpage > 5 + ($adjacents * 2))    //enough pages to hide some
			{
				if($page < 1 + ($adjacents * 2))        
				{
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a href=\"$targetpage?slug=$s1&page=$counter\">$counter</a>";                 
					}
					$pagination.= "...";
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=$lpm1\">$lpm1</a>";
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=$lastpage\">$lastpage</a>";       
				}
				elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=1\">1</a>";
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=2\">2</a>";
					$pagination.= "...";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a href=\"$targetpage?slug=$s1&page=$counter\">$counter</a>";                 
					}
					$pagination.= "...";
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=$lpm1\">$lpm1</a>";
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=$lastpage\">$lastpage</a>";       
				}
				else
				{
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=1\">1</a>";
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=2\">2</a>";
					$pagination.= "...";
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a href=\"$targetpage?slug=$s1&page=$counter\">$counter</a>";                 
					}
				}
			}
			if ($page < $counter - 1) 
				$pagination.= "<a href=\"$targetpage?slug=$s1&page=$next\">next &#187;</a>";
			else
				$pagination.= "<span class=\"disabled\">next &#187;</span>";
			$pagination.= "</div>\n";       
		}
		/* ===================== Pagination Code Ends ================== */
		?>

                            <?php
							foreach ($result as $row) {
								?>
                            <div class="post-item">
                                <div class="image-holder">
                                    <img class="img-responsive"
                                        src="<?php echo BASE_URL; ?>assets/uploads/<?php echo $row['photo']; ?>"
                                        alt="<?php echo $row['news_title']; ?>">
                                </div>
                                <div class="text">
                                    <h3><a
                                            href="<?php echo BASE_URL; ?>news/<?php echo $row['news_slug']; ?>"><?php echo $row['news_title']; ?></a>
                                    </h3>
                                    <ul class="status">
                                        <li><i class="fa fa-tag"></i>Category: <a
                                                href="<?php echo BASE_URL; ?>category/<?php echo $row['category_slug']; ?>"><?php echo $row['category_name']; ?></a>
                                        </li>
                                        <li><i class="fa fa-calendar"></i>Date: <?php echo $row['news_date']; ?></li>
                                    </ul>
                                    <p>
                                        <?php echo substr($row['news_content'],0,200).' ...'; ?>
                                    </p>
                                    <p class="button">
                                        <a href="<?php echo BASE_URL; ?>news/<?php echo $row['news_slug']; ?>">Read
                                            More</a>
                                    </p>
                                </div>
                            </div>
                            <?php
							}
							?>
                            <?php endif; ?>

                        </div>

                        <div class="col-md-12">
                            <?php if($total): ?>
                            <?php echo $pagination; ?>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
                <!-- Blog Classic End -->

            </div>
            <div class="col-md-3">

                <?php require_once('sidebar.php'); ?>

            </div>




        </div>
    </div>
</section>
<?php endif; ?>



<?php if($page_layout == 'Attorney Page Layout'): ?>
<section class="attorney-v3">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <!-- Attorney Container Start -->
                <div class="attorney-inner">

                    <?php
					$statement = $pdo->prepare("SELECT
												
												t1.id,
												t1.name,
												t1.slug,
												t1.designation_id,
												t1.photo,
												t1.degree,
												t1.detail,
												t1.facebook,
												t1.twitter,
												t1.linkedin,
												t1.youtube,
												t1.google_plus,
												t1.instagram,
												t1.flickr,
												t1.address,
												t1.practice_location,
												t1.phone, 
												t1.email,
												t1.website,
												t1.status,

												t2.designation_id,
												t2.designation_name
						
					                            FROM tbl_attorney t1
					                            JOIN tbl_designation t2
					                            ON t1.designation_id = t2.designation_id
					                            WHERE t1.status=?
					                            ");
					$statement->execute(array('Active'));
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
					foreach ($result as $row) {
						?>
                    <div class="col-md-3 item">
                        <div class="inner">
                            <div class="thumb">
                                <div class="photo"
                                    style="background-image:url(<?php echo BASE_URL; ?>assets/uploads/<?php echo $row['photo']; ?>)">
                                </div>
                                <div class="overlay"></div>
                                <div class="social-icons">
                                    <ul>
                                        <?php if($row['facebook']!=''): ?>
                                        <li><a href="<?php echo $row['facebook']; ?>" target="_blank"><i
                                                    class="fa fa-facebook"></i></a></li>
                                        <?php endif; ?>

                                        <?php if($row['twitter']!=''): ?>
                                        <li><a href="<?php echo $row['twitter']; ?>" target="_blank"><i
                                                    class="fa fa-twitter"></i></a></li>
                                        <?php endif; ?>

                                        <?php if($row['linkedin']!=''): ?>
                                        <li><a href="<?php echo $row['linkedin']; ?>" target="_blank"><i
                                                    class="fa fa-linkedin"></i></a></li>
                                        <?php endif; ?>

                                        <?php if($row['youtube']!=''): ?>
                                        <li><a href="<?php echo $row['youtube']; ?>" target="_blank"><i
                                                    class="fa fa-youtube"></i></a></li>
                                        <?php endif; ?>

                                        <?php if($row['google_plus']!=''): ?>
                                        <li><a href="<?php echo $row['google_plus']; ?>" target="_blank"><i
                                                    class="fa fa-google-plus"></i></a></li>
                                        <?php endif; ?>

                                        <?php if($row['instagram']!=''): ?>
                                        <li><a href="<?php echo $row['instagram']; ?>" target="_blank"><i
                                                    class="fa fa-instagram"></i></a></li>
                                        <?php endif; ?>

                                        <?php if($row['flickr']!=''): ?>
                                        <li><a href="<?php echo $row['flickr']; ?>" target="_blank"><i
                                                    class="fa fa-flickr"></i></a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="text">
                                <h3><a
                                        href="<?php echo BASE_URL; ?>attorney/<?php echo $row['slug']; ?>"><?php echo $row['name']; ?></a>
                                </h3>
                                <h4><?php echo $row['designation_name']; ?></h4>
                                <p class="button">
                                    <a href="<?php echo BASE_URL; ?>attorney/<?php echo $row['slug']; ?>">See Full
                                        Profile</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
					}
					?>

                </div>
                <!-- Attorney Container End -->

            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once('footer.php'); ?>