/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');

const $ = require('jquery');

require('bootstrap');

import AOS from 'aos';
import 'aos/dist/aos.css'; // You can also use <link> for styles

AOS.init({
  // Global settings:
  debounceDelay: 200, // the delay on debounce used while resizing window (advanced)
  delay: 100, // values from 0 to 3000, with step 50ms
  duration: 1500
});




(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-XXXXXXX-X', 'auto');
ga('send', 'pageview');