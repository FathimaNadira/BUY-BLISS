<?php
session_start();
// Redirect to login page if the user is not logged in
// Check if the admin is logged in, if not, redirect to login page
if (!isset($_SESSION['admin_logged_in'])) {
	header("Location: ../client/index.php");
	exit();
}

include("../config/connect.php");
include("../config/function.php");

// Check if product_id is provided in the URL
if (isset($_GET['product_id'])) {
	$product_id = $_GET['product_id'];

	// Fetch product details based on product_id
	$product = getProduct($conn, $product_id);

	if (!$product) {
		// Product not found, you can handle this case accordingly
		echo "Product not found!";
		exit;
	}
} else {
	// If product_id is not provided in the URL, redirect to index.php or handle it accordingly
	header("Location: index.php");
	exit;
}

// include the head
include("../inc/head.php");

// include the header
include("../inc/header.php") ?>

<!-- include the style in this page only -->
<style>
	.icon-hover:hover {
		border-color: #3b71ca !important;
		background-color: white !important;
		color: #3b71ca !important;
	}

	.icon-hover:hover i {
		color: #3b71ca !important;
	}

	.nav-tabs .nav-item .nav-link:not(.active) {
		color: #6c757d;
		/* Change the color as needed */
		background-color: transparent;
		/* Remove the background color */
		border-color: transparent;
		/* Remove the border color */
	}

	.desc {
		height: 220px;
		overflow: hidden;
	}
</style>

<!-- content -->
<section class="py-5">
	<div class="container">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h2>Product Details</h2>
			<!-- <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
				<i class="fas fa-plus"></i> Add New Product
			</button> -->
		</div>

		<!-- Add Product Modal -->
		<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add New Product</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
					<div class="modal-body">
						<form id="addProductForm" action="add_product.php" method="POST" enctype="multipart/form-data">
							<div class="row g-3">
								<div class="col-md-6">
									<label class="form-label">Product Name</label>
									<input type="text" class="form-control" name="product_name" required>
								</div>
								<div class="col-md-6">
									<label class="form-label">Price (RS.)</label>
									<input type="number" class="form-control" name="price" step="0.01" required>
								</div>
								<div class="col-md-6">
									<label class="form-label">Category</label>
									<select class="form-select" name="category" required>
										<option value="">Select Category</option>
										<option value="Electronics">Electronics</option>
										<option value="Clothing">Clothing</option>
										<option value="Books">Books</option>
										<option value="Sports">Sports</option>
										<option value="Cosmetics">Cosmetics</option>
										<option value="Home Accessories">Home Accessories</option>
									</select>
								</div>
								<div class="col-md-6">
									<label class="form-label">Quantity</label>
									<input type="number" class="form-control" name="quantity" required>
								</div>
								<div class="col-12">
									<label class="form-label">Description</label>
									<textarea class="form-control" name="description" rows="3" required></textarea>
								</div>
								<div class="col-md-6">
									<label class="form-label">Product Image</label>
									<input type="file" class="form-control" name="image" accept="image/*" required>
								</div>
								<div class="col-md-6">
									<label class="form-label">Vendor</label>
									<select class="form-select" name="vendor_id" required>
										<?php
										$vendors = $conn->query("SELECT vendor_id, vendor_name FROM vendor");
										while($vendor = $vendors->fetch_assoc()) {
											echo "<option value='".$vendor['vendor_id']."'>".$vendor['vendor_name']."</option>";
										}
										?>
									</select>
								</div>
							</div>
							<div class="mt-3">
								<button type="submit" class="btn btn-primary">Add Product</button>
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="row gx-5">
			<aside class="col-lg-6">
				<div class="border rounded-4 mb-3 d-flex justify-content-center">
					<a data-fslightbox="mygallery" class="rounded-4" target="_blank" data-type="image" href="<?php echo $product['image']; ?>">
						<img style="max-width: 100%; height: 70vh; margin: auto;" class="rounded-4 fit" src="../uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['product_name']; ?>" />
					</a>
				</div>
			</aside>
			<main class="col-lg-6">
				<div class="ps-lg-3">
					<h4 class="title text-dark">
						<?php echo $product['product_name']; ?>
					</h4>
					<div class="d-flex flex-row my-3">
						<!-- Star ratings and orders count -->
						<div class="text-warning mb-1 me-2">
							<i class="fa fa-star"></i>
							<i class="fa fa-star"></i>
							<i class="fa fa-star"></i>
							<i class="fa fa-star"></i>
							<i class="fas fa-star-half-alt"></i>
							<span class="ms-1">4.5</span>
						</div>
						<span class="text-muted"><i class="fas fa-shopping-basket fa-sm mx-1"></i><?php echo $product['quantity']; ?> items in stock</span>
						<span class="text-success ms-2">In stock</span>
					</div>

					<div class="mb-3">
						<!-- Product price -->
						<span class="h5">RS.<?php echo $product['price']; ?></span>
					</div>

					<p class="desc">
						<!-- Product description -->
						<?php echo $product['description']; ?>
					</p>

					<div class="row">
						<!-- Product details -->
						<dt class="col-3">Name:</dt>
						<dd class="col-9"><?php echo $product['product_name']; ?></dd>

						<dt class="col-3">Date Created:</dt>
						<dd class="col-9"><?php echo $product['create_date']; ?></dd>

						<dt class="col-3">Category:</dt>
						<dd class="col-9"><?php echo $product['category']; ?></dd>

						<dt class="col-3">Seller Name:</dt>
						<dd class="col-9"><?php echo $product['vendor_name']; ?></dd>
					</div>

					<hr />

					<!-- Add action buttons -->
					<div class="d-flex gap-2">
						<a href="update_product.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-primary">Edit Product</a>
						<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
							Delete Product
						</button>
					</div>

					<!-- Delete Modal -->
					<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Confirm Delete</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
									Are you sure you want to delete this product?
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
									<a href="delete_product.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-danger">Delete</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
	</div>
