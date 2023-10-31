/**
 * Loads anti-spam payload for Spam Destroyer plugin.
 * Incorrect execution results in form rejection as spam.
 */
document.addEventListener('DOMContentLoaded', function() {

	/**
	 * Create a custom cookie in the browser.
	 * The form will automatically submit this to the backend
	 * which will check if the cookie is valid.
	 */
	const createCookie = (name) => {
		const unix = Math.round(Date.now() / 1000); // Current time in seconds.
		const expire = new Date();
		expire.setTime(expire.getTime() + (spam_destroyer.lifetime * 1000)); // Cookie lifetime in milliseconds.
		const expires = `; expires=${expire.toUTCString()}`;
		document.cookie = `${name}=${unix}${expires}; path=/`;
	};
	const readCookie = (name) => {
		const nameEQ = `${name}=`;
		const cookies = document.cookie.split(';').map(c => c.trim());
		const foundCookie = cookies.find(c => c.indexOf(nameEQ) === 0);
		return foundCookie ? foundCookie.substring(nameEQ.length) : null;
	};
	const checkCookies = () => {
		const cookieValue = readCookie(spam_destroyer.key);
		if (!cookieValue) {
			createCookie(spam_destroyer.key);
		}
	};
	checkCookies();

	/**
	 * Replace hidden input field value with the spam key.
	 */
	try {
		document.getElementById('killer_value').value = spam_destroyer.key;
	} catch (e) {
		console.error('Could not set the killer_value:', e);
	}
});