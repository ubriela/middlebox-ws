<div class="row clearfix" id="taskmanager">
		<div class="col-md-12 column" >
        
			<h3>
				Task manager
			</h3>
            <button type="button" class="btn btn-default btnback">Close</button>
            <button type="button" class="btn btn-default" id="refresh">Refresh</button>
            
            <select class="btn btn-default" id="loadtype">
                <option value="0">Pending</option>
                <option value="1">Completed</option>
                <option value="2">Expired</option>
            </select>
            <button type="button" class="btn btn-default" id="btndel">Delete</button>
			<table class="table" id="tabletask">
				<thead>
					<tr>
						<th>
							Location
						</th>
						
                        <th>
							Completed
						</th>
                        <th>
							Expired
						</th>
                        <th>
							Delete
						</th>
					</tr>
				</thead>
				<tbody id="containertask">
					
				</tbody>
			</table>
            
		</div>
        
	</div>