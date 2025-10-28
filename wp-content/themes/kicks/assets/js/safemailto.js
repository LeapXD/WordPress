/* eslint-disable no-underscore-dangle */
import {
  setCookie,
  getCookie,
} from './cookies';
import { qa } from './dom';

let recaptchaObject;
let target;

const rot13 = (str) => {
  const input = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
  const output = 'NOPQRSTUVWXYZABCDEFGHIJKLMnopqrstuvwxyzabcdefghijklm';
  const index = (x) => input.indexOf(x);
  const translate = (x) => (index(x) > -1 ? output[index(x)] : x);
  return str.split('').map(translate).join('');
};

const descramble = () => {
  const { mailto } = target.dataset;
  target.setAttribute('href', `mailto:${rot13(mailto)}`);
};

const verifyCallback = async (token) => {
  const data = new FormData();
  data.append('action', 'verify_captcha');
  data.append('token', token);
  const response = (await (await fetch('/wp-admin/admin-ajax.php', {
    method: 'POST',
    body: data,
  })).json());

  if (response.recaptcha) {
    UIkit.modal('#email_verify_modal').hide();
    setCookie('ishuman', true, 365);
    descramble();
    window.open(target.getAttribute('href'));
  }
};

const recaptchaCallback = () => {
  recaptchaObject = grecaptcha.render('email_verify', {
    sitekey: window.recaptcha_site_key,
    callback: verifyCallback,
    theme: 'light',
  });
};

function setupRecaptcha() {
  if (recaptchaObject) {
    window.grecaptcha.reset(recaptchaObject);
  } else {
    const script = document.createElement('script');
    script.onload = () => {
      grecaptcha.ready(recaptchaCallback);
    };
    script.src = 'https://www.google.com/recaptcha/api.js';
    document.getElementsByTagName('head')[0].appendChild(script);
  }
}

export default function attachEmailLinkListeners() {
  if (!window.recaptcha_site_key) {
    return;
  }

  qa('[data-mailto]').forEach((el) => {
    el.on('click', (e) => {
      target = el;
      setupRecaptcha();
      if (getCookie('ishuman')) {
        descramble();
        return;
      }
      e.preventDefault();
      UIkit.modal('#email_verify_modal').show();
    });
  });
}
