/**
 * Create a cookie with the given name.
 *
 * Based on code from
 * http://www.quirksmode.org/js/cookies.html
 *
 * @param {string} name - The name of the cookie.
 */
const sdCreateCookie = (name) => {
	const unix = Math.round(Date.now() / 1000); // Current time in seconds
	const expire = new Date();
	expire.setTime(expire.getTime() + (spam_destroyer.lifetime * 1000)); // Cookie lifetime in milliseconds
	const expires = `; expires=${expire.toUTCString()}`;
	document.cookie = `${name}=${unix}${expires}; path=/`;
};

/**
 * Read a cookie by its name.
 *
 * @param {string} name - The name of the cookie to read.
 * @return {string|null} - The cookie value or null if not found.
 */
const sdReadCookie = (name) => {
	const nameEQ = `${name}=`;
	const cookies = document.cookie.split(';').map(c => c.trim());
	const foundCookie = cookies.find(c => c.indexOf(nameEQ) === 0);
	return foundCookie ? foundCookie.substring(nameEQ.length) : null;
};

/**
 * Check if cookies are set, if not create them.
 */
const sdCheckCookies = () => {
	const cookieValue = sdReadCookie(spam_destroyer.key);
	if (!cookieValue) {
		sdCreateCookie(spam_destroyer.key);
	}
};
	
// Initialize cookies
sdCheckCookies();

// Replace hidden input field with key, if the element exists
try {
	document.getElementById('killer_value').value = spam_destroyer.key;
} catch (e) {
	console.error('Could not set the killer_value:', e);
}
