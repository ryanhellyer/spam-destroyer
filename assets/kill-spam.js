document.addEventListener('DOMContentLoaded', function() {
    const createCookie = (name) => {
        const unix = Math.round(Date.now() / 1000); // Current time in seconds
        const expire = new Date();
        expire.setTime(expire.getTime() + (spam_destroyer.lifetime * 1000)); // Cookie lifetime in milliseconds
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

    // Initialize cookies
    checkCookies();

    // Replace hidden input field with key, if the element exists
    try {
        document.getElementById('killer_value').value = spam_destroyer.key;
    } catch (e) {
        console.error('Could not set the killer_value:', e);
    }
});