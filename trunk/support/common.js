// STYLE HANDLING 

function xhrFeedback($outcome, $message){
	if ($outcome == "success"){
		document.getElementById("xhrfeedback").innerHTML = '<span class="xhr_success">' + $message + '</span>';
	}else{
		document.getElementById("xhrfeedback").innerHTML = '<span class="xhr_failure">' + $message + '</span>';		
	}
}

function delStyle($action, $styleid){
  url = $action;

  url = url + '?do=delStyle&styleid='+$styleid;
  //alert("delstyle " + url);
  http.open("GET", url, true);
  http.onreadystatechange = handleHttpResponseStyleDel;

  http.send(null);
}

function handleHttpResponseStyleDel() {
  if (http.readyState == 4) {
  	if (http.responseText.indexOf('success') != -1){
  		xhrFeedback('success', 'Stilen ble slettet');
		document.getElementById("styleName").value = "";
		document.getElementById("styleURL").value = "";
		fetchStyles('modules/styleService.php');
	}else{
		xhrFeedback('failure', 'Stilen ble ikke slettet.');
	}
  }
}


function deleteStyleList($arr){
	//alert("styles.. " + $arr[0]);
	var tbl = document.getElementById("deleteStyleList");
	var cont = "<br/>";
	var i = 1;
	while ($arr[i] != null){
		if ($arr[i+2] == '1'){
			cont = cont + $arr[i] + ' <a href="javascript:delStyle(' + "'" + 'modules/styleService.php' + "'" + ',' + $arr[i+1] + ')">Slett</a><br/>';
		}
		i = i + 3;
	}
	tbl.innerHTML = cont;
	//alert(cont);
}


function fetchStyles($action){
  url = $action;
  url = url + '?do=fetch';
  http.open("GET", url, true);
  http.onreadystatechange = handleHttpResponseFetch;
  http.send(null);
}


function addUserStyle($action) {
  url = $action;
  url = url + '?do=add&styleURL=' + document.getElementById("styleURL").value + '&styleName=' + document.getElementById("styleName").value;
  http.open("GET", url, true);
  http.onreadystatechange = handleHttpResponseStyleAdd;
  http.send(null);
 }


function handleHttpResponseStyleAdd() {
  if (http.readyState == 4) {
  	if (http.responseText.indexOf('success') != -1){
		document.getElementById("xhrfeedback").innerHTML = '<span class="xhr_success">Stilen (i form av en URL til den) ble lagret.</span>';
		document.getElementById("styleName").value = "";
		document.getElementById("styleURL").value = "";
		fetchStyles('modules/styleService.php');
	}else{
		document.getElementById("xhrfeedback").innerHTML = '<span class="xhr_failure">Stilen (i form av en URL til den) ble ikke lagret.</span>';
	}
  }
} 
 

function changeUserStyle($action) {
  var chosen = document.getElementById("userStyles").selectedIndex;
  var selectelement = document.getElementById("userStyles");
  //document.getElementById("styleURL").value = selectelement.options[chosen].value;

  url = $action;
  url = url + '?do=setStyle&styleid=' + selectelement.options[chosen].value;
  
  http.open("GET", url, true);
  http.onreadystatechange = handleHttpResponseSetStyle;
  http.send(null);
 }


function handleHttpResponseSetStyle() {
  if (http.readyState == 4) {
  	if (http.responseText.indexOf('success') != -1){
  		xhrFeedback('success', 'Stilen ble endret. Last siden på nytt for å se endringene.');

		document.getElementById("styleName").value = "";
		document.getElementById("styleURL").value = "";
		fetchStyles('modules/styleService.php');
	}else{
		xhrFeedback('failure', 'Stilen ble ikke endret.');
	}
  }
}


function handleHttpResponseFetch() {

  if (http.readyState == 4) {
	//alert ("Hele greia: " + http.responseText);
	result = http.responseText.split("-");
	//alert(http.responseText);
	if (result[0] == "success"){
		deleteStyleList(result);
		i = 1;
			
		var elSel = document.getElementById('userStyles');

		var j;
		for (j = elSel.length - 1; j>=0; j--) {		
			elSel.remove(j);		
		}


		var elOpt1 = document.createElement('option');
		var elOpt2 = document.createElement('option');
		elOpt1.text = "Velg stil";
		elOpt1.value = "NULL";
		elOpt2.text = "Bruk standard";
		elOpt2.value = "NULL";
				
		try {
		 //alert ("adder.. " + elOptNew.text + " fra " + result[i] );
			elSel.add(elOpt1, null); // standards compliant; doesn't work in IE
			elSel.add(elOpt2, null);
		}
		catch(ex) {
		  	elSel.add(elOpt1); // IE only
		  	elSel.add(elOpt2); // IE only
		}			
		
		while (result[i] != null){
			//alert(result[i] + " og " + result[i+1]);
			
			var elSel = document.getElementById('userStyles');
		    var elOptNew = document.createElement('option');
		    elOptNew.text = result[i];
		    elOptNew.value = result[i+1];
 
		    try {
		    //alert ("adder.. " + elOptNew.text + " fra " + result[i] );
		      elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
		    }
		    catch(ex) {
		      elSel.add(elOptNew); // IE only
		    }	
			i=i+3;
		}	
	}
 } 
}

function handleHttpResponse() {

  if (http.readyState == 4) {
	document.getElementById("testediv").innerHTML = http.responseText;
	fetchStyles('modules/styleService.php');
  }

}

function getHTTPObject() {
  var xmlhttp;
  /*@cc_on
  @if (@_jscript_version >= 5)
    try {
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (E) {
        xmlhttp = false;
      }
    }
  @else
  xmlhttp = false;
  @end @*/
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
    try {
      xmlhttp = new XMLHttpRequest();
    } catch (e) {
      xmlhttp = false;
    }
  }
  return xmlhttp;

}
var http = getHTTPObject(); // We create the HTTP Object













// GENERAL JAVASCRIPT
		
		function viewLogin(){
			scroll(0,0);
			showDiv("loginform", "errorandlogout");
			document.form_login.username.focus();

		}

		// Javascript for scrolling a window to a specified height
		function openWin($string){
		 	x = document.documentElement.scrollTop;
 			x += '';
			window.location.href = $string + '&scroll=' + x;	
		}

		// Show a specified div block and hide another
		function showDiv(showid, hideid){
			document.getElementById(showid).style.visibility = 'visible';
			if (document.getElementById(hideid) != null)
				document.getElementById(hideid).style.visibility = 'hidden';	
			document.form_login.username.focus();
		}

		// Used for limiting the amount of input in the flash forum
		function limitText(limitField, limitCount, limitNum) {
			if (limitField.value.length > limitNum) {
				limitField.value = limitField.value.substring(0, limitNum);
			} else {
				limitCount.value = limitNum - limitField.value.length;
			}
		}

		// Removes all the text in the flashforum input box when the
		// user clicks in it.
		function wipeOut(string) {
			if(string != 'Maks 180 tegn' && string != 'Ditt navn')
				return string;
			var tstring = "";
			return tstring;
		}	

		// Remove all the spaces in a string and return it
		function removeSpaces(string) {
			var tstring = "";
			string = '' + string;
			splitstring = string.split(" ");
			for(i = 0; i < splitstring.length; i++)
				tstring += splitstring[i];
			return tstring;
		}
