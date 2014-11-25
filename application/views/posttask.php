<div class="row clearfix">
		<div class="col-md-12 column">
			<form role="form" id="posttask">
            
            <fieldset>
            <legend>iRain - Post a task</legend>
				<div class="form-group" style="display: none;">
					 <label for="exampleInputEmail1">Title</label>
                     <select class="form-control" id="title">
                        <option value="Please report weather at your location">Please report weather at your location</option>
                     </select>
				</div>
				<div class="form-group">
					 <label for="exampleInputPassword1">Location</label><input type="text" disabled="true" class="form-control" id="location"/>
				</div>
                <div class="form-group" style="display: none;">
					 <label for="exampleInputPassword1">Latitude</label><input type="text" disabled="true" class="form-control" id="lat"/>
				</div>
                <div class="form-group" style="display: none;">
					 <label for="exampleInputPassword1">Longitude</label><input type="text" disabled="true" class="form-control" id="lng"/>
				</div>
                <div class="form-group">
					 <label for="exampleInputPassword1">Radius</label>
                     <select id="radius" class="form-control">
                        <option value="10000">10000 m</option>
                        <option value="5000">5000 m</option>
                        <option value="2000">2000 m</option>
                        <option value="1000">1000 m</option>
                     </select>
				</div>
				<button type="button" class="btn btn-default" id="btnposttask" disabled="true">Post task</button>
                <button type="button" class="btn btn-default btnback">Close</button>
                </fieldset>
			</form>
		</div>
	</div>