</section>

<!-- content -->
<section class="py-5 bg-white">
	<div class="container">
		<div class="row gx-4">
			<div class="col-lg-12 mb-4">
				<div class="border rounded-2 px-4 py-3">
					<!-- Nav tabs -->
					<ul class="nav nav-tabs" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<a class="nav-link active" id="specification-tab" data-bs-toggle="tab" href="#specification" role="tab" aria-controls="specification" aria-selected="true">Specification</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" id="seller-profile-tab" data-bs-toggle="tab" href="#seller-profile" role="tab" aria-controls="seller-profile" aria-selected="false">Seller Profile</a>
						</li>
					</ul>
					<!-- Tab panes -->
					<div class="tab-content mt-3" id="myTabContent">
						<div class="tab-pane fade show active" id="specification" role="tabpanel" aria-labelledby="specification-tab">
							<p>
								<?php echo $product['description']; ?>
							</p>
						</div>
						<div class="tab-pane fade mb-2" id="seller-profile" role="tabpanel" aria-labelledby="seller-profile-tab">
							<?php
							if ($product['vendor_id']) {
							?>
								<ul class="list-group">
									<li class="list-group-item">
										<span class="fw-bold">Seller Name : </span> <?php echo $product['vendor_name']; ?>
									</li>
									<li class="list-group-item">
										<span class="fw-bold">Seller Email : </span> <?php echo $product['vendor_email']; ?>
									</li>
									<li class="list-group-item">
										<span class="fw-bold">Register Date : </span> <?php echo $product['register_date']; ?>
									</li>
									<li class="list-group-item">
										<span class="fw-bold">Total Orders : </span> <?php echo getVendorOrders($conn, $product['vendor_id']); ?>
									</li>
								</ul>
							<?php } else {
								echo "Seller information not found.";
							}
							?>

						</div>
					</div>
					<!-- Pills content -->


				</div>
			</div>
		</div>
	</div>
	<!-- Start Related Products Section -->
	<!-- End Related Products Section -->
	<br><br><br>
</section>
<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/tiny-slider.js"></script>
<script src="../js/custom.js"></script>