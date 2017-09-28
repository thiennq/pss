function LocalStore(localName) {
	localName = localName || "FacebookCrazyTool";
	if (!localStorage[localName]) {
		localStorage[localName] = '{}';
	}
	var __localStorage = JSON.parse(localStorage[localName]),
	__readLocal = function (key) {
		return __localStorage[key];
	},
	__writeLocal = function (key, val) {
		__localStorage[key] = val;
		localStorage[localName] = JSON.stringify(__localStorage);
	},
	__clearLocal = function (key) {
		delete __localStorage[key];
		localStorage[localName] = JSON.stringify(__localStorage);
		return __localStorage[key];
	};

	return {
		get: __readLocal,
		set: __writeLocal,
		clear: __clearLocal
	};
}
