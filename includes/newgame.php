<div class="alert alert-success collapse" role="alert" id="saveGameSuccess">
	La partie a été enregistrée avec <a href="#" class="alert-link">succès</a>.
</div>
<div class="alert alert-danger collapse" role="alert" id="saveGameFail">

</div>
<form role="form" id="newGameForm" onsubmit="return false" onreset="resetTable()">
	<div id="processBox">
        <span id="resultValidity" class="glyphicon"></span>
        <button id="CalculateScore" class="btn btn-primary" onclick="computeScores()" aria-label="Left Align">
          <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Calculer les résultats
        </button>
        <button id="saveToDb" class="btn btn-success" onclick="saveEvent()" aria-label="Left Align">
          <span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Enregistrer
        </button>
        <input id="resetNewGameTable" value="Reset" type="reset" class="btn btn-default" aria-label="Left Align"/>
    </div>
    <table id="newGameTable" class="table table-bordered">
		<thead>
			<tr>
				<th>#</th>
				<th>Joueur</th>
				<th>Score<span id="scoreAnomaly"></span></th>
				<th>UMA</th>
				<th>Résultat</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="rankCell">?</td>
				<td><select class="userSelect" style="width: 100%" required ></select></td>
				<td><input type="number" class="form-control" placeholder="30000" step="100" required /></td>
				<td class="umaCell"></td>
				<td class="resultCell"></td>
			</tr>
			<tr>
				<td class="rankCell">?</td>
				<td><select class="userSelect" style="width: 100%" required></select></td>
				<td><input type="number" class="form-control" placeholder="30000" step="100" required/></td>
				<td class="umaCell"></td>
				<td class="resultCell"></td>
			</tr>
			<tr>
				<td class="rankCell">?</td>
				<td><select class="userSelect" style="width: 100%" required></select></td>
				<td><input type="number" class="form-control" placeholder="30000" step="100" required/></td>
				<td class="umaCell"></td>
				<td class="resultCell"></td>
			</tr>
			<tr>
				<td class="rankCell">?</td>
				<td><select class="userSelect" style="width: 100%" required></select></td>
				<td><input type="number" class="form-control" placeholder="30000" step="100" required/></td>
				<td class="umaCell"></td>
				<td class="resultCell"></td>
			</tr>
			<tr id="addFifthPlayerRow">
				<td>
					<button id="addPlayerButton" type="button" class="btn btn-success" aria-label="Add Fifth Player Button">
						<span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span>
					</button>
                    <button id="removePlayerButton" type="button" class="btn btn-danger" aria-label="Add Fifth Player Button">
						<span class="glyphicon glyphicon glyphicon-minus" aria-hidden="true"></span>
					</button>
				</td>
        <td>
          <div class="checkbox">
            <label for="demiHanChanCheckBox">
                <input type="checkbox" id="demiHanChanCheckBox"> Demi Han Chan
            </label>
          </div>
        </td>
			</tr>
		</tbody>
	</table>
</form>
<div id="parametersBox" class="panel panel-default">
  <div class="panel-heading">
    <a data-toggle="collapse" data-parent="#parametersBox" href="#parametersCollapse">Paramètres</a>
  </div>
  <div id="parametersCollapse" class="panel-collapse collapse">
    <div class="panel-body">
      <div>
        <label for="turnamentSelect">Tournoi : </label>
        <select id="turnamentSelect">
          <option value="0">Championnat du club</option>
        </select>
      </div>
      <hr/>
      <div id="initialStackInput" class="input-group">
        <span class="input-group-addon" id="basic-addon1">Stack initial</span>
        <input type="text" class="form-control" aria-describedby="basic-addon1"/>
        <span class="input-group-addon" id="basic-addon2">pts</span>
      </div>
      <div class="input-group">
          <span class="input-group-addon" id="basic-addon4">Date</span>
          <input type="text" id="newGameDate" class="form-control" aria-describedby="basic-addon4" placeholder="AAAA-MM-DD" />
      </div>
    </div>
  </div>
</div>    
