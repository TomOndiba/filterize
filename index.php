<?php
session_start();
if (!empty($_POST)) {
  if (isset($_FILES["image"]) && $_FILES["image"]["size"]) {
    move_uploaded_file($_FILES["image"]["tmp_name"], "images/image.unk");
    $_SESSION["temp"] = "images/image.unk";
    $_SESSION["type"] = substr($_FILES["image"]["type"], 6);
  }
  $image = call_user_func("imagecreatefrom" . $_SESSION["type"], $_SESSION["temp"]);
  if ($_POST["negate"]) {imagefilter($image, IMG_FILTER_NEGATE);}
  if ($_POST["grayscale"]) {imagefilter($image, IMG_FILTER_GRAYSCALE);}
  if ($_POST["brightness"]) {imagefilter($image, IMG_FILTER_BRIGHTNESS, $_POST["brightness_level"]);}
  if ($_POST["contrast"]) {imagefilter($image, IMG_FILTER_CONTRAST, $_POST["contrast_level"]);}
  if ($_POST["colorize"]) {imagefilter($image, IMG_FILTER_COLORIZE, (int)$_POST["colorize_r"], (int)$_POST["colorize_g"], (int)$_POST["colorize_b"], 127 - ((empty($_POST["colorize_a"])?1:(float)$_POST["colorize_a"]) * 127));}
  if ($_POST["edgedetect"]) {imagefilter($image, IMG_FILTER_EDGEDETECT);}
  if ($_POST["emboss"]) {imagefilter($image, IMG_FILTER_EMBOSS);}
  if ($_POST["gaussianblur"]) {for ($i = 0; $i < (int)$_POST["gaussianblur_rounds"]; $i++) {imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);}}
  if ($_POST["selectiveblur"]) {for ($i = 0; $i < (int)$_POST["selectiveblur_rounds"]; $i++) {imagefilter($image, IMG_FILTER_SELECTIVE_BLUR);}}
  if ($_POST["meanremoval"]) {for ($i = 0; $i < (int)$_POST["meanremoval_rounds"]; $i++) {imagefilter($image, IMG_FILTER_MEAN_REMOVAL);}}
  if ($_POST["smooth"]) {imagefilter($image, IMG_FILTER_SMOOTH, (int)$_POST["smooth_level"]);}
  if ($_POST["pixelate"]) {imagefilter($image, IMG_FILTER_PIXELATE, (int)$_POST["pixelate_blocksize"], isset($_POST["pixelate_advanced"]));}
  ob_start();
  call_user_func("image" . $_SESSION["type"], $image);
  $image_data = "data:image/" . $_SESSION["type"] . ";base64," . base64_encode(ob_get_contents());
  ob_end_clean();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Filterize</title>
    <style media="screen">
    html, body {
      width: 100%;
      margin: 0;
    }
    * {
      box-sizing: border-box;
    }
    body {
      padding-bottom: 20px;
      padding-left: 20px;
      padding-right: 20px;
      margin: 0 auto;
      max-width: 50em;
      font-family: "Helvetica", "Arial", sans-serif;
      line-height: 1.5;
      color: #555;
    }
    [class*="col-"] {
      float: left;
      padding: 15px;
      border: 1px solid #999;
      text-align: center;
    }
    form {
      outline: 1px solid #999;
    }
    .row::after {
      content: "";
      clear: both;
      display: block;
    }
    .text-center {
      text-align: center;
    }
    h1, h2, strong {
      color: #333;
      margin: 20px 0 20px 0;
    }
    input[type="file"] {
      outline: 1px solid #999;
      cursor: pointer;
    }
    input[type=number] {
      -moz-appearance:textfield;
    }
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    img {
      width: 100%;
      height: auto;
      margin-bottom: -6.5px;
    }
    .col-xs-3 {width: 25%;}
    .col-xs-4 {width: 33.333333%;}
    .col-xs-6 {width: 50%;}
    .col-xs-12 {width: 100%;}
    @media (min-width: 544px) {
      .col-sm-3 {width: 25%;}
      .col-sm-4 {width: 33.333333%;}
      .col-sm-6 {width: 50%;}
      .col-sm-12 {width: 100%;}
    }
    @media (min-width: 768px) {
      .col-md-3 {width: 25%;}
      .col-md-4 {width: 33.333333%;}
      .col-md-6 {width: 50%;}
      .col-md-12 {width: 100%;}
    }
    </style>
  </head>
  <body>
    <h2 class="text-center">Filterize - 10k Apart</h2>
    <form method="post" enctype="multipart/form-data">
      <div class="row">
        <div class="col-xs-12">
          <?php if (isset($_SESSION["temp"])): ?>
          <span>To use previous image, do not specify</span>
          <?php else: ?>
          <span>Specify an image file to filterize</span>
          <?php endif; ?>
          <br>
          <input type="file" name="image" accept="image/*" <?php echo isset($_SESSION["temp"])?"":"required"; ?>>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <input type="checkbox" name="negate" value="checked" <?php echo $_POST["negate"]; ?>>Negate
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <input type="checkbox" name="grayscale" value="checked" <?php echo $_POST["grayscale"]; ?>>Grayscale
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <input type="checkbox" name="emboss" value="checked" <?php echo $_POST["emboss"]; ?>>Emboss
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <input type="checkbox" name="edgedetect" value="checked" <?php echo $_POST["edgedetect"]; ?>>Edge Detect
        </div>
        <div class="col-sm-4 col-xs-12">
          <input type="checkbox" name="brightness" value="checked" <?php echo $_POST["brightness"]; ?>>Brightness
          <br>
          <input type="number" min="-255" min="255" name="brightness_level" placeholder="Level (-255 - 255)" value="<?php echo $_POST["brightness_level"]; ?>">
        </div>
        <div class="col-sm-4 col-xs-12">
          <input type="checkbox" name="contrast" value="checked" <?php echo $_POST["contrast"]; ?>>Contrast
          <br>
          <input type="number" name="contrast_level" placeholder="Level (-Nan - Nan)" value="<?php echo $_POST["contrast_level"]; ?>">
        </div>
        <div class="col-sm-4 col-xs-12">
          <input type="checkbox" name="smooth" value="checked" <?php echo $_POST["smooth"]; ?>>Smooth
          <br>
          <input type="number" min="0" name="smooth_level" placeholder="Level (0 - Nan)" value="<?php echo $_POST["smooth_level"]; ?>">
        </div>
        <div class="col-md-6 col-xs-12">
          <input type="checkbox" name="colorize" value="checked" <?php echo $_POST["colorize"]; ?>>Colorize
          <br>
          <input type="number" min="0" max="255" name="colorize_r" placeholder="Red (0 - 255)" value="<?php echo $_POST["colorize_r"]; ?>">
          <input type="number" min="0" max="255" name="colorize_g" placeholder="Green (0 - 255)" value="<?php echo $_POST["colorize_g"]; ?>">
          <br>
          <input type="number" min="0" max="255" name="colorize_b" placeholder="Blue (0 - 255)" value="<?php echo $_POST["colorize_b"]; ?>">
          <input type="number" min="0" max="1" step=0.01 name="colorize_a" placeholder="Alpha (0.0 - 1.0)" value="<?php echo $_POST["colorize_a"]; ?>">
        </div>
        <div class="col-md-6 col-xs-12">
          <input type="checkbox" name="pixelate" value="checked" <?php echo $_POST["pixelate"]; ?>>Pixelate
          <br>
          <input type="checkbox" name="pixelate_advanced" value="checked" <?php echo $_POST["pixelate_advanced"]; ?>>Advanced Pixelation
          <br>
          <input type="number" min="0" name="pixelate_blocksize" placeholder="Block Size (0 - Nan)" value="<?php echo $_POST["pixelate_blocksize"]; ?>">
        </div>
        <div class="col-sm-4 col-xs-12">
          <input type="checkbox" name="gaussianblur" value="checked" <?php echo $_POST["gaussianblur"]; ?>>Gaussian Blur
          <br>
          <input type="number" min="0" name="gaussianblur_rounds" placeholder="Rounds (0 - Nan)" value="<?php echo $_POST["gaussianblur_rounds"]; ?>">
        </div>
        <div class="col-sm-4 col-xs-12">
          <input type="checkbox" name="selectiveblur" value="checked" <?php echo $_POST["selectiveblur"]; ?>>Selective Blur
          <br>
          <input type="number" min="0" name="selectiveblur_rounds" placeholder="Rounds (0 - Nan)" value="<?php echo $_POST["selectiveblur_rounds"]; ?>">
        </div>
        <div class="col-sm-4 col-xs-12">
          <input type="checkbox" name="meanremoval" value="checked" <?php echo $_POST["meanremoval"]; ?>>Mean Removal
          <br>
          <input type="number" min="0" name="meanremoval_rounds" placeholder="Rounds (0 - Nan)" value="<?php echo $_POST["meanremoval_rounds"]; ?>">
        </div>
        <div class="col-xs-12">
          <button type="submit"><span>Filterize</span></button>
        </div>
        <?php if (isset($image_data)): ?>
        <div class="col-xs-12">
          <img src="<?php echo $image_data; ?>" alt="Filtered Image" />
        </div>
        <?php endif; ?>
      </div>
    </form>
  </body>
</html>
