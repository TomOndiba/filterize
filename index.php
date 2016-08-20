<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
function post ($i) {return isset($_POST[$i])?$_POST[$i]:"";}
if (!empty($_POST)) {
  if (isset($_FILES["image"]) && $_FILES["image"]["size"]) {
    if (isset($_SESSION["temp"]) && file_exists($_SESSION["temp"])) unlink($_SESSION["temp"]);
    $_SESSION["temp"] = "images/" . uniqid();
    move_uploaded_file($_FILES["image"]["tmp_name"], $_SESSION["temp"]);
    $_SESSION["type"] = substr($_FILES["image"]["type"], 6);
  }
  $image = call_user_func("imagecreatefrom" . $_SESSION["type"], $_SESSION["temp"]);
  if (post("negate")) {imagefilter($image, IMG_FILTER_NEGATE);}
  if (post("grayscale")) {imagefilter($image, IMG_FILTER_GRAYSCALE);}
  if (post("brightness")) {imagefilter($image, IMG_FILTER_BRIGHTNESS, post("brightness_level"));}
  if (post("contrast")) {imagefilter($image, IMG_FILTER_CONTRAST, post("contrast_level"));}
  if (post("colorize")) {imagefilter($image, IMG_FILTER_COLORIZE, (int)post("colorize_r"), (int)post("colorize_g"), (int)post("colorize_b"), 127 - ((empty(post("colorize_a"))?1:(float)post("colorize_a")) * 127));}
  if (post("edgedetect")) {imagefilter($image, IMG_FILTER_EDGEDETECT);}
  if (post("emboss")) {imagefilter($image, IMG_FILTER_EMBOSS);}
  if (post("gaussianblur")) {for ($i = 0; $i < (int)post("gaussianblur_rounds"); $i++) {imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);}}
  if (post("selectiveblur")) {for ($i = 0; $i < (int)post("selectiveblur_rounds"); $i++) {imagefilter($image, IMG_FILTER_SELECTIVE_BLUR);}}
  if (post("meanremoval")) {for ($i = 0; $i < (int)post("meanremoval_rounds"); $i++) {imagefilter($image, IMG_FILTER_MEAN_REMOVAL);}}
  if (post("smooth")) {imagefilter($image, IMG_FILTER_SMOOTH, (int)post("smooth_level"));}
  if (post("pixelate")) {imagefilter($image, IMG_FILTER_PIXELATE, (int)post("pixelate_blocksize"), isset($_POST["pixelate_advanced"]));}
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
          <input type="checkbox" name="negate" value="checked" <?php echo post("negate"); ?>>Negate
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <input type="checkbox" name="grayscale" value="checked" <?php echo post("grayscale"); ?>>Grayscale
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <input type="checkbox" name="emboss" value="checked" <?php echo post("emboss"); ?>>Emboss
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <input type="checkbox" name="edgedetect" value="checked" <?php echo post("edgedetect"); ?>>Edge Detect
        </div>
        <div class="col-sm-4 col-xs-12">
          <input type="checkbox" name="brightness" value="checked" <?php echo post("brightness"); ?>>Brightness
          <br>
          <input type="number" min="-255" max="255" name="brightness_level" placeholder="Level (-255 - 255)" value="<?php echo post("brightness_level"); ?>">
        </div>
        <div class="col-sm-4 col-xs-12">
          <input type="checkbox" name="contrast" value="checked" <?php echo post("contrast"); ?>>Contrast
          <br>
          <input type="number" name="contrast_level" placeholder="Level (-Nan - Nan)" value="<?php echo post("contrast_level"); ?>">
        </div>
        <div class="col-sm-4 col-xs-12">
          <input type="checkbox" name="smooth" value="checked" <?php echo post("smooth"); ?>>Smooth
          <br>
          <input type="number" min="0" name="smooth_level" placeholder="Level (0 - Nan)" value="<?php echo post("smooth_level"); ?>">
        </div>
        <div class="col-md-6 col-xs-12">
          <input type="checkbox" name="colorize" value="checked" <?php echo post("colorize"); ?>>Colorize
          <br>
          <input type="number" min="0" max="255" name="colorize_r" placeholder="Red (0 - 255)" value="<?php echo post("colorize_r"); ?>">
          <input type="number" min="0" max="255" name="colorize_g" placeholder="Green (0 - 255)" value="<?php echo post("colorize_g"); ?>">
          <br>
          <input type="number" min="0" max="255" name="colorize_b" placeholder="Blue (0 - 255)" value="<?php echo post("colorize_b"); ?>">
          <input type="number" min="0" max="1" step=0.01 name="colorize_a" placeholder="Alpha (0.0 - 1.0)" value="<?php echo post("colorize_a"); ?>">
        </div>
        <div class="col-md-6 col-xs-12">
          <input type="checkbox" name="pixelate" value="checked" <?php echo post("pixelate"); ?>>Pixelate
          <br>
          <input type="checkbox" name="pixelate_advanced" value="checked" <?php echo post("pixelate_advanced"); ?>>Advanced Pixelation
          <br>
          <input type="number" min="0" name="pixelate_blocksize" placeholder="Block Size (0 - Nan)" value="<?php echo post("pixelate_blocksize"); ?>">
        </div>
        <div class="col-sm-4 col-xs-12">
          <input type="checkbox" name="gaussianblur" value="checked" <?php echo post("gaussianblur"); ?>>Gaussian Blur
          <br>
          <input type="number" min="0" name="gaussianblur_rounds" placeholder="Rounds (0 - Nan)" value="<?php echo post("gaussianblur_rounds"); ?>">
        </div>
        <div class="col-sm-4 col-xs-12">
          <input type="checkbox" name="selectiveblur" value="checked" <?php echo post("selectiveblur"); ?>>Selective Blur
          <br>
          <input type="number" min="0" name="selectiveblur_rounds" placeholder="Rounds (0 - Nan)" value="<?php echo post("selectiveblur_rounds"); ?>">
        </div>
        <div class="col-sm-4 col-xs-12">
          <input type="checkbox" name="meanremoval" value="checked" <?php echo post("meanremoval"); ?>>Mean Removal
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
