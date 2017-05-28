<script type="text/javascript">
//<![CDATA[
CKEDITOR.config.customConfig ='<%adminurl%>ckeditor/config.js';
CKEDITOR.config.language = '<%lang%>';
CKEDITOR.config.filebrowserBrowseUrl ='<%adminurl%>media.php';
CKEDITOR.config.filebrowserUploadUrl ='<%adminurl%>upload.php';
CKEDITOR.config.uploadUrl ='<%adminurl%>upload.php?responseType=json';
CKEDITOR.config.fontSize_sizes += ';xx-small;x-small;small;medium;large;x-large;xx-large;';

function cke_ready(){
  var ed1, ed2;
  try {
    ed1 = CKEDITOR.replace('inputbody');
    ed2 = CKEDITOR.replace('inputmore');
  }
  catch (e) { }
  if (!ed1 || !ed2) {
    return ;
  }
  // hide preview button
  var obj_jp = document.getElementById("showPreview");
  if (obj_jp) {
      obj_jp.style.display = "none";
  }
  var elm = document.getElementById("switchbuttons");
  if (!elm) { return; }
  var i = elm.childNodes.length;
  while ( i > 0 ) {
    i--;
    var obj = elm.childNodes[i];
    if (typeof obj.onclick == 'function') {
        if (obj.onclick.toString().match( /updAllPreviews\s*\(\s*/ )) {
            obj.style.display = "none";
        }
    }
  }
}

if (typeof window.addEventListener == 'function') {
  window.addEventListener("load", cke_ready, false);
} else if (typeof window.attachEvent == 'function') {
  window.attachEvent("onload", cke_ready);
} else {
  var cke_old_onload = window.onload;
  function cke_new_onload()
  {
      if (cke_old_onload) {
        cke_old_onload();
      }
      cke_ready();
  }
  window.onload = cke_new_onload;
}

function getQuery()
{
	if(location.search.length > 1)
	{
		var get = new Object();
		var ret = location.search.substr(1).split("&");
		for(var i = 0; i < ret.length; i++)
		{
			var r = ret[i].split("=");
			get[r[0]] = r[1];
		}
		return get;
	}
	else
	{
		return false;
	}
}

var queryParam = getQuery();
var CKEditorFuncNum = queryParam["CKEditorFuncNum"];

function includeImage(collection, filename, type, width, height,CKEditorFuncNum) {
    var fullName;
	fullName = '<%MediaURL%>' + collection + '/' + filename;
    CKEDITOR.tools.callFunction(CKEditorFuncNum, fullName);
    window.close();
}
//]]>
</script>
