<script type="text/javascript">
//<![CDATA[
CKEDITOR.config.customConfig ='<%adminurl%>ckeditor/config.js';
CKEDITOR.config.language = '<%lang%>';
CKEDITOR.config.filebrowserBrowseUrl ='<%adminurl%>media.php';
CKEDITOR.config.filebrowserUploadUrl ='<%adminurl%>upload.php';
CKEDITOR.config.uploadUrl ='<%adminurl%>upload.php?responseType=json';
CKEDITOR.replace('body', {skin: 'flat,<%adminurl%>ckeditor/skins/flat/'});
CKEDITOR.replace('more', {skin: 'flat,<%adminurl%>ckeditor/skins/flat/'});

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
