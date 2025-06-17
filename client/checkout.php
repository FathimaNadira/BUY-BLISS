<?php
session_start();

// Include database connection and other necessary files
include("../config/connect.php");
include("../config/function.php");

// Initialize variables for subtotal and total
$subtotal = 0;
$total = 0;

// Check if the checkout form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Get client information from the form
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$client_name = $first_name . ' ' . $last_name;
	$wilaya = $_POST['wilaya'];
	$city = $_POST['city'];
	$phone = $_POST['phone'];

	// Initialize points to add for each order
	$pointsToAdd = 5;

	// Store each product in the cart as a separate order
	if (!empty($_SESSION['cart'])) {
		foreach ($_SESSION['cart'] as $product_id => $product) {
			// Fetch product details from database
			$row = getProductDetails($product_id, $conn);

			if ($row) {
				$product_id = $row['product_id']; // Use the product ID from the product table
				$vendor_id = $row['vendor_id']; // Use the vendor ID from the product table
				$product_name = $row['product_name'];
				$description = $row['description'];
				$price = $row['price'];
				$image = $row['image'];
				$seller_name = $row['seller_name'];
				$quantity = $product['quantity'];

				// Define default status
				$status = "pending";

				// Insert order into the database
				insertOrder($product_id, $vendor_id, $client_name, $city, $wilaya, $phone, $quantity, $status, $conn);

				// Update vendor points
				addVendorPoints($seller_name, $pointsToAdd, $conn);

				// Update quantity
				updateQuantity($product_id, $quantity, $status, $conn);
			}
		}
	}

	// Clear the cart after placing the order
	unset($_SESSION['cart']);

	// Redirect to the order confirmation page
	header("Location: thankyou.php");
	exit();
}

// include the head
include("../inc/head.php");

// include the header
include("../inc/header.php") ?>

<!-- Start Form -->
<div class="untree_co-section">
	<div class="container">
		<?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) { ?>
			<h2>Your cart is empty</h2>
			<div class="row mb-5">
				<div class="col-md-6 mb-3 mb-md-0">
					<br><a href="shop.php"><button class="btn btn-primary btn-sm btn-block">Continue Shopping</button></a>
				</div>
			</div>
			<script src="../js/bootstrap.bundle.min.js"></script>
			<script src="../js/tiny-slider.js"></script>
			<script src="../js/custom.js"></script>
		<?php } else {
		?>
			<div class="row">
				<div class="col-md-6 mb-5 mb-md-0">
					<h2 class="h3 mb-3 text-black">Billing Details</h2>
					<div class="p-3 p-lg-5 border bg-white">
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
							<div class="form-group row">
								<div class="col-md-6">
									<label for="c_fname" class="text-black">First Name <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="c_fname" name="first_name" required>
								</div>
								<div class="col-md-6">
									<label for="c_lname" class="text-black">Last Name <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="c_lname" name="last_name" required>
								</div>
							</div>

							<div class="form-group row">
								<div class="col-md-12">
									<label for="c_address" class="text-black">Address <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="c_address" name="c_address" placeholder="Street address">
								</div>
							</div>

							<div class="form-group mt-3">
								<input type="text" class="form-control" placeholder="Apartment, suite, unit etc. (optional)">
							</div>

							<div class="form-group row">
								<div class="col-md-6">
									<label for="c_state_country" class="text-black">District <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="c_state_country" name="wilaya" required>
								</div>
								<div class="col-md-6">
									<label for="c_postal_zip" class="text-black">City <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="c_postal_zip" name="city" required>
								</div>
							</div>

							<div class="form-group row mb-5">
								<div class="col-md-6">
									<label for="c_email_address" class="text-black">Posta / Zip <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="c_email_address" name="c_email_address">
								</div>
								<div class="col-md-6">
									<label for="c_phone" class="text-black">Phone <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="c_phone" name="phone" required placeholder="Phone Number">
								</div>
							</div>

							<div class="form-group mb-5">
								<label for="c_order_notes" class="text-black">Order Notes</label>
								<textarea name="c_order_notes" id="c_order_notes" cols="30" rows="5" class="form-control" placeholder="Write your notes here..."></textarea>
							</div>
							<div class="form-group">
								<input class="btn btn-primary btn-lg py-3 btn-block" type="button" value="Place Order" onclick="showPaymentModal()">
							</div>
						</form>
					</div>
				</div>
				<div class="col-md-6">

					<div class="row mb-5">
						<div class="col-md-12">
							<h2 class="h3 mb-3 text-black">Coupon Code</h2>
							<div class="p-3 p-lg-5 border bg-white">

								<label for="c_code" class="text-black mb-3">Enter your coupon code if you have
									one</label>
								<div class="input-group w-75 couponcode-wrap">
									<input type="text" class="form-control me-2" id="c_code" placeholder="Coupon Code" aria-label="Coupon Code" aria-describedby="button-addon2">
									<div class="input-group-append">
										<button class="btn btn-primary btn-sm" type="button" id="button-addon2">Apply</button>
									</div>
								</div>

							</div>
						</div>
					</div>

					<div class="row mb-5">
						<div class="col-md-12">
							<h2 class="h3 mb-3 text-black">Your Order</h2>
							<div class="p-3 p-lg-5 border bg-white">
								<table class="table site-block-order-table mb-5">
									<thead>
										<tr>
											<th>Product</th>
											<th>Total</th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach ($_SESSION['cart'] as $product_id => $product) {
											// Fetch product details from database
											$sql = "SELECT product_name, price FROM product WHERE product_id = ?";
											$stmt = $conn->prepare($sql);
											$stmt->bind_param("i", $product_id);
											$stmt->execute();
											$result = $stmt->get_result();
											$row = $result->fetch_assoc();

											if ($row) {
												$product_name = $row['product_name'];
												$price = $row['price'];
												$quantity = $product['quantity'];
												$product_total = $price * $quantity;
												$subtotal += $product_total;
										?>
												<tr>
													<td><?php echo $product_name; ?> <strong class="mx-2">x</strong> <?php echo $quantity; ?></td>
													<td><?php echo 'RS.' . number_format($product_total, 2); ?></td>
												</tr>
										<?php
											}
										}
										?>
										<tr>
											<td class="text-black font-weight-bold"><strong>Order Total</strong></td>
											<td class="text-black font-weight-bold"><strong><?php echo 'RS.' . number_format($subtotal, 2); ?></strong></td>
										</tr>
									</tbody>
								</table>



							</div>
						</div>
					</div>

				</div>
			</div>
			<!-- </form> -->
	</div>
</div>
<!-- End Form -->

<?php
		}

		// Check if the reset button is clicked
		if (isset($_POST['reset_cart'])) {
			resetCart();
		}
