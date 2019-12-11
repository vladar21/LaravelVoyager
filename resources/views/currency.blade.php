<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" 
     content="width=device-width, initial-scale=1"> 
    <link rel="stylesheet" 
     href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> 
    <script src= 
     "https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"> 
    </script> 
    <script src= 
     "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"> 
    </script> 
    <script src= 
     "https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"> 
    </script> 
    <title>Currency</title>
</head>
<body>
    <center> 
        <div class="container"> 
            <h1 style="color:green">Configuration</h1> 
  
            <form action="/api/latest" method="GET"> 
                <div class="form-group col-sm-3"> 
                    <label for="geeks1">To choice base currency:</label> 
                    <select class="form-control" id="basecurrencyid" name="base"> 
                    
                    <?php 
                    
                    foreach($names as $name)
                    echo '<option>'.$name.'</option>';?>
                        
                    </select> 
                    <br> 
                    <label for="geeks2">To choice currencies, that one rates you want (shift Ctrl):</label> 
                    <select multiple class="form-control" id="symbolsid" name="symbols"> 

                    <?php foreach($names as $name)
                    echo '<option>'.$name.'</option>';?>

                    </select> 
                </div> 
                <button type="submit" class="btn btn-primary">Submit</button> 
            </form> 
        </div> 
  </center> 
</body>
</html>