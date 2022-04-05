<?php
ob_start();
session_start();
include("admin/config.php");
include("admin/functions.php");
$error_message = '';
$success_message = '';

$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$base_url .= "://".$_SERVER['HTTP_HOST'];
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
define("BASE_URL", $base_url);
?>
<?php
// Getting the basic data for the website from database
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row)
{
	$logo = $row['logo'];
	$favicon = $row['favicon'];
	$contact_email = $row['contact_email'];
	$contact_phone = $row['contact_phone'];
	$recaptcha_status = $row['recaptcha_status'];
	$recaptcha_site_key = $row['recaptcha_site_key'];
	$preloader_status = $row['preloader_status'];
	$website_color = $row['website_color'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_menu_home WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row)
{
	$home_menu_name = $row['home_menu_name'];
	$home_menu_status = $row['home_menu_status'];
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>

	<!-- Meta Tags -->	
	<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>


	<!-- Showing the SEO related meta tags data -->
	<?php
	
	// Getting the current page URL
	$cur_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);

	if($cur_page == 'news.php')
	{
		$statement = $pdo->prepare("SELECT * FROM tbl_news WHERE news_slug=?");
		$statement->execute(array($_REQUEST['slug']));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) 
		{
		    $og_photo = $row['photo'];
		    $og_title = $row['news_title'];
		    $og_slug = $row['news_slug'];
			$og_description = substr(strip_tags($row['news_content']),0,200).'...';
			echo '<meta name="description" content="'.$row['meta_description'].'">';
			echo '<meta name="keywords" content="'.$row['meta_keyword'].'">';
			echo '<title>'.$row['meta_title'].'</title>';
		}
	}

	if($cur_page == 'page.php')
	{
		$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE page_slug=?");
		$statement->execute(array($_REQUEST['slug']));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) 
		{
			echo '<meta name="description" content="'.$row['meta_description'].'">';
			echo '<meta name="keywords" content="'.$row['meta_keyword'].'">';
			echo '<title>'.$row['meta_title'].'</title>';
		}
	}

	if($cur_page == 'category.php')
	{
		$statement = $pdo->prepare("SELECT * FROM tbl_category WHERE category_slug=?");
		$statement->execute(array($_REQUEST['slug']));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row)
		{
			echo '<meta name="description" content="'.$row['meta_description'].'">';
			echo '<meta name="keywords" content="'.$row['meta_keyword'].'">';
			echo '<title>'.$row['meta_title'].'</title>';
		}
	}

	if($cur_page == 'index.php')
	{
		$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) 
		{
			echo '<meta name="description" content="'.$row['meta_description_home'].'">';
			echo '<meta name="keywords" content="'.$row['meta_keyword_home'].'">';
			echo '<title>'.$row['meta_title_home'].'</title>';
		}
	}
	?>

	<!-- Favicon -->
	<link href="<?php echo BASE_URL; ?>assets/uploads/<?php echo $favicon; ?>" rel="shortcut icon" type="image/png">

	<!-- Stylesheets -->
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/slicknav.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/superfish.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/animate.css">
	
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/jquery.bxslider.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/hover.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/magnific-popup.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/toastr.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/responsive.css">

	<script src="<?php echo BASE_URL; ?>assets/js/modernizr.min.js"></script>

	<?php if($cur_page == 'news.php'): ?>
		<meta property="og:title" content="<?php echo $og_title; ?>">
		<meta property="og:type" content="website">
		<meta property="og:url" content="<?php echo BASE_URL; ?>news/<?php echo $og_slug; ?>">
		<meta property="og:description" content="<?php echo $og_description; ?>">
		<meta property="og:image" content="<?php echo BASE_URL; ?>assets/uploads/<?php echo $og_photo; ?>">
	<?php endif; ?>

	<script src='https://www.google.com/recaptcha/api.js'></script>

	<style>
		.top-bar,
		.sf-menu li li:hover,
		.slider p.button a,
		.attorney-v1 .owl-controls .owl-prev:hover, 
		.attorney-v1 .owl-controls .owl-next:hover,
		.testimonial-v1 .overlay,
		.news-v1 .owl-controls .owl-prev:hover, 
		.news-v1 .owl-controls .owl-next:hover,
		.footer-social,
		.footer-social .item ul li,
		.footer-col h3:after,
		.scrollup i,
		ul.gallery-menu li.filter.active,
		ul.gallery-menu li:hover,
		.gallery .inner .icons-inner a,
		.attorney-v3 .text p.button a:hover,
		.attorney-detail .contact .icon,
		.attorney-detail .attorney-single .social ul li a,
		.attorney-v2 .owl-controls .owl-prev:hover, 
		.attorney-v2 .owl-controls .owl-next:hover,
		.attorney-v2 .social-icons ul li a,
		.blog p.button a:hover,
		.widget-search button,
		.contact-v1 .cform-1 .btn-success {
			background: #<?php echo $website_color; ?>;
		}

		.sf-menu li:hover a,
		.service-v1 .heading h2,
		.service-v1 .text p.button a:hover,
		.attorney-v1 .heading h2,
		.attorney-v1 .text h3 a,
		.news-v1 .heading h2,
		.news-v1 .text h3 a:hover,
		.partner-v1 .heading h2,
		ul.gallery-menu li,
		.attorney-v3 .text h3 a:hover,
		.attorney-detail .attorney-detail-tab .nav-tabs>li>a,
		.attorney-detail .attorney-single .text p,
		.blog h3 a:hover,
		.blog .text ul.status li,
		.blog .text ul.status li a,
		.blog h4,
		.widget ul li a:hover {
			color: #<?php echo $website_color; ?>;
		}

		header,
		.heading-normal h2,
		.widget h4 {
			border-bottom-color: #<?php echo $website_color; ?>;
		}

		.footer-social .item ul li a,
		ul.gallery-menu li.filter.active,
		ul.gallery-menu li:hover,
		.widget-search input:focus,
		.widget-search button,
		.contact-v1 .cform-1 .btn-success {
			border-color: #<?php echo $website_color; ?>;
		}

		.service-v1 .text h3 a,
		.heading-normal h2 {
			color: #<?php echo $website_color; ?>!important;
		}
	</style>
	
