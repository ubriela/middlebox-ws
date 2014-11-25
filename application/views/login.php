<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <!-- CSS -->
    <link href="<?php echo base_url();?>css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url();?>css/style.css"/> 
    <!-- End CSS -->
    <title>Welcome to iRain</title>
    <!-- JAVASCRIPT -->
    
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=visualization,places"></script>
   <script type="text/javascript" src="https://www.google.com/jsapi"></script>
   <script type="text/javascript" src="<?php echo base_url();?>themes/1/tooltip.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/notify.min.js"></script>
    <script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerwithlabel/src/markerwithlabel.js"></script>
    
    <script src="<?php echo base_url();?>js/function.js"></script>
    
    <script>
        
        //google.setOnLoadCallback(drawChart);
         var baseurl = '<?php echo base_url();?>';
         
         
    </script>
    <script type="text/javascript">
      var script = '<script type="text/javascript" src="<?php echo base_url();?>js/markerclusterer';
      if (document.location.search.indexOf('compiled') !== -1) {
        script += '_compiled';
      }
      script += '.js"><' + '/script>';
      document.write(script);
    </script>
    
    <script src="<?php echo base_url();?>js/login.js"></script>
    
    <!-- END JAVASCRIPT -->
  </head>
  <body>
    <div id="map-canvas" style="width: 100%!important;"></div>
    <div class="lightbox" id="boxtop">
        
        <input id="pac-input" class="controls" type="text" placeholder="Search Box"/>
        <select id="type" class="form-control" style="width: 150px;display: inline;">
            <option value="0">today</option>
            <option value="1">1 day ago</option>
            <option value="2">2 day ago</option>
            <option value="3">3 day ago</option>
            <option value="5">5 day ago</option>
            
        </select>
        
        <button type="button" class="btn btn-default" id="showlogin">Login</button>
        <button type="button" class="btn btn-default" id="showregister">Register</button>
        
    </div>
    <div class="lightbox" id="boxbot">
        <img src="<?php echo base_url();?>img/logoFinal.png" height="70" width="70"/>
    </div>
    <div id="overlay">
        <?php
            include('loginform.php');
            include('registerform.php');
        ?>
    </div>
    
    
    
  </body>
</html>