?>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#card">Card Payment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#cod">Cash on Delivery</a>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="card">
                        <form id="cardPaymentForm">
                            <div class="mb-3">
                                <label class="form-label">Card Number</label>
                                <input type="text" class="form-control" id="cardNumber" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="expiryDate" placeholder="MM/YY" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" required>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="processCardPayment()">Pay Now</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="cod">
                        <p>Pay with cash upon delivery.</p>
                        <button type="button" class="btn btn-primary" onclick="processCODPayment()">Confirm Order</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Animation Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="success-animation">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
                <h4 class="mt-3">Payment Successful!</h4>
                <p>Your order has been placed successfully.</p>
            </div>
        </div>
    </div>
</div>

<style>
.success-animation {
    margin: 20px auto;
}
.checkmark {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: block;
    stroke-width: 2;
    stroke: #4bb71b;
    stroke-miterlimit: 10;
    box-shadow: inset 0px 0px 0px #4bb71b;
    animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
}
.checkmark__circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #4bb71b;
    fill: none;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}
.checkmark__check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}
@keyframes stroke { 100% { stroke-dashoffset: 0; } }
@keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
@keyframes fill { 100% { box-shadow: inset 0px 0px 0px 30px #4bb71b; } }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentModalEl = document.getElementById('paymentModal');
    const successModalEl = document.getElementById('successModal');
    const paymentModal = new bootstrap.Modal(paymentModalEl);
    const successModal = new bootstrap.Modal(successModalEl);

    window.showPaymentModal = function() {
        const form = document.querySelector('form');
        if (form.checkValidity()) {
            paymentModal.show();
        } else {
            form.reportValidity();
        }
    }

    window.processCardPayment = function() {
        const cardNumber = document.getElementById('cardNumber').value;
        const expiryDate = document.getElementById('expiryDate').value;
        const cvv = document.getElementById('cvv').value;

        if (!validateCardNumber(cardNumber)) {
            alert('Please enter a valid 16-digit card number');
            return;
        }
        if (!validateExpiryDate(expiryDate)) {
            alert('Please enter a valid expiry date (MM/YY)');
            return;
        }
        if (!validateCVV(cvv)) {
            alert('Please enter a valid CVV');
            return;
        }

        paymentModal.hide();
        showSuccessAndSubmit();
    }

    window.processCODPayment = function() {
        paymentModal.hide();
        showSuccessAndSubmit();
    }

    function showSuccessAndSubmit() {
        successModal.show();
        setTimeout(() => {
            document.querySelector('form').submit();
        }, 2000);
    }

    // Keep the validation functions
    window.validateCardNumber = function(number) {
        return /^[0-9]{16}$/.test(number);
    }

    window.validateExpiryDate = function(expiry) {
        if (!/^(0[1-9]|1[0-2])\/([0-9]{2})$/.test(expiry)) return false;
        const [month, year] = expiry.split('/');
        const expDate = new Date(2000 + parseInt(year), parseInt(month) - 1);
        return expDate > new Date();
    }

    window.validateCVV = function(cvv) {
        return /^[0-9]{3,4}$/.test(cvv);
    }
});
</script>