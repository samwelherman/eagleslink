<?php require_once('header.php'); ?>
		
<?php
// Preventing the direct access of this page.
if(!isset($_REQUEST['slug']))
{
	header('location: '.BASE_URL);
	exit;
}

// Getting the news detailed data from the news id
$statement = $pdo->prepare("SELECT
							t1.news_title,
							t1.news_slug,
							t1.news_content,
							t1.news_date,
							t1.publisher,
							t1.photo,
							t1.category_id,
							
							t2.category_id,
							t2.category_name,
							t2.category_slug

                           	FROM tbl_news t1
                           	JOIN tbl_category t2
                           	ON t1.category_id = t2.category_id
                           	WHERE t1.news_slug=?");
$statement->execute(array($_REQUEST['slug']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) {
	$news_title    = $row['news_title'];
	$news_slug    = $row['news_slug'];
	$news_content  = $row['news_content'];
	$news_date     = $row['news_date'];
	$publisher     = $row['publisher'];
	$photo         = $row['photo'];
	$category_id   = $row['category_id'];
	$category_slug = $row['category_slug'];
	$category_name = $row['category_name'];
}

// Update data for view count for this news page
// Getting current view count
$statement = $pdo->prepare("SELECT * FROM tbl_news WHERE news_slug=?");
$statement->execute(array($_REQUEST['slug']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) 
{
	$current_total_view = $row['total_view'];
}
$updated_total_view = $current_total_view+1;

// Updating database for view count
$statement = $pdo->prepare("UPDATE tbl_news SET total_view=? WHERE news_slug=?");
$statement->execute(array($updated_total_view,$_REQUEST['slug']));
?>


<!-- Banner Start -->
<div class="page-banner" style="background:none;">
	<div class="overlay"></div>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="banner-text">
					<h1><?php echo $news_title; ?></h1>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Banner End -->


<!-- Blog Start -->
<section class="blog">
	<div class="container">
		<div class="row">
			<div class="col-md-9">
				
				<!-- Blog Classic Start -->
				<div class="blog-grid">
					<div class="row">
						<div class="col-md-12">
							

							<!-- Post Item Start -->
							<div class="post-item">
								<div class="image-holder">
									<img class="img-responsive" src="<?php echo BASE_URL; ?>assets/uploads/<?php echo $photo ?>" alt="<?php echo $news_title; ?>">
								</div>
								<div class="text">
									<h3><?php echo $news_title; ?></h3>
									<ul class="status">
										<li><i class="fa fa-tag"></i>Category: <a href="<?php echo BASE_URL; ?>category/<?php echo $category_slug; ?>"><?php echo $category_name; ?></a></li>
										<li><i class="fa fa-calendar"></i>Date: <?php echo $news_date; ?></li>
									</ul>
									<p>
										<?php echo $news_content; ?>
									</p>
									<h3>Share This</h3>
									<div class="share_buttons">
										<a class="facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo BASE_URL; ?>news/<?php echo $news_slug; ?>&t=<?php echo $news_title; ?>"><i class="fa fa-facebook"></i></a>

										<a class="twitter" target="_blank" href="https://twitter.com/share?text=<?php echo $news_title; ?>&url=<?php echo BASE_URL; ?>news/<?php echo $news_slug; ?>"><i class="fa fa-twitter"></i></a>

										<a class="pinterest" target="_blank" href="https://www.pinterest.com/pin/create/button/?description=<?php echo $news_title; ?>&media=&url=<?php echo BASE_URL; ?>news/<?php echo $news_slug; ?>"><i class="fa fa-pinterest"></i></a>

										<a class="linkedin" target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo BASE_URL; ?>news/<?php echo $news_slug; ?>&title=<?php echo $news_title; ?>"><i class="fa fa-linkedin"></i></a>
									</div>


								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<h3>Comments</h3>
									<?php
									// Getting the full url of the current page
									$final_url = BASE_URL . 'news/' . $_REQUEST['slug'];
									?>
									<!-- Facebook Comment Main Code (got from facebook website) -->
									<div class="fb-comments" data-href="<?php echo $final_url; ?>" data-numposts="5"></div>
								</div>
							</div>
							<!-- Post Item End -->

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
<!-- Blog End -->


<?php require_once('footer.php'); ?>