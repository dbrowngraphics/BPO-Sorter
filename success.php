<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>BPO - Success!</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>

<?php 
	$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'the Default Email';
?>
<div class="container">
	<div class="row">
		<div class="col-12">
			<div class="alert alert-success mt-5" role="alert">
				<h4>Time to wait for an Email!</h4>
				<hr >
				<p>
					An email will be sent to <strong><?= $email ?></strong> to let you know when the file has finished being processed.
				</p>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready( function() {

		var email = '<?= $email ?>',
			location = "<?= $_SESSION['location'] ?>";

		$.ajax({
			type: 'POST',
			url: "sort.php",
			dataType: 'json',
			data: {
				email : email,
				location : location
			},
			success: function() {
				console.log('Success');
			},
			error: function() {
				console.log('Error');
			}

		});

	});

</script>

</body>
</html>