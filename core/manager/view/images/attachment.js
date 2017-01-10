var aid = 1;

function addattachfrom() {
	var newnode = document.getElementById('attachbodyhidden').firstChild.cloneNode(true);
	document.getElementById('attachbody').appendChild(newnode);
}
function removeattachfrom() {
	document.getElementById('attachbody').childNodes.length > 1 && document.getElementById('attachbody').lastChild ? document.getElementById('attachbody').removeChild(document.getElementById('attachbody').lastChild) : 0;
}

function delAttach(id) {
	document.getElementById('attachbody').removeChild(document.getElementById('attach_' + id).parentNode.parentNode);
	document.getElementById('attachbody').innerHTML == '' && addAttach();
}

function addAttach() {
	newnode = document.getElementById('attachbodyhidden').firstChild.cloneNode(true);
	var id = aid;
	var tags;
	tags = newnode.getElementsByTagName('input');
	for(i in tags) {
		if(tags[i].name == 'attach[]') {
			tags[i].id = 'attach_' + id;
			tags[i].onchange = function() {insertAttach(id)};
			tags[i].unselectable = 'on';
		}
		if(tags[i].name == 'localid[]') {
			tags[i].value = id;
		}
	}
	tags = newnode.getElementsByTagName('span');
	for(i in tags) {
		if(tags[i].id == 'localfile[]') {
			tags[i].id = 'localfile_' + id;
		}
	}
	aid++;
	document.getElementById('attachbody').appendChild(newnode);
}

addAttach();

function insertAttach(id) {
	var path = document.getElementById('attach_' + id).value;
	var localfile = document.getElementById('attach_' + id).value.substr(document.getElementById('attach_' + id).value.replace(/\\/g, '/').lastIndexOf('/') + 1);

	if(path == '') {
		return;
	}
	document.getElementById('localfile_' + id).innerHTML = '[<a href="javascript:delAttach(' + id + ');">删除</a>] [<a href="###" onclick="insertAttachtext(' + id + ');return false;">插入</a>] [' + id + '] ' + localfile;
	document.getElementById('attach_' + id).style.display = 'none';

	addAttach();
}

function insertAttachtext(id) {
	editor.pasteHTML('[localfile=' + id + ']');
}