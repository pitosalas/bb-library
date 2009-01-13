function isLink(str) {
	return str.match(/^(https?|ftp):\/\/[^\/\.]+/);
}

function trim(str) {
	return str.replace(/^\s*|\s*$/g,"");
}

function isFunction(a) {
    return typeof a == 'function';
}

function isObject(a) {
    return (a && typeof a == 'object') || isFunction(a);
}

function isArray(a) {
    return isObject(a) && a.constructor == Array;
}

function selectAll(formId, select, clazz) {
	var form = document.getElementById(formId);
	var els = form.elements;
	for (i = 0; i < els.length; i++) {
		if (els[i]['type'] == 'checkbox' && !els[i]['disabled'] &&
			(clazz == null || els[i].className == clazz)) els[i]['checked'] = select;
	}
}

function getElementsByClass(node, searchClass, tag) {
	var classElements = new Array();
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp("(^|\\s)"+searchClass+"($|\\s)");
	for (i = 0, j = 0; i < elsLen; i++) {
		if (pattern.test(els[i].className)) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
}
