<div class="row clearfix">
		<div class="col-md-12 column">
        
			<form role="form" id="weatherform">
            
            <fieldset><legend>iRain - Weather report</legend>
				<div class="form-group">
					 <input type="text" class="form-control" id="responsetitle" disabled="true" style="display: none;"/>
				</div>
		        <div class="form-group">
					 <label for="exampleInputPassword1">Your location</label><input type="text" class="form-control" id="locationweather"/>
				</div>
                <div class="form-group">
					 <label for="exampleInputPassword1">Time</label>
                     <select class="form-control" id="timeresponse1">
                    <option value="0">now</option>
                    <?php
                        for($i=1;$i<25;$i++){
                            echo " <option value=$i>$i".'hour ago</option>';
                        }
                    ?>
                   
                </select>   
				</div>
                <div class="form-group">
                
                    <button type="button" id="btnnone1"></button>
                    <button type="button" id="btnrain1"></button>
                    <button type="button" id="btnsnow1"></button>
                </div>
                 
                <div class="hide">
                    <div id="demo1">
                        <button type="button" class="btn btn-default" id="light1">Light</button>
                        <button type="button" class="btn btn-default" id="moderate1">Moderate</button>
                        <button type="button" class="btn btn-default" id="heavy1">Heavy</button>
                    </div>
                </div>
				
                <button type="button" class="btn btn-default btnback">Close</button>
			</fieldset>
            </form>
            
		</div>
	</div>