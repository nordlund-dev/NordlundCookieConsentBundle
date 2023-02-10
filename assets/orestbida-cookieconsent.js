import 'vanilla-cookieconsent/dist/cookieconsent.js'
import 'vanilla-cookieconsent/dist/cookieconsent.css';

window.nordlundCookieOnFirstAction = (user_preferences, cookie) => {
    console.log('first');
    console.log(user_preferences);
    console.log(cookie);
}

window.nordlundCookieOnAccept = (cookie) => {
    console.log('accept');
    console.log(cookie);
}

window.nordlundCookieOnChange = (cookie, changed_categories) => {
    console.log('change');
    console.log(cookie);
    console.log(changed_categories);
}
