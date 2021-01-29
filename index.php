<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container mt-3">
<h1>BPO Sorter</h1>
<hr />
  <h2>Upload a PDF</h2>
  <p>To create a custom file upload, wrap a container element with a class of .custom-file around the input with type="file". Then add the .custom-file-input to the file input:</p>
  <form action="action_page.php" method="POST" enctype="multipart/form-data">
    <p>Custom file:</p>
    <div class="custom-file mb-3">
      <input type="file" class="custom-file-input" id="my_upload" name="my_upload">
      <label class="custom-file-label" for="my_upload">Choose file</label>
    </div>
    
    <hr />
    <div class="form-group mb-3">
      <label class="custom-input" for="location">Destination: (Just tell us the year & destination folder: ie. 2020/20200115) </label>
      <input type="text" name="location" class="form-control" placeholder="Accounting Department\Cigna PBM Invoices\">
    </div>

    <hr />
    <div class="form-group mb-3">
      <label class="custom-input" for="email">Email Address: (optional)</label>
      <input type="text" name="email" class="form-control" placeholder="Who should we email when the file is sorted?">
    </div>

<!--     <p>Default file:</p>
    <input type="file" id="myFile" name="filename2"> -->
  
    <div class="mt-3">
      <input type="submit" id="submit" class="btn btn-primary" value="Upload Now">
      <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
    </div>
  </form>
</div>

<script>
// Add the following code if you want the name of the file appear on select
var fileName = '';

$(".custom-file-input").on("change", function() {
  fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});


$("#submit").on('click', function(e){
  var ext = fileName.substring(fileName.length - 3, fileName.length);

  if ((ext.toLowerCase() != 'pdf') || ext.toUpperCase() != 'PDF') {
    e.preventDefault();
    alert('You must choose a PDF file to upload.');
  }

});
</script>

</body>
</html>