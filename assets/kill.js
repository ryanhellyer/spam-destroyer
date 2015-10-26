/**
 * Add/read cookie
 *
 * Based on code from
 * http://www.quirksmode.org/js/cookies.html
 */
function sdCreateCookie(name) {
	var unix = Math.round(+new Date()/1000); // Current time in seconds
	var expire = new Date(); // Current time in miliseconds
	expire.setTime(expire.getTime()+(spam_destroyer.lifetime*1000)); // Cookie lifetime in seconds * 1000 miliseconds
	var expires = "; expires="+expire.toUTCString();
	document.cookie = name+"="+unix+expires+"; path=/";
}

function sdReadCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

/**
 * Set cookie if not exists
 */
function sdCheckCookies() {
	var x = sdReadCookie(spam_destroyer.key)
	if (x) {
		// If cookie is set, do nothing
	} else {
		sdCreateCookie(spam_destroyer.key);
	}
}
sdCheckCookies();

// Replace hidden input field with key
try {
	document.getElementById('killer_value').value = spam_destroyer.key;
} catch (e) {}
