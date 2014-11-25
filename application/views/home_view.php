<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <!-- CSS -->
    <link href="<?php echo base_url();?>css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url();?>css/style.css"/> 
    <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>themes/1/tooltip.css" />
    <!-- End CSS -->
    <title>Welcome to iRain</title>
    <!-- JAVASCRIPT -->
    
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
    
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>
    <script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markermanager/src/markermanager.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/bootstrap.min.js"></script>
    <script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerwithlabel/src/markerwithlabel.js"></script>
    <script src="<?php echo base_url();?>js/function.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js1/plupload.full.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>themes/1/tooltip.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/notify.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/jstz-1.0.4.min.js"></script>
    <script>
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
    
    <script src="<?php echo base_url();?>js/home.js"></script>
    
    <!-- END JAVASCRIPT -->
  </head>
  <body>
  <div id="wrapper">
      <?php
        include('sidebar.php');
      ?>
    
    <div id="map-canvas"></div>
    <div class="lightbox" id="pac-input1"style="position: fixed; top: 0;left: 200px;padding: 0px;">
        <input id="pac-input" class="controls" type="text" placeholder="Search Box"/>
        <select id="type" class="form-control" style="width: 150px;display: inline;">
            <option value="0">today</option>
            <option value="1">1 day ago</option>
            <option value="2">2 day ago</option>
            <option value="3">3 day ago</option>
            <option value="5">5 day ago</option>
        </select>     
    </div>
    
    <div id="overlay">
        <?php
            $this->load->view('taskmanager');
            $this->load->view('posttask');
            $this->load->view('response');
            //$this->load->view('admin');
            $this->load->view('update');
            $this->load->view('weather');
              
        ?>
        <img src="<?php echo base_url()?>img/loading.gif" id="loading" width="50" height="50"/>
    </div>
    </div>
    <div class="hide">
        <div id="exit">
            <h3>Do you want logout?</h3>
            <button type="button" class="btn btn-default" id="yes">Yes</button>
            <button type="button" class="btn btn-default" id="no">No</button>
        </div>
    </div>
  </body>
</html>