</head>
<body>

<?php
// Getting Facebook comment code from the database
$statement = $pdo->prepare("SELECT * FROM tbl_comment WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) 
{
	echo $row['code_body'];
}
?>
	
	<?php if($preloader_status == 'On'): ?>
	<div id="preloader">
		<div id="status"></div>
	</div>
	<?php endif; ?>
	
	<div class="page-wrapper">
		
		<!-- Top Bar Start -->
		<div class="top-bar">
			<div class="container">
				<div class="row">
					<div class="col-md-4 top-contact">
						<div class="list">
							<i class="fa fa-envelope"></i> <a href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a>
						</div>
						<div class="list">
							<i class="fa fa-phone"></i> <?php echo $contact_phone; ?>
						</div>
					</div>
					<div class="col-md-8 top-social">
						<ul>
							<?php
							// Getting and showing all the social media icon URL from the database
							$statement = $pdo->prepare("SELECT * FROM tbl_social");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
							foreach ($result as $row) 
							{
								if($row['social_url']!='')
								{
									echo '<li><a href="'.$row['social_url'].'"><i class="'.$row['social_icon'].'"></i></a></li>';
								}
							}
							?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<!-- Top Bar End -->

		<!-- Header Start -->
		<header>
			<div class="container">
				<div class="row">
					<div class="col-md-3 logo">
						<a href="<?php echo BASE_URL; ?>"><img src="<?php echo BASE_URL; ?>assets/uploads/<?php echo $logo; ?>" alt="" width="200px" height="40px"></a>
					</div>
				
				
					<div class="col-md-9 nav-wrapper">
						<!-- Nav Start -->
						<div class="nav">
							<ul class="sf-menu">

								<?php if($home_menu_status == 'Show'): ?>
								<li>
									<a href="<?php echo BASE_URL; ?>">
										<span class="menu-title"><?php echo $home_menu_name; ?></span>
									</a>
								</li>
								<?php endif; ?>


								<?php
								// Showing the menu dynamically from the database
								$statement = $pdo->prepare("SELECT * FROM tbl_menu ORDER BY menu_order ASC");
								$statement->execute();
								$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
								foreach ($result as $row) 
								{
									echo '<li>';
									if($row['menu_parent']==0)
									{
										if($row['menu_type']=='Category')
										{
											echo '
											<a href="'.BASE_URL.'category/'.$row['category_or_page_slug'].'">
												<span class="menu-title">
													'.$row['menu_name'].'
												</span>
											</a>
											';
										}
										if($row['menu_type']=='Page')
										{
											echo '
											<a href="'.BASE_URL.'page/'.$row['category_or_page_slug'].'">
												<span class="menu-title">
													'.$row['menu_name'].'
												</span>
											</a>
											';
										}
										if($row['menu_type']=='Other')
										{
											echo '<a href="'.$row['menu_url'].'">';
											echo '
												<span class="menu-title">
													'.$row['menu_name'].'
												</span>
												';
											echo '</a>';
										}
									}

									$statement1 = $pdo->prepare("SELECT * FROM tbl_menu WHERE menu_parent=?");
									$statement1->execute(array($row['menu_id']));
									$total = $statement1->rowCount();
									if($total)
									{
										echo '<ul>';
										$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);							
										foreach ($result1 as $row1) 
										{
											echo '<li>';
											if($row1['menu_type']=='Category')
											{
												echo '<a href="'.BASE_URL.'category/'.$row1['category_or_page_slug'].'">';
											}
											if($row1['menu_type']=='Page')
											{
												echo '<a href="'.BASE_URL.'page/'.$row1['category_or_page_slug'].'">';
											}
											if($row1['menu_type']=='Other')
											{
												echo '<a href="'.$row1['menu_url'].'">';
											}											
											echo $row1['menu_name'];
											echo '</a>';
											echo '</li>';
										}
										echo '</ul>';
									}
									echo '</li>';
								}
								?>
							</ul>
						</div>
						<!-- Nav End -->

					</div>
				</div>
			</div>
		</header>
		<!-- Header End -->