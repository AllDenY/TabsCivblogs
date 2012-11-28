function hideciv(nbre){
			document.getElementById("civ"+nbre).style.visibility="hidden";
			document.getElementById("civ"+nbre).style.display="none";
		}
function showciv(nbre){
	var div = document.getElementById("civ"+nbre);
	document.getElementById("civ"+nbre).style.visibility="visible";
	document.getElementById("civ"+nbre).style.display="block";